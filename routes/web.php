<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\AdminUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Models\Surat;
use App\Models\KategoriSurat;
use App\Models\User;

Route::get('/', function () {
    return redirect()->route('login');
});

// ========== SEMUA YANG SUDAH LOGIN ==========
Route::middleware('auth')->group(function () {

    // ================== DASHBOARD ==================
    Route::get('/dashboard', function () {

        $user = Auth::user();

        // ---- base query tergantung role (admin lihat semua, staf hanya yang ditujukan ke dia) ----
        if ($user->role === 'admin') {
            $baseQuery = Surat::query();
        } else {
            $baseQuery = Surat::whereHas('penerima', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        // ---- total masuk / keluar ----
        $totalMasuk  = (clone $baseQuery)->where('tipe', 'masuk')->count();
        $totalKeluar = (clone $baseQuery)->where('tipe', 'keluar')->count();
        $totalSemua  = $totalMasuk + $totalKeluar;

        // ---- ringkasan per kategori ----
        $kategoriSummary = KategoriSurat::orderBy('nama')->get()
            ->map(function ($k) use ($baseQuery) {
                $k->total = (clone $baseQuery)
                    ->where('kategori_id', $k->id)
                    ->count();
                return $k;
            })
            ->filter(fn($k) => $k->total > 0)
            ->values();

        // ---- ringkasan per user (khusus admin) ----
        $userSummary = null;
        if ($user->role === 'admin') {
            $userSummary = User::where('role', '!=', 'admin')
                ->orderBy('name')
                ->get()
                ->map(function ($u) {
                    $u->total_surat = $u->suratDiterima()->count();
                    return $u;
                })
                ->filter(fn($u) => $u->total_surat > 0)
                ->values();
        }

        // ---- ringkasan per tahun ----
        $summaryTahun = (clone $baseQuery)
            ->whereNotNull('tanggal_surat')
            ->selectRaw('YEAR(tanggal_surat) as tahun, COUNT(*) as total')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->get();

        // ---- ringkasan per bulan (untuk tahun terbaru yang punya surat) ----
        $summaryBulan = collect();
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $tahunTerbaru = $summaryTahun->first()->tahun ?? null;
        if ($tahunTerbaru) {
            $summaryBulan = (clone $baseQuery)
                ->whereYear('tanggal_surat', $tahunTerbaru)
                ->whereNotNull('tanggal_surat')
                ->selectRaw('MONTH(tanggal_surat) as bulan, COUNT(*) as total')
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get()
                ->map(function ($row) use ($tahunTerbaru, $namaBulan) {
                    $row->tahun = $tahunTerbaru;
                    $row->nama_bulan = $namaBulan[$row->bulan] ?? $row->bulan;
                    return $row;
                });
        }

        return view('dashboard', [
            'totalMasuk'      => $totalMasuk,
            'totalKeluar'     => $totalKeluar,
            'totalSemua'      => $totalSemua,
            'kategoriSummary' => $kategoriSummary,
            'userSummary'     => $userSummary,
            'summaryTahun'    => $summaryTahun,
            'summaryBulan'    => $summaryBulan,
        ]);
    })->name('dashboard');

    // ================== MENU ADMIN ==================
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

        Route::get('/surat/upload', [SuratController::class, 'create'])->name('surat.create');
        Route::post('/surat', [SuratController::class, 'store'])->name('surat.store');
        Route::get('/surat/{surat}/edit', [SuratController::class, 'edit'])->name('surat.edit');
        Route::put('/surat/{surat}', [SuratController::class, 'update'])->name('surat.update');
        Route::delete('/surat/{surat}', [SuratController::class, 'destroy'])->name('surat.destroy');
    });

    Route::get('/surat-masuk', [SuratController::class, 'indexMasuk'])->name('surat.masuk.index');
    Route::get('/surat-keluar', [SuratController::class, 'indexKeluar'])->name('surat.keluar.index');

    Route::get('/surat/{surat}', [SuratController::class, 'show'])->name('surat.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
