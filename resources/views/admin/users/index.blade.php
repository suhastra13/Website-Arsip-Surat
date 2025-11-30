<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kelola User
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between items-center">
                <div>
                    @if (session('success'))
                    <div class="mb-2 rounded-md bg-emerald-50 border border-emerald-200 px-3 py-2 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="mb-2 rounded-md bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                    @endif
                </div>

                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    + Tambah User
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-100">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-800">Daftar User</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        User internal yang dapat mengakses sistem & surat yang di-share.
                    </p>
                </div>

                <div class="px-6 py-4 overflow-x-auto">
                    @if ($users->isEmpty())
                    <p class="text-sm text-slate-500">Belum ada user terdaftar.</p>
                    @else
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                <th class="py-2 pr-4">Nama</th>
                                <th class="py-2 pr-4">Email</th>
                                <th class="py-2 pr-4">Role</th>
                                <th class="py-2 pr-4">Dibuat</th>
                                <th class="py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($users as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 pr-4">
                                    <div class="font-medium text-slate-800">{{ $user->name }}</div>
                                </td>
                                <td class="py-2 pr-4 text-slate-600">
                                    {{ $user->email }}
                                </td>
                                <td class="py-2 pr-4">
                                    @if ($user->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Admin
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200">
                                        Staf
                                    </span>
                                    @endif
                                </td>
                                <td class="py-2 pr-4 text-slate-500">
                                    {{ $user->created_at?->format('d-m-Y H:i') }}
                                </td>
                                <td class="py-2 text-right">
                                    @if (auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus user {{ $user->name }}?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-600 border border-red-200 hover:bg-red-100">
                                            Hapus
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-xs text-slate-400 italic">
                                        (akun Anda)
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>