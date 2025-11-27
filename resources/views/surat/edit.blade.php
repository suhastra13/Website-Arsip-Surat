<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Surat ({{ strtoupper($surat->tipe) }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                    <div class="mb-4 text-red-400">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('surat.update', $surat) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block font-semibold">Tipe Surat</label>
                            <input type="text" class="border rounded w-full p-2 bg-gray-200 dark:bg-gray-700"
                                value="{{ strtoupper($surat->tipe) }}" disabled>
                        </div>

                        <div>
                            <label class="block font-semibold">Kategori</label>
                            <select name="kategori_id" class="border rounded w-full p-2 text-black" required>
                                @foreach ($kategori as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_id', $surat->kategori_id) == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold">Nomor Surat</label>
                                <input type="text" name="no_surat" class="border rounded w-full p-2 text-black"
                                    value="{{ old('no_surat', $surat->no_surat) }}" required>
                            </div>
                            <div>
                                <label class="block font-semibold">Tanggal Surat</label>
                                <input type="date" name="tanggal_surat" class="border rounded w-full p-2 text-black"
                                    value="{{ old('tanggal_surat', optional($surat->tanggal_surat)->format('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if ($surat->tipe === 'masuk')
                            <div>
                                <label class="block font-semibold">Tanggal Terima</label>
                                <input type="date" name="tanggal_terima" class="border rounded w-full p-2 text-black"
                                    value="{{ old('tanggal_terima', optional($surat->tanggal_terima)->format('Y-m-d')) }}">
                            </div>
                            <div>
                                <label class="block font-semibold">Asal Surat</label>
                                <input type="text" name="asal_surat" class="border rounded w-full p-2 text-black"
                                    value="{{ old('asal_surat', $surat->asal_surat) }}">
                            </div>
                            @else
                            <div>
                                <label class="block font-semibold">Tanggal Keluar</label>
                                <input type="date" name="tanggal_keluar" class="border rounded w-full p-2 text-black"
                                    value="{{ old('tanggal_keluar', optional($surat->tanggal_keluar)->format('Y-m-d')) }}">
                            </div>
                            <div>
                                <label class="block font-semibold">Tujuan Surat</label>
                                <input type="text" name="tujuan_surat" class="border rounded w-full p-2 text-black"
                                    value="{{ old('tujuan_surat', $surat->tujuan_surat) }}">
                            </div>
                            @endif
                        </div>

                        <div>
                            <label class="block font-semibold">Perihal</label>
                            <input type="text" name="perihal" class="border rounded w-full p-2 text-black"
                                value="{{ old('perihal', $surat->perihal) }}" required>
                        </div>

                        <div>
                            <label class="block font-semibold">Ringkasan / Keterangan</label>
                            <textarea name="ringkasan" rows="3" class="border rounded w-full p-2 text-black">{{ old('ringkasan', $surat->ringkasan) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold">Penandatangan</label>
                                <input type="text" name="penandatangan" class="border rounded w-full p-2 text-black"
                                    value="{{ old('penandatangan', $surat->penandatangan) }}">
                            </div>
                            <div>
                                <label class="block font-semibold">Tingkat Penting</label>
                                <select name="tingkat_penting" class="border rounded w-full p-2 text-black">
                                    @foreach (['biasa' => 'Biasa', 'penting' => 'Penting', 'sangat_penting' => 'Sangat Penting'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('tingkat_penting', $surat->tingkat_penting) == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold">File Surat (PDF / Word)</label>
                            @if ($surat->file_path)
                            <p class="text-sm mb-1">
                                File saat ini:
                                <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" class="text-blue-400 underline">
                                    Lihat / Download
                                </a>
                            </p>
                            @endif
                            <input type="file" name="file" class="border rounded w-full p-2 text-black">
                            <p class="text-xs text-gray-400">
                                Kosongkan jika tidak ingin mengganti file.
                            </p>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>