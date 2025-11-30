<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratController;
use Illuminate\Support\Facades\Route;
use App\Models\Surat;
use App\Models\KategoriSurat;
use App\Http\Controllers\AdminUserController;
use Illuminate\Support\Facades\Auth;




Route::get('/', function () {
    return redirect()->route('login');
});

// ========== SEMUA YANG SUDAH LOGIN ==========
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::middleware(['auth'])->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {

            $user = Auth::user(); // <-- sekarang pakai facade

            // ===== Base query tergantung role =====
            if ($user->role === 'admin') {
                // Admin lihat semua surat
                $baseQuery = Surat::query();
            } else {
                // Staf hanya surat yang ditujukan ke dia
                $baseQuery = Surat::whereHas('penerima', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            }

            // ===== Hitung surat masuk / keluar dari baseQuery =====
            $totalMasuk  = (clone $baseQuery)->where('tipe', 'masuk')->count();
            $totalKeluar = (clone $baseQuery)->where('tipe', 'keluar')->count();
            $totalSemua  = $totalMasuk + $totalKeluar;

            // ===== Ringkasan per kategori =====
            $kategoriSummary = KategoriSurat::orderBy('nama')->get();
            foreach ($kategoriSummary as $k) {
                $k->total = (clone $baseQuery)
                    ->where('kategori_id', $k->id)
                    ->count();
            }

            $kategoriLabels = $kategoriSummary->pluck('nama');
            $kategoriCounts = $kategoriSummary->pluck('total');

            // ===== Ringkasan per user (khusus admin) =====
            $userSummary = collect();
            if ($user->role === 'admin') {
                $userSummary = \App\Models\User::where('role', '!=', 'admin')
                    ->orderBy('name')
                    ->get()
                    ->map(function ($u) {
                        $u->total_surat = $u->suratDiterima()->count();
                        return $u;
                    });
            }

            return view('dashboard', compact(
                'totalMasuk',
                'totalKeluar',
                'totalSemua',
                'kategoriSummary',
                'kategoriLabels',
                'kategoriCounts',
                'userSummary'
            ));
        })->name('dashboard');
    });

    // ================== MENU ADMIN ==================
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
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
