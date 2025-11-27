<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\KategoriSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    // LIST SURAT MASUK
    public function indexMasuk(Request $request)
    {
        $query = Surat::with('kategori')
            ->where('tipe', 'masuk')
            ->orderByDesc('tanggal_surat');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_surat', 'like', "%$q%")
                    ->orWhere('perihal', 'like', "%$q%")
                    ->orWhere('asal_surat', 'like', "%$q%");
            });
        }

        $surat = $query->paginate(10)->withQueryString();
        $kategori = KategoriSurat::orderBy('nama')->get();

        return view('surat.index-masuk', compact('surat', 'kategori'));
    }

    // LIST SURAT KELUAR
    public function indexKeluar(Request $request)
    {
        $query = Surat::with('kategori')
            ->where('tipe', 'keluar')
            ->orderByDesc('tanggal_surat');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_surat', 'like', "%$q%")
                    ->orWhere('perihal', 'like', "%$q%")
                    ->orWhere('tujuan_surat', 'like', "%$q%");
            });
        }

        $surat = $query->paginate(10)->withQueryString();
        $kategori = KategoriSurat::orderBy('nama')->get();

        return view('surat.index-keluar', compact('surat', 'kategori'));
    }

    // FORM UPLOAD
    public function create()
    {
        $kategori = KategoriSurat::orderBy('nama')->get();
        return view('surat.create', compact('kategori'));
    }

    // SIMPAN SURAT BARU
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
            'file'            => 'required|file|mimes:pdf,doc,docx|max:5120', // max ±5MB
        ]);

        // Validasi tambahan: jika tipe masuk, tanggal_terima sebaiknya diisi
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
            'tanggal_terima'  => $data['tipe'] === 'masuk' ? $data['tanggal_terima'] : null,
            'tanggal_keluar'  => $data['tipe'] === 'keluar' ? $data['tanggal_keluar'] : null,
            'asal_surat'      => $data['tipe'] === 'masuk' ? $data['asal_surat'] : null,
            'tujuan_surat'    => $data['tipe'] === 'keluar' ? $data['tujuan_surat'] : null,
            'perihal'         => $data['perihal'],
            'ringkasan'       => $data['ringkasan'] ?? null,
            'penandatangan'   => $data['penandatangan'] ?? null,
            'tingkat_penting' => $data['tingkat_penting'],
            'file_path'       => $path,
            'created_by'      => Auth::id(),
            'updated_by'      => null,
        ]);

        return redirect()
            ->route($surat->tipe === 'masuk' ? 'surat.masuk.index' : 'surat.keluar.index')
            ->with('success', 'Surat berhasil disimpan.');
    }

    // DETAIL SURAT
    public function show(Surat $surat)
    {
        $surat->load(['kategori', 'creator', 'updater']);
        return view('surat.show', compact('surat'));
    }

    // FORM EDIT
    public function edit(Surat $surat)
    {
        $kategori = KategoriSurat::orderBy('nama')->get();
        return view('surat.edit', compact('surat', 'kategori'));
    }

    // UPDATE DATA SURAT
    public function update(Request $request, Surat $surat)
    {
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
        ]);

        // Tipe surat tidak diubah dari sini (lebih aman)
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

        // Jika ada file baru
        if ($request->hasFile('file')) {
            // hapus file lama
            if ($surat->file_path && Storage::disk('public')->exists($surat->file_path)) {
                Storage::disk('public')->delete($surat->file_path);
            }
            $path = $request->file('file')->store('surat', 'public');
            $surat->file_path = $path;
        }

        $surat->kategori_id     = $data['kategori_id'];
        $surat->no_surat        = $data['no_surat'];
        $surat->tanggal_surat   = $data['tanggal_surat'];
        $surat->tanggal_terima  = $surat->tipe === 'masuk' ? $data['tanggal_terima'] : null;
        $surat->tanggal_keluar  = $surat->tipe === 'keluar' ? $data['tanggal_keluar'] : null;
        $surat->asal_surat      = $surat->tipe === 'masuk' ? $data['asal_surat'] : null;
        $surat->tujuan_surat    = $surat->tipe === 'keluar' ? $data['tujuan_surat'] : null;
        $surat->perihal         = $data['perihal'];
        $surat->ringkasan       = $data['ringkasan'] ?? null;
        $surat->penandatangan   = $data['penandatangan'] ?? null;
        $surat->tingkat_penting = $data['tingkat_penting'];
        $surat->updated_by      = Auth::id();

        $surat->save();

        return redirect()
            ->route($surat->tipe === 'masuk' ? 'surat.masuk.index' : 'surat.keluar.index')
            ->with('success', 'Surat berhasil diperbarui.');
    }

    // HAPUS SURAT
    public function destroy(Surat $surat)
    {
        // Hapus file fisik
        if ($surat->file_path && Storage::disk('public')->exists($surat->file_path)) {
            Storage::disk('public')->delete($surat->file_path);
        }

        $tipe = $surat->tipe;
        $surat->delete();

        return redirect()
            ->route($tipe === 'masuk' ? 'surat.masuk.index' : 'surat.keluar.index')
            ->with('success', 'Surat berhasil dihapus.');
    }

    // ====== HELPER: GENERATE KODE ARSIP ======
    protected function generateKodeArsip(string $tipe): string
    {
        // Prefix per tipe
        $prefix = $tipe === 'masuk' ? 'SM' : 'SK';
        $tahun  = now()->year;

        // Pola dasar, misal "SM-2025-"
        $pattern = $prefix . '-' . $tahun . '-';

        // Ambil kode_arsip terbesar yang cocok dengan prefix + tahun
        $lastKode = Surat::where('kode_arsip', 'like', $pattern . '%')
            ->orderByDesc('kode_arsip')
            ->value('kode_arsip');

        if ($lastKode) {
            // Potong bagian belakang (nomor 4 digit)
            $lastNo = (int) substr($lastKode, strlen($pattern));
        } else {
            $lastNo = 0;
        }

        $urut = $lastNo + 1;

        // Hasil contoh: SM-2025-0003
        return sprintf('%s-%s-%04d', $prefix, $tahun, $urut);
    }
}
