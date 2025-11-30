<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah User
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl border border-slate-100">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-800">Form User Baru</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Tambahkan akun staf atau admin yang dapat mengakses sistem arsip.
                    </p>
                </div>

                <div class="px-6 py-5">
                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                        @csrf

                        {{-- Nama --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nama
                            </label>
                            <input id="name" name="name" type="text"
                                value="{{ old('name') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900
               focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                required>
                            @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1" for="email">
                                Email
                            </label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                class="block w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm bg-slate-50 px-3 py-2">
                            @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1" for="password">
                                Password
                            </label>
                            <input id="password" name="password" type="password" required
                                class="block w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm bg-slate-50 px-3 py-2">
                            <p class="mt-1 text-[11px] text-slate-500">Minimal 8 karakter.</p>
                            @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1" for="role">
                                Role
                            </label>
                            <select id="role" name="role" required
                                class="block w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm bg-slate-50 px-3 py-2">
                                <option value="staf" {{ old('role') === 'staf' ? 'selected' : '' }}>Staf</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-3 flex items-center justify-between">
                            <a href="{{ route('admin.users.index') }}"
                                class="text-xs text-slate-500 hover:text-slate-700">
                                ← Kembali
                            </a>

                            <button type="submit"
                                class="inline-flex items-center px-5 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>