@php
// Fallback kalau variabel belum dikirim (biar nggak error saat dev)
$totalMasuk = $totalMasuk ?? 0;
$totalKeluar = $totalKeluar ?? 0;
$totalSemua = $totalSemua ?? ($totalMasuk + $totalKeluar);
$kategoriSummary = $kategoriSummary ?? collect();


$chartKategoriLabels = $kategoriSummary->pluck('nama')->values();
$chartKategoriCounts = $kategoriSummary->pluck('total')->values();
@endphp


<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    {{-- ROW KARTU UTAMA --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- Surat Masuk --}}
        <div class="bg-white rounded-xl shadow-sm border border-blue-100 px-5 py-4 flex justify-between items-center hover:shadow-md hover:-translate-y-0.5 transition">
            <div>
                <p class="text-xs font-semibold tracking-wide text-blue-500 uppercase">Surat Masuk</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalMasuk }}</p>
                <p class="mt-1 text-xs text-gray-500">Total surat yang diterima</p>
            </div>
            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-lg">
                ⬇
            </div>
        </div>


        {{-- Surat Keluar --}}
        <div class="bg-white rounded-xl shadow-sm border border-emerald-100 px-5 py-4 flex justify-between items-center hover:shadow-md hover:-translate-y-0.5 transition">
            <div>
                <p class="text-xs font-semibold tracking-wide text-emerald-500 uppercase">Surat Keluar</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalKeluar }}</p>
                <p class="mt-1 text-xs text-gray-500">Total surat yang dikirim</p>
            </div>
            <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-lg">
                ⬆
            </div>
        </div>


        {{-- Total Surat --}}
        <div class="bg-white rounded-xl shadow-sm border border-indigo-100 px-5 py-4 flex justify-between items-center hover:shadow-md hover:-translate-y-0.5 transition">
            <div>
                <p class="text-xs font-semibold tracking-wide text-indigo-500 uppercase">Total Surat</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalSemua }}</p>
                <p class="mt-1 text-xs text-gray-500">
                    {{ $kategoriSummary->count() }} kategori terdaftar
                </p>
            </div>
            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-lg">
                ✉
            </div>
        </div>

    </div>

    {{-- KARTU PER KATEGORI --}}
    <div class="mb-8">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-semibold text-gray-800">
                Ringkasan per Kategori
            </h3>
            <p class="text-xs text-gray-500">
                Contoh: Undangan, Surat Edaran, Laporan, dll.
            </p>
        </div>

        @if ($kategoriSummary->isEmpty())
        <div class="bg-white rounded-xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">
            Belum ada data kategori / surat.
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($kategoriSummary as $k)
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm px-4 py-3 flex flex-col hover:border-blue-400 hover:shadow-md transition">
                <span class="text-xs font-semibold text-blue-500 uppercase tracking-wide">
                    {{ $k->nama }}
                </span>
                <span class="mt-2 text-2xl font-bold text-gray-900">
                    {{ $k->total }}
                </span>
                <span class="mt-1 text-xs text-gray-500">surat</span>
            </div>

            @endforeach

        </div>
        @endif
    </div>

    {{-- CHARTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Pie Masuk vs Keluar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">
                Perbandingan Surat Masuk & Keluar
            </h3>
            <div class="h-64">
                <canvas id="chartMasukKeluar"></canvas>
            </div>
        </div>

        {{-- Pie per Kategori --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">
                Distribusi Surat per Kategori
            </h3>
            <div class="h-64">
                <canvas id="chartKategori"></canvas>
            </div>
        </div>
    </div>

    {{-- ================== SCRIPTS (Chart.js) ================== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ====== Data dari PHP → JS (tanpa foreach / panah di JS) ======
            const totalMasuk = @json((int) $totalMasuk);
            const totalKeluar = @json((int) $totalKeluar);

            // Data kategori sudah disiapkan di blok @php di atas
            let kategoriLabels = @json($chartKategoriLabels);
            let kategoriCounts = @json($chartKategoriCounts);

            // Kalau belum ada kategori sama sekali, kasih dummy biar Chart.js tidak error
            if (!Array.isArray(kategoriLabels) || kategoriLabels.length === 0) {
                kategoriLabels = ['Belum ada data'];
                kategoriCounts = [1];
            }

            // ====== 1) Pie Masuk vs Keluar ======
            const el1 = document.getElementById('chartMasukKeluar');
            if (el1) {
                const ctx1 = el1.getContext('2d');

                new Chart(ctx1, {
                    type: 'doughnut',
                    data: {
                        labels: ['Surat Masuk', 'Surat Keluar'],
                        datasets: [{
                            data: [totalMasuk, totalKeluar],
                            backgroundColor: ['#2563eb', '#10b981'],
                            borderWidth: 1,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = (totalMasuk + totalKeluar) || 1;
                                        const value = context.parsed;
                                        const persen = ((value / total) * 100).toFixed(1);
                                        return context.label + ': ' + value + ' (' + persen + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ====== 2) Pie per Kategori ======
            const el2 = document.getElementById('chartKategori');
            if (el2) {
                const ctx2 = el2.getContext('2d');

                const baseColors = [
                    '#2563eb', '#10b981', '#f97316', '#ec4899',
                    '#8b5cf6', '#f59e0b', '#06b6d4', '#4b5563'
                ];
                const bgColors = kategoriLabels.map(function(_, i) {
                    return baseColors[i % baseColors.length];
                });

                new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        labels: kategoriLabels,
                        datasets: [{
                            data: kategoriCounts,
                            backgroundColor: bgColors,
                            borderWidth: 1,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = kategoriCounts.reduce(function(a, b) {
                                            return a + b;
                                        }, 0) || 1;
                                        const value = context.parsed;
                                        const persen = ((value / total) * 100).toFixed(1);
                                        return context.label + ': ' + value + ' (' + persen + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

</x-app-layout>