<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Surat') }}
        </h2>
    </x-slot>

    @if (session('success'))
    <div class="mb-4 text-green-700">
        {{ session('success') }}
    </div>
    @endif

    <div class="border rounded p-4 space-y-2">
        <div><strong>Tipe:</strong> {{ strtoupper($surat->tipe) }}</div>
        <div><strong>Kode Arsip:</strong> {{ $surat->kode_arsip }}</div>
        <div><strong>Nomor Surat:</strong> {{ $surat->no_surat }}</div>
        <div><strong>Kategori:</strong> {{ $surat->kategori->nama ?? '-' }}</div>
        <div><strong>Tanggal Surat:</strong> {{ $surat->tanggal_surat?->format('d-m-Y') }}</div>

        @if ($surat->tipe === 'masuk')
        <div><strong>Tanggal Terima:</strong> {{ $surat->tanggal_terima?->format('d-m-Y') }}</div>
        <div><strong>Asal Surat:</strong> {{ $surat->asal_surat }}</div>
        @else
        <div><strong>Tanggal Keluar:</strong> {{ $surat->tanggal_keluar?->format('d-m-Y') }}</div>
        <div><strong>Tujuan Surat:</strong> {{ $surat->tujuan_surat }}</div>
        @endif

        <div><strong>Perihal:</strong> {{ $surat->perihal }}</div>
        <div><strong>Ringkasan:</strong> {{ $surat->ringkasan }}</div>
        <div><strong>Penandatangan:</strong> {{ $surat->penandatangan }}</div>
        <div><strong>Tingkat Penting:</strong> {{ ucfirst(str_replace('_', ' ', $surat->tingkat_penting)) }}</div>
        {{-- Penerima Internal --}}
        <div class="mt-4">
            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                Penerima Internal
            </dt>
            <dd class="mt-1 text-sm text-slate-800">
                @if ($surat->penerima->isEmpty())
                <span class="text-slate-500">Tidak ada penerima internal yang dipilih.</span>
                @else
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($surat->penerima as $u)
                    <li>
                        {{ $u->name }}
                        <span class="text-xs text-slate-500">({{ $u->email }})</span>
                    </li>
                    @endforeach
                </ul>
                @endif
            </dd>
        </div>

        <div><strong>Dibuat oleh:</strong> {{ $surat->creator->name ?? '-' }}</div>
        <div class="pt-2">
            <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank"
                class="bg-blue-600 text-white px-4 py-2 rounded inline-block">
                Download / Buka File
            </a>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
    <div class="flex gap-2 mt-4">
        <a href="{{ route('surat.edit', $surat) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">
            Edit
        </a>

        <form action="{{ route('surat.destroy', $surat) }}" method="POST"
            onsubmit="return confirm('Yakin ingin menghapus surat ini?');">
            @csrf
            @method('DELETE')
            <button class="bg-red-600 text-white px-4 py-2 rounded">
                Hapus
            </button>
        </form>
    </div>
    @endif
    </div>
</x-app-layout>