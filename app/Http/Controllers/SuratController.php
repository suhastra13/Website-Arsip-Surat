<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use App\Models\KategoriSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    // ================== LIST SURAT MASUK ==================


    public function indexMasuk(Request $request)
    {
        $user = Auth::user();

        // ---- base query: semua surat MASUK yang boleh dia lihat ----
        $baseQuery = Surat::with(['kategori', 'penerima', 'creator'])
            ->where('tipe', 'masuk')
            ->orderByDesc('tanggal_surat');

        // kalau bukan admin: hanya yang dia buat ATAU yang ditujukan ke dia
        if ($user->role !== 'admin') {
            $userId = $user->id;

            $baseQuery->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                    ->orWhereHas('penerima', function ($qq) use ($userId) {
                        $qq->where('users.id', $userId);
                    });
            });
        }

        // ---- daftar tahun untuk filter (tanpa filter q/kategori/bulan) ----
        $tahunQuery = clone $baseQuery;
        $daftarTahun = $tahunQuery
            ->whereNotNull('tanggal_surat')
            ->selectRaw('YEAR(tanggal_surat) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        // ---- terapkan filter dari form ----
        $query = clone $baseQuery;

        if ($request->filled('year')) {
            $query->whereYear('tanggal_surat', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('tanggal_surat', $request->month);
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($sub) use ($keyword) {
                $sub->where('no_surat', 'like', "%{$keyword}%")
                    ->orWhere('perihal', 'like', "%{$keyword}%")
                    ->orWhere('asal_surat', 'like', "%{$keyword}%");
            });
        }

        $surat = $query->paginate(10)->withQueryString();

        $kategori = KategoriSurat::orderBy('nama')->get();

        $users = User::where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();

        return view('surat.index-masuk', compact(
            'surat',
            'kategori',
            'users',
            'daftarTahun'
        ));
    }


    // ================== LIST SURAT KELUAR ==================
    public function indexKeluar(Request $request)
    {
        $user  = Auth::user();

        $query = Surat::with(['kategori', 'penerima', 'creator'])
            ->where('tipe', 'keluar')
            ->orderByDesc('tanggal_surat');

        // Filter akses untuk non-admin
        if ($user->role !== 'admin') {
            $userId = $user->id;

            $query->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                    ->orWhereHas('penerima', function ($qq) use ($userId) {
                        $qq->where('users.id', $userId);
                    });
            });
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal_surat', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('tanggal_surat', $request->month);
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_surat', 'like', "%{$q}%")
                    ->orWhere('perihal', 'like', "%{$q}%")
                    ->orWhere('tujuan_surat', 'like', "%{$q}%");
            });
        }

        $surat    = $query->paginate(10)->withQueryString();
        $kategori = KategoriSurat::orderBy('nama')->get();
        $users    = User::where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();

        $tahunQuery = Surat::where('tipe', 'keluar');
        if ($user->role !== 'admin') {
            $tahunQuery->whereHas('penerima', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
        $daftarTahun = $tahunQuery
            ->whereNotNull('tanggal_surat')
            ->selectRaw('YEAR(tanggal_surat) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('surat.index-keluar', compact('surat', 'kategori', 'users', 'daftarTahun'));
    }

    // ================== FORM UPLOAD ==================
    public function create()
    {
        $kategori = KategoriSurat::orderBy('nama')->get();
        // Hanya user non-admin yang bisa dipilih sebagai penerima
        $users    = User::where('role', '!=', 'admin')->orderBy('name')->get();

        return view('surat.create', compact('kategori', 'users'));
    }

    // ================== SIMPAN SURAT BARU ==================
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipe'            => 'required|in:masuk,keluar',
            'kategori_id'     => 'required|exists:kategori_surat,id',
            'no_surat'        => 'required|string|max:255',
            'tanggal_surat'   => 'required|date',
            'tanggal_terima'  => 'nullable|date',
            'tanggal_keluar'  => 'nullable|date',
            'asal_surat'      => 'nullable|string|max:255',
            'tujuan_surat'    => 'nullable|string|max:255',
            'perihal'         => 'required|string|max:255',
            'ringkasan'       => 'nullable|string',
            'penandatangan'   => 'nullable|string|max:255',
            'tingkat_penting' => 'required|in:biasa,penting,sangat_penting',
            'file'            => 'required|file|mimes:pdf,doc,docx|max:5120', // ±5MB
            'user_ids'        => ['required', 'array', 'min:1'],
            'user_ids.*'      => ['integer', 'exists:users,id'],
        ]);

        // Validasi tambahan berdasarkan tipe
        if ($data['tipe'] === 'masuk' && empty($data['tanggal_terima'])) {
            $request->validate([
                'tanggal_terima' => 'required|date',
            ]);
        }

        if ($data['tipe'] === 'keluar' && empty($data['tanggal_keluar'])) {
            $request->validate([
                'tanggal_keluar' => 'required|date',
            ]);
        }

        // Simpan file
        $path = $request->file('file')->store('surat', 'public');

        // Generate kode arsip
        $kodeArsip = $this->generateKodeArsip($data['tipe']);

        $surat = Surat::create([
            'tipe'            => $data['tipe'],
            'kategori_id'     => $data['kategori_id'],
            'kode_arsip'      => $kodeArsip,
            'no_surat'        => $data['no_surat'],
            'tanggal_surat'   => $data['tanggal_surat'],
            'tanggal_terima'  => $data['tipe'] === 'masuk'   ? $data['tanggal_terima']  : null,
            'tanggal_keluar'  => $data['tipe'] === 'keluar'  ? $data['tanggal_keluar']  : null,
            'asal_surat'      => $data['tipe'] === 'masuk'   ? $data['asal_surat']      : null,
            'tujuan_surat'    => $data['tipe'] === 'keluar'  ? $data['tujuan_surat']    : null,
            'perihal'         => $data['perihal'],
            'ringkasan'       => $data['ringkasan']       ?? null,
            'penandatangan'   => $data['penandatangan']   ?? null,
            'tingkat_penting' => $data['tingkat_penting'],
            'file_path'       => $path,
            'created_by'      => Auth::id(),
            'updated_by'      => null,
        ]);

        // Simpan penerima (pivot)
        $surat->penerima()->sync($data['user_ids']);

        return redirect()
            ->route($surat->tipe === 'masuk' ? 'surat.masuk.index' : 'surat.keluar.index')
            ->with('success', 'Surat berhasil disimpan.');
    }

    // ================== DETAIL SURAT ==================
    public function show(Surat $surat)
    {
        $user = Auth::user();

        // Non-admin hanya boleh lihat surat yang memang dituju ke dia
        if ($user->role !== 'admin') {
            $boleh = $surat->penerima()
                ->where('users.id', $user->id)
                ->exists();

            if (! $boleh) {
                abort(403); // Forbidden
            }
        }

        $surat->load(['kategori', 'creator', 'updater', 'penerima']);

        return view('surat.show', compact('surat'));
    }


    // ================== FORM EDIT ==================
    public function edit(Surat $surat)
    {
        $this->checkAccess($surat);

        $kategori = KategoriSurat::orderBy('nama')->get();
        $users    = User::where('role', '!=', 'admin')->orderBy('name')->get();

        return view('surat.edit', compact('surat', 'kategori', 'users'));
    }

    // ================== UPDATE DATA SURAT ==================
    public function update(Request $request, Surat $surat)
    {
        $this->checkAccess($surat);

        $data = $request->validate([
            'kategori_id'     => 'required|exists:kategori_surat,id',
            'no_surat'        => 'required|string|max:255',
            'tanggal_surat'   => 'required|date',
            'tanggal_terima'  => 'nullable|date',
            'tanggal_keluar'  => 'nullable|date',
            'asal_surat'      => 'nullable|string|max:255',
            'tujuan_surat'    => 'nullable|string|max:255',
            'perihal'         => 'required|string|max:255',
            'ringkasan'       => 'nullable|string',
            'penandatangan'   => 'nullable|string|max:255',
            'tingkat_penting' => 'required|in:biasa,penting,sangat_penting',
            'file'            => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'user_ids'        => ['required', 'array', 'min:1'],
            'user_ids.*'      => ['integer', 'exists:users,id'],
        ]);

        // Validasi tambahan
        if ($surat->tipe === 'masuk' && empty($data['tanggal_terima'])) {
            $request->validate([
                'tanggal_terima' => 'required|date',
            ]);
        }

        if ($surat->tipe === 'keluar' && empty($data['tanggal_keluar'])) {
            $request->validate([
                'tanggal_keluar' => 'required|date',
            ]);
        }

        // File baru?
        if ($request->hasFile('file')) {
            if ($surat->file_path && Storage::disk('public')->exists($surat->file_path)) {
                Storage::disk('public')->delete($surat->file_path);
            }

            $path = $request->file('file')->store('surat', 'public');
            $surat->file_path = $path;
        }

        $surat->kategori_id     = $data['kategori_id'];
        $surat->no_surat        = $data['no_surat'];
        $surat->tanggal_surat   = $data['tanggal_surat'];
        $surat->tanggal_terima  = $surat->tipe === 'masuk'  ? $data['tanggal_terima'] : null;
        $surat->tanggal_keluar  = $surat->tipe === 'keluar' ? $data['tanggal_keluar'] : null;
        $surat->asal_surat      = $surat->tipe === 'masuk'  ? $data['asal_surat']     : null;
        $surat->tujuan_surat    = $surat->tipe === 'keluar' ? $data['tujuan_surat']   : null;
        $surat->perihal         = $data['perihal'];
        $surat->ringkasan       = $data['ringkasan']     ?? null;
        $surat->penandatangan   = $data['penandatangan'] ?? null;
        $surat->tingkat_penting = $data['tingkat_penting'];
        $surat->updated_by      = Auth::id();

        $surat->save();

        // Update penerima
        $surat->penerima()->sync($data['user_ids']);

        return redirect()
            ->route($surat->tipe === 'masuk' ? 'surat.masuk.index' : 'surat.keluar.index')
            ->with('success', 'Surat berhasil diperbarui.');
    }

    // ================== HAPUS SURAT ==================
    public function destroy(Surat $surat)
    {
        $this->checkAccess($surat);

        if ($surat->file_path && Storage::disk('public')->exists($surat->file_path)) {
            Storage::disk('public')->delete($surat->file_path);
        }

        $tipe = $surat->tipe;

        // Pivot akan ikut terhapus jika di migration foreign key-nya onDelete('cascade')
        // kalau mau extra aman, boleh:
        // $surat->penerima()->detach();

        $surat->delete();

        return redirect()
            ->route($tipe === 'masuk' ? 'surat.masuk.index' : 'surat.keluar.index')
            ->with('success', 'Surat berhasil dihapus.');
    }

    // ================== HELPER: CEK AKSES SURAT ==================
    protected function checkAccess(Surat $surat): void
    {
        $user = Auth::user();

        // Admin bebas akses
        if ($user->role === 'admin') {
            return;
        }

        $userId  = $user->id;
        $allowed = $surat->created_by == $userId
            || $surat->penerima()->where('users.id', $userId)->exists();

        if (! $allowed) {
            abort(403, 'Anda tidak berhak mengakses surat ini.');
        }
    }

    // ================== HELPER: GENERATE KODE ARSIP ==================
    protected function generateKodeArsip(string $tipe): string
    {
        $prefix  = $tipe === 'masuk' ? 'SM' : 'SK';
        $tahun   = now()->year;
        $pattern = $prefix . '-' . $tahun . '-';

        $lastKode = Surat::where('kode_arsip', 'like', $pattern . '%')
            ->orderByDesc('kode_arsip')
            ->value('kode_arsip');

        $lastNo = $lastKode
            ? (int) substr($lastKode, strlen($pattern))
            : 0;

        $urut = $lastNo + 1;

        return sprintf('%s-%s-%04d', $prefix, $tahun, $urut);
    }
}
