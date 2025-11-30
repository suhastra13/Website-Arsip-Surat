@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Surat Keluar
        </h2>
    </x-slot>

    {{-- NOTIF SUKSES --}}
    @if (session('success'))
    <div id="alert-success"
        class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 flex items-start justify-between">
        <div>
            <span class="font-semibold">Berhasil.</span>
            <span class="ml-1">{{ session('success') }}</span>
        </div>
        <button type="button"
            class="ml-4 text-green-700 hover:text-green-900"
            onclick="document.getElementById('alert-success').classList.add('hidden')">
            &times;
        </button>
    </div>
    @endif

    {{-- FILTER --}}
    <div class="mb-4 bg-blue-600 text-white rounded-lg shadow px-4 py-4">
        <form method="GET" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="q"
                placeholder="Cari no surat / perihal / tujuan"
                value="{{ request('q') }}"
                class="flex-1 min-w-[200px] rounded-md border-0 px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-blue-300">

            <select name="kategori_id"
                class="rounded-md border-0 px-3 py-2 text-sm text-gray-900">
                <option value="">Semua Kategori</option>
                @foreach ($kategori as $k)
                <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                    {{ $k->nama }}
                </option>
                @endforeach
            </select>

            <button
                class="inline-flex items-center px-3 py-2 rounded-md bg-white text-blue-700 hover:bg-blue-50 text-sm font-semibold border border-blue-100">
                Filter
            </button>
        </form>
    </div>

    {{-- TABEL SURAT KELUAR --}}
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border border-blue-100 rounded-lg overflow-hidden bg-white">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-3 py-2 text-left">No</th>
                    <th class="px-3 py-2 text-left">Kode Arsip</th>
                    <th class="px-3 py-2 text-left">No Surat</th>
                    <th class="px-3 py-2 text-left">Tanggal Surat</th>
                    <th class="px-3 py-2 text-left">Tanggal Keluar</th>
                    <th class="px-3 py-2 text-left">Tujuan Surat</th>
                    <th class="px-3 py-2 text-left">Kategori</th>
                    <th class="px-3 py-2 text-left">Perihal</th>
                    <th class="px-3 py-2 text-center w-40">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($surat as $item)
                @php
                $fileUrl = $item->file_path ? Storage::url($item->file_path) : '#';
                @endphp

                <tr class="border-t border-slate-100 hover:bg-slate-50">
                    <td class="px-3 py-2">
                        {{ $loop->iteration + ($surat->currentPage() - 1) * $surat->perPage() }}
                    </td>
                    <td class="px-3 py-2 font-mono text-xs">{{ $item->kode_arsip }}</td>
                    <td class="px-3 py-2 font-mono text-xs">{{ $item->no_surat }}</td>
                    <td class="px-3 py-2">{{ $item->tanggal_surat?->format('d-m-Y') }}</td>
                    <td class="px-3 py-2">{{ $item->tanggal_keluar?->format('d-m-Y') }}</td>
                    <td class="px-3 py-2 truncate max-w-[150px]" title="{{ $item->tujuan_surat }}">
                        {{ $item->tujuan_surat }}
                    </td>
                    <td class="px-3 py-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                            {{ $item->kategori->nama ?? '-' }}
                        </span>
                    </td>
                    <td class="px-3 py-2 truncate max-w-[180px]" title="{{ $item->perihal }}">
                        {{ $item->perihal }}
                    </td>
                    <td class="px-3 py-2 text-center">
                        {{-- Tombol Detail --}}
                        <button type="button"
                            class="btn-detail inline-flex items-center px-2 py-1 text-xs font-semibold rounded bg-blue-500 hover:bg-blue-600 text-white mb-1"
                            data-id="{{ $item->id }}"
                            data-kode="{{ $item->kode_arsip }}"
                            data-tipe="{{ strtoupper($item->tipe) }}"
                            data-no="{{ $item->no_surat }}"
                            data-kategori="{{ $item->kategori->nama ?? '-' }}"
                            data-tgl-surat="{{ optional($item->tanggal_surat)->format('d-m-Y') }}"
                            data-tgl-keluar="{{ optional($item->tanggal_keluar)->format('d-m-Y') }}"
                            data-tujuan="{{ $item->tujuan_surat }}"
                            data-perihal="{{ $item->perihal }}"
                            data-ringkasan="{{ $item->ringkasan }}"
                            data-ttd="{{ $item->penandatangan }}"
                            data-tingkat="{{ $item->tingkat_penting }}"
                            data-file-url="{{ $fileUrl }}"
                            data-dibuat="{{ $item->creator->name ?? '-' }}"
                            data-penerima='@json($item->penerima->map(fn($u) => [
        "name"  => $u->name,
        "email" => $u->email,
    ]))'>
                            Detail
                        </button>

                        @if (Auth::user()->role === 'admin')
                        {{-- Tombol Edit --}}
                        <button type="button"
                            class="btn-edit inline-flex items-center px-2 py-1 text-xs font-semibold rounded bg-amber-400 hover:bg-amber-500 text-slate-900 mb-1"
                            data-update-url="{{ route('surat.update', $item) }}"
                            data-id="{{ $item->id }}"
                            data-kategori-id="{{ $item->kategori_id }}"
                            data-no="{{ $item->no_surat }}"
                            data-tgl-surat="{{ optional($item->tanggal_surat)->format('Y-m-d') }}"
                            data-tgl-keluar="{{ optional($item->tanggal_keluar)->format('Y-m-d') }}"
                            data-tujuan="{{ $item->tujuan_surat }}"
                            data-perihal="{{ $item->perihal }}"
                            data-ringkasan="{{ $item->ringkasan }}"
                            data-ttd="{{ $item->penandatangan }}"
                            data-tingkat="{{ $item->tingkat_penting }}"
                            data-user-ids='@json($item->penerima->pluck("id"))'>
                            Edit
                        </button>

                        {{-- Tombol Hapus --}}
                        <button type="button"
                            class="btn-delete inline-flex items-center px-2 py-1 text-xs font-semibold rounded bg-red-500 hover:bg-red-600 text-white"
                            data-delete-url="{{ route('surat.destroy', $item) }}"
                            data-perihal="{{ $item->perihal }}">
                            Hapus
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-3 py-4 text-center text-gray-500">
                        Belum ada surat keluar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">
        {{ $surat->links() }}
    </div>

    {{-- ======================= MODAL DETAIL ======================= --}}
    <div id="modal-detail"
        class="fixed inset-0 bg-black/40 flex items-center justify-center z-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
            <div class="px-5 py-3 border-b border-gray-200 flex justify-between items-center bg-blue-600 text-white rounded-t-lg">
                <h3 class="font-semibold text-lg">Detail Surat Keluar</h3>
                <button type="button" class="text-white/80 hover:text-white" data-close-detail>&times;</button>
            </div>
            <div class="px-5 py-4" id="detail-body">
                {{-- diisi via JS --}}
            </div>
            <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex justify-between items-center rounded-b-lg">
                <a id="detail-file-link" href="#" target="_blank"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded bg-blue-600 hover:bg-blue-700 text-white">
                    Download / Buka File
                </a>
                <button type="button" class="px-3 py-1.5 text-xs rounded bg-white border border-gray-300 hover:bg-gray-100"
                    data-close-detail>
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ======================= MODAL EDIT (ADMIN) ======================= --}}
    @if (Auth::user()->role === 'admin')
    <div id="modal-edit"
        class="fixed inset-0 bg-black/40 flex items-center justify-center z-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
            <div class="px-5 py-3 border-b border-gray-200 flex justify-between items-center bg-blue-600 text-white rounded-t-lg">
                <h3 class="font-semibold text-lg">Edit Surat Keluar</h3>
                <button type="button" class="text-white/80 hover:text-white" data-close-edit>&times;</button>
            </div>

            <form id="form-edit" method="POST" enctype="multipart/form-data" class="px-5 py-4 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Nomor Surat</label>
                        <input type="text" name="no_surat" id="edit-no_surat"
                            class="mt-1 w-full rounded-md border-gray-300 text-sm"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Tanggal Surat</label>
                        <input type="date" name="tanggal_surat" id="edit-tanggal_surat"
                            class="mt-1 w-full rounded-md border-gray-300 text-sm"
                            required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Tanggal Keluar</label>
                        <input type="date" name="tanggal_keluar" id="edit-tanggal_keluar"
                            class="mt-1 w-full rounded-md border-gray-300 text-sm"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Kategori</label>
                        <select name="kategori_id" id="edit-kategori_id"
                            class="mt-1 w-full rounded-md border-gray-300 text-sm"
                            required>
                            @foreach ($kategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700">Tujuan Surat</label>
                    <input type="text" name="tujuan_surat" id="edit-tujuan_surat"
                        class="mt-1 w-full rounded-md border-gray-300 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700">Perihal</label>
                    <input type="text" name="perihal" id="edit-perihal"
                        class="mt-1 w-full rounded-md border-gray-300 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700">Ringkasan</label>
                    <textarea name="ringkasan" id="edit-ringkasan" rows="3"
                        class="mt-1 w-full rounded-md border-gray-300 text-sm"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Penandatangan</label>
                        <input type="text" name="penandatangan" id="edit-penandatangan"
                            class="mt-1 w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Tingkat Penting</label>
                        <select name="tingkat_penting" id="edit-tingkat_penting"
                            class="mt-1 w-full rounded-md border-gray-300 text-sm" required>
                            <option value="biasa">Biasa</option>
                            <option value="penting">Penting</option>
                            <option value="sangat_penting">Sangat Penting</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">
                        Penerima Internal
                    </label>

                    <div class="flex items-center mt-1 mb-1">
                        <input type="checkbox" id="edit-select-all-users"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="edit-select-all-users" class="ml-2 text-xs text-gray-600">
                            Pilih semua user
                        </label>
                    </div>

                    <select name="user_ids[]" id="edit-user_ids" multiple
                        class="mt-1 w-full rounded-md border-gray-300 text-sm h-32">
                        @foreach ($users as $u)
                        <option value="{{ $u->id }}">
                            {{ $u->name }} ({{ $u->email }})
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] text-gray-500">
                        Tahan Ctrl/Cmd untuk memilih lebih dari satu.
                    </p>
                </div>


                <div>
                    <label class="block text-xs font-medium text-gray-700">Ganti File (opsional)</label>
                    <input type="file" name="file"
                        class="mt-1 w-full text-sm text-gray-700
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100"
                        accept=".pdf,.doc,.docx">
                    <p class="mt-1 text-[11px] text-gray-500">
                        Biarkan kosong jika tidak ingin mengganti file.
                    </p>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-200">
                    <button type="button"
                        class="px-3 py-1.5 text-xs rounded bg-white border border-gray-300 hover:bg-gray-100"
                        data-close-edit>
                        Batal
                    </button>
                    <button type="submit"
                        class="btn-save px-4 py-1.5 text-xs font-semibold rounded bg-blue-600 hover:bg-blue-700 text-white">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- FORM DELETE GLOBAL --}}
    @if (Auth::user()->role === 'admin')
    <form id="form-delete" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endif

    {{-- ======================= JAVASCRIPT ======================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // auto-hide alert success
            const alertSuccess = document.getElementById('alert-success');
            if (alertSuccess) {
                setTimeout(() => {
                    alertSuccess.classList.add('transition', 'duration-500', 'opacity-0');
                }, 3000);
            }

            // ---------- DETAIL MODAL ----------
            const modalDetail = document.getElementById('modal-detail');
            const detailBody = document.getElementById('detail-body');
            const detailFileLink = document.getElementById('detail-file-link');

            document.querySelectorAll('[data-close-detail]').forEach(btn => {
                btn.addEventListener('click', () => modalDetail.classList.add('hidden'));
            });

            document.querySelectorAll('.btn-detail').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tingkat = btn.dataset.tingkat;
                    let tingkatLabel = 'Biasa';
                    let tingkatColor = 'bg-slate-100 text-slate-800';
                    if (tingkat === 'penting') {
                        tingkatLabel = 'Penting';
                        tingkatColor = 'bg-amber-100 text-amber-800';
                    } else if (tingkat === 'sangat_penting') {
                        tingkatLabel = 'Sangat Penting';
                        tingkatColor = 'bg-red-100 text-red-800';
                    }

                    // ===== PENERIMA INTERNAL =====
                    let penerima = [];
                    try {
                        penerima = btn.dataset.penerima ? JSON.parse(btn.dataset.penerima) : [];
                    } catch (e) {
                        penerima = [];
                    }

                    let penerimaHtml = '';
                    if (!penerima.length) {
                        penerimaHtml = '<span class="text-slate-500">Tidak ada penerima internal yang dipilih.</span>';
                    } else {
                        penerimaHtml = '<ul class="list-disc list-inside space-y-0.5">';
                        penerima.forEach(u => {
                            penerimaHtml += `<li>${u.name} <span class="text-xs text-slate-500">(${u.email})</span></li>`;
                        });
                        penerimaHtml += '</ul>';
                    }


                    detailBody.innerHTML = `
                        <dl class="space-y-2 text-sm text-gray-900">
                            <div>
                                <dt class="font-semibold text-gray-600">Tipe</dt>
                                <dd>${btn.dataset.tipe}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-gray-600">Kode Arsip</dt>
                                <dd>${btn.dataset.kode}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-gray-600">Nomor Surat</dt>
                                <dd>${btn.dataset.no}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-gray-600">Kategori</dt>
                                <dd>${btn.dataset.kategori}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-gray-600">Tanggal Surat</dt>
                                <dd>${btn.dataset.tglSurat || '-'}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-gray-600">Tanggal Keluar</dt>
                                <dd>${btn.dataset.tglKeluar || '-'}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-gray-600">Tujuan Surat</dt>
                                <dd>${btn.dataset.tujuan || '-'}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-gray-600">Perihal</dt>
                                <dd>${btn.dataset.perihal || '-'}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-gray-600">Ringkasan</dt>
                                <dd>${btn.dataset.ringkasan || '-'}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-gray-600">Penandatangan</dt>
                                <dd>${btn.dataset.ttd || '-'}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-gray-600">Tingkat Penting</dt>
                                <dd class="mt-0.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs ${tingkatColor}">
                                        ${tingkatLabel}
                                    </span>
                                </dd>
                            </div>
                             <div>
                    <dt class="font-semibold text-gray-600">Penerima Internal</dt>
                    <dd class="mt-0.5">
                        ${penerimaHtml}
                    </dd>
                </div>
                            <div>
                                <dt class="font-semibold text-gray-600">Dibuat oleh</dt>
                                <dd>${btn.dataset.dibuat || '-'}</dd>
                            </div>
                        </dl>
                    `;

                    detailFileLink.href = btn.dataset.fileUrl || '#';

                    modalDetail.classList.remove('hidden');
                });
            });

            // ---------- EDIT MODAL ----------
            const modalEdit = document.getElementById('modal-edit');
            const formEdit = document.getElementById('form-edit');
            // Elemen select penerima & checkbox "pilih semua"
            const selectUsers = document.getElementById('edit-user_ids');
            const selectAllUsers = document.getElementById('edit-select-all-users');

            // Event "pilih semua user"
            if (selectUsers && selectAllUsers) {
                selectAllUsers.addEventListener('change', () => {
                    const checked = selectAllUsers.checked;
                    Array.from(selectUsers.options).forEach(opt => {
                        opt.selected = checked;
                    });
                });
            }


            if (modalEdit && formEdit) {
                document.querySelectorAll('[data-close-edit]').forEach(btn => {
                    btn.addEventListener('click', () => modalEdit.classList.add('hidden'));
                });

                document.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', () => {
                        formEdit.action = btn.dataset.updateUrl;

                        document.getElementById('edit-no_surat').value = btn.dataset.no || '';
                        document.getElementById('edit-tanggal_surat').value = btn.dataset.tglSurat || '';
                        document.getElementById('edit-tanggal_keluar').value = btn.dataset.tglKeluar || '';
                        document.getElementById('edit-tujuan_surat').value = btn.dataset.tujuan || '';
                        document.getElementById('edit-perihal').value = btn.dataset.perihal || '';
                        document.getElementById('edit-ringkasan').value = btn.dataset.ringkasan || '';
                        document.getElementById('edit-penandatangan').value = btn.dataset.ttd || '';
                        document.getElementById('edit-tingkat_penting').value = btn.dataset.tingkat || 'biasa';

                        const selectKategori = document.getElementById('edit-kategori_id');
                        if (selectKategori) {
                            selectKategori.value = btn.dataset.kategoriId || '';
                        }

                        // ===== SET PENERIMA =====
                        if (selectUsers) {
                            let selectedIds = [];
                            try {
                                selectedIds = btn.dataset.userIds ? JSON.parse(btn.dataset.userIds) : [];
                            } catch (e) {
                                selectedIds = [];
                            }

                            Array.from(selectUsers.options).forEach(opt => {
                                opt.selected = selectedIds.includes(parseInt(opt.value));
                            });

                            if (selectAllUsers) {
                                const allIds = Array.from(selectUsers.options).map(o => parseInt(o.value));
                                const allSelected = allIds.length && allIds.every(id => selectedIds.includes(id));
                                selectAllUsers.checked = allSelected;
                            }
                        }


                        modalEdit.classList.remove('hidden');
                    });
                });
            }

            // ---------- DELETE ----------
            const formDelete = document.getElementById('form-delete');
            if (formDelete) {
                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const perihal = btn.dataset.perihal || 'surat ini';
                        if (confirm(`Yakin ingin menghapus "${perihal}"? Tindakan ini tidak dapat dibatalkan.`)) {
                            formDelete.action = btn.dataset.deleteUrl;
                            formDelete.submit();
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>