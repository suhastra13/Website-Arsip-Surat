<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload Surat
        </h2>
    </x-slot>

    {{-- Notifikasi sukses (kalau ada) --}}
    @if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
        {{ session('success') }}
    </div>
    @endif

    {{-- Error validasi --}}
    @if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
        <div class="font-semibold mb-1">Periksa kembali isian Anda:</div>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST"
        action="{{ route('surat.store') }}"
        enctype="multipart/form-data"
        id="form-surat"
        class="space-y-6">
        @csrf

        {{-- Baris 1: Tipe & Kategori --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="tipe" class="block text-sm font-medium text-gray-700">
                    Tipe Surat <span class="text-red-500">*</span>
                </label>
                <select id="tipe" name="tipe"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    required>
                    <option value="">-- Pilih --</option>
                    <option value="masuk" {{ old('tipe') === 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                    <option value="keluar" {{ old('tipe') === 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Pilih apakah surat ini masuk ke dinas atau dikeluarkan oleh dinas.
                </p>
            </div>

            <div>
                <label for="kategori_id" class="block text-sm font-medium text-gray-700">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select id="kategori_id" name="kategori_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($kategori as $k)
                    <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama }}
                    </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Misalnya: Surat Tugas, Undangan, Laporan, Nota Dinas, dll.
                </p>
            </div>
        </div>

        {{-- Baris 2: Nomor & Tanggal Surat --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="no_surat" class="block text-sm font-medium text-gray-700">
                    Nomor Surat <span class="text-red-500">*</span>
                </label>
                <input type="text" id="no_surat" name="no_surat"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    value="{{ old('no_surat') }}"
                    placeholder="Contoh: 123/PH/KSDAE/IX/2025" required>
            </div>

            <div>
                <label for="tanggal_surat" class="block text-sm font-medium text-gray-700">
                    Tanggal Surat <span class="text-red-500">*</span>
                </label>
                <input type="date" id="tanggal_surat" name="tanggal_surat"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    value="{{ old('tanggal_surat') }}" required>
            </div>
        </div>

        {{-- Baris 3: KHUSUS MASUK vs KELUAR --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Grup Masuk --}}
            <div class="field-masuk">
                <label for="tanggal_terima" class="block text-sm font-medium text-gray-700">
                    Tanggal Terima (untuk Surat Masuk)
                </label>
                <input type="date" id="tanggal_terima" name="tanggal_terima"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    value="{{ old('tanggal_terima') }}">
                <label for="asal_surat" class="block text-sm font-medium text-gray-700 mt-3">
                    Asal Surat (untuk Surat Masuk)
                </label>
                <input type="text" id="asal_surat" name="asal_surat"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    value="{{ old('asal_surat') }}"
                    placeholder="Contoh: Balai Besar Konservasi Sumber Daya Alam">
            </div>

            {{-- Grup Keluar --}}
            <div class="field-keluar">
                <label for="tanggal_keluar" class="block text-sm font-medium text-gray-700">
                    Tanggal Keluar (untuk Surat Keluar)
                </label>
                <input type="date" id="tanggal_keluar" name="tanggal_keluar"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    value="{{ old('tanggal_keluar') }}">
                <label for="tujuan_surat" class="block text-sm font-medium text-gray-700 mt-3">
                    Tujuan Surat (untuk Surat Keluar)
                </label>
                <input type="text" id="tujuan_surat" name="tujuan_surat"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    value="{{ old('tujuan_surat') }}"
                    placeholder="Contoh: Kepala Balai Taman Nasional Kerinci Seblat">
            </div>
        </div>

        {{-- Baris 4: Perihal --}}
        <div>
            <label for="perihal" class="block text-sm font-medium text-gray-700">
                Perihal <span class="text-red-500">*</span>
            </label>
            <input type="text" id="perihal" name="perihal"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                value="{{ old('perihal') }}"
                placeholder="Contoh: Permohonan Data Kawasan Hutan Lindung">
        </div>

        {{-- Baris 5: Ringkasan --}}
        <div>
            <label for="ringkasan" class="block text-sm font-medium text-gray-700">
                Ringkasan / Keterangan
            </label>
            <textarea id="ringkasan" name="ringkasan" rows="4"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                placeholder="Tuliskan ringkasan isi surat: pokok bahasan, maksud surat, atau tindak lanjut yang diharapkan.">{{ old('ringkasan') }}</textarea>
        </div>

        {{-- Baris 6: Penandatangan & Tingkat Penting --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="penandatangan" class="block text-sm font-medium text-gray-700">
                    Penandatangan
                </label>
                <input type="text" id="penandatangan" name="penandatangan"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    value="{{ old('penandatangan') }}"
                    placeholder="Contoh: Kepala Bidang Perlindungan Hutan dan KSDAE">
            </div>

            <div>
                <label for="tingkat_penting" class="block text-sm font-medium text-gray-700">
                    Tingkat Penting <span class="text-red-500">*</span>
                </label>
                <select id="tingkat_penting" name="tingkat_penting"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    required>
                    <option value="biasa" {{ old('tingkat_penting') === 'biasa' ? 'selected' : '' }}>Biasa</option>
                    <option value="penting" {{ old('tingkat_penting') === 'penting' ? 'selected' : '' }}>Penting</option>
                    <option value="sangat_penting" {{ old('tingkat_penting') === 'sangat_penting' ? 'selected' : '' }}>Sangat Penting</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Gunakan "Sangat Penting" bila surat terkait batas waktu ketat atau risiko tinggi.
                </p>
            </div>

            {{-- PENERIMA SURAT --}}
            <div class="mt-6">
                <x-input-label for="user_ids" value="Penerima (User yang dapat mengakses surat)" />

                <p class="mt-1 text-xs text-gray-500">
                    Pilih satu atau beberapa user internal yang boleh melihat & mengunduh surat ini.
                </p>

                {{-- Checkbox "Pilih semua" --}}
                <div class="mt-3 flex items-center space-x-2">
                    <input id="select_all_users" type="checkbox"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <label for="select_all_users" class="text-sm text-gray-700">
                        Pilih semua user
                    </label>
                </div>

                {{-- Daftar user --}}
                <div
                    class="mt-2 max-h-52 overflow-y-auto border border-gray-200 rounded-xl bg-slate-50 px-3 py-3
               grid grid-cols-1 sm:grid-cols-2 gap-2">

                    @foreach ($users as $user)
                    @php
                    $checked = collect(old('user_ids', []))->contains($user->id);
                    @endphp

                    <label class="flex items-center space-x-2 text-sm text-gray-700">
                        <input type="checkbox"
                            name="user_ids[]"
                            value="{{ $user->id }}"
                            class="user-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                            {{ $checked ? 'checked' : '' }}>
                        <span>
                            {{ $user->name }}
                            <span class="text-xs text-gray-500">({{ $user->email }})</span>
                        </span>
                    </label>
                    @endforeach
                </div>

                <x-input-error :messages="$errors->get('user_ids')" class="mt-2" />
            </div>

        </div>

        {{-- Baris 7: File --}}
        <div>
            <label for="file" class="block text-sm font-medium text-gray-700">
                File Surat (PDF / Word) <span class="text-red-500">*</span>
            </label>
            <input type="file" id="file" name="file"
                class="mt-1 block w-full text-sm text-gray-700
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-md file:border-0
                          file:text-sm file:font-semibold
                          file:bg-blue-50 file:text-blue-700
                          hover:file:bg-blue-100"
                accept=".pdf,.doc,.docx" required>
            <p class="mt-1 text-xs text-gray-500">
                Maksimal 5 MB. Format yang diperbolehkan: PDF, DOC, DOCX.
            </p>
        </div>

        {{-- Tombol --}}
        <div class="pt-4">
            <button type="submit"
                class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-semibold rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Simpan
            </button>
        </div>
    </form>

    {{-- JS: show/hide field sesuai tipe surat --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipeSelect = document.getElementById('tipe');
            const grupMasuk = document.querySelectorAll('.field-masuk');
            const grupKeluar = document.querySelectorAll('.field-keluar');

            function setGroup(group, visible) {
                group.forEach(function(wrapper) {
                    if (!wrapper) return;
                    if (visible) {
                        wrapper.classList.remove('hidden');
                        wrapper.querySelectorAll('input, select, textarea').forEach(function(el) {
                            el.disabled = false;
                        });
                    } else {
                        wrapper.classList.add('hidden');
                        wrapper.querySelectorAll('input, select, textarea').forEach(function(el) {
                            el.disabled = true;
                            // optional: kosongkan nilai supaya tidak ikut tersimpan
                            // el.value = '';
                        });
                    }
                });
            }

            function updateVisibility() {
                const tipe = tipeSelect.value;
                if (tipe === 'masuk') {
                    setGroup(grupMasuk, true);
                    setGroup(grupKeluar, false);
                } else if (tipe === 'keluar') {
                    setGroup(grupMasuk, false);
                    setGroup(grupKeluar, true);
                } else {
                    // belum memilih tipe
                    setGroup(grupMasuk, false);
                    setGroup(grupKeluar, false);
                }
            }

            // inisialisasi awal (pakai old value kalau ada)
            updateVisibility();

            // update setiap kali tipe diganti
            tipeSelect.addEventListener('change', updateVisibility);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select_all_users');
            const checkboxes = document.querySelectorAll('.user-checkbox');

            if (!selectAll || checkboxes.length === 0) return;

            // Klik "Pilih semua" -> centang / uncheck semua user
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
            });

            // Kalau semua checkbox dicentang manual, otomatis "Pilih semua" ikut centang
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = Array.from(checkboxes).every(c => c.checked);
                    selectAll.checked = allChecked;
                });
            });
        });
    </script>

</x-app-layout>