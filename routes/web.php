<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratController;
use Illuminate\Support\Facades\Route;
use App\Models\Surat;
use App\Models\KategoriSurat;


Route::get('/', function () {
    return view('welcome');
});

// ========== SEMUA YANG SUDAH LOGIN ==========
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {

            // Jumlah surat masuk & keluar
            $totalMasuk  = Surat::where('tipe', 'masuk')->count();
            $totalKeluar = Surat::where('tipe', 'keluar')->count();
            $totalSemua  = $totalMasuk + $totalKeluar;

            // Ringkasan per kategori
            $kategoriSummary = KategoriSurat::orderBy('nama')->get();
            foreach ($kategoriSummary as $k) {
                // total surat (masuk + keluar) di kategori ini
                $k->total = Surat::where('kategori_id', $k->id)->count();
            }

            // Data untuk chart
            $kategoriLabels = $kategoriSummary->pluck('nama');
            $kategoriCounts = $kategoriSummary->pluck('total');

            return view('dashboard', compact(
                'totalMasuk',
                'totalKeluar',
                'totalSemua',
                'kategoriSummary',
                'kategoriLabels',
                'kategoriCounts'
            ));
        })->name('dashboard');
    });


    // Daftar Surat Masuk & Keluar (boleh dilihat semua user login)
    Route::get('/surat-masuk', [SuratController::class, 'indexMasuk'])
        ->name('surat.masuk.index');

    Route::get('/surat-keluar', [SuratController::class, 'indexKeluar'])
        ->name('surat.keluar.index');

    // -------- ROUTE KHUSUS ADMIN DI DALAM GROUP AUTH --------
    Route::middleware('admin')->group(function () {
        // Form Upload Surat
        Route::get('/surat/upload', [SuratController::class, 'create'])
            ->name('surat.create');

        // Simpan surat baru
        Route::post('/surat', [SuratController::class, 'store'])
            ->name('surat.store');

        // Edit, update, hapus surat
        Route::get('/surat/{surat}/edit', [SuratController::class, 'edit'])
            ->name('surat.edit');

        Route::put('/surat/{surat}', [SuratController::class, 'update'])
            ->name('surat.update');

        Route::delete('/surat/{surat}', [SuratController::class, 'destroy'])
            ->name('surat.destroy');
    });

    // -------- ROUTE SHOW PALING TERAKHIR --------
    // Detail surat (boleh dilihat semua user login)
    Route::get('/surat/{surat}', [SuratController::class, 'show'])
        ->name('surat.show');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
