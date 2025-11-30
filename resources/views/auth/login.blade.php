<x-guest-layout>
    <x-auth-card>
        <h1 class="text-xl font-semibold text-slate-800 text-center mb-1">
            Login Arsip Surat
        </h1>
        <p class="text-xs text-slate-500 text-center mb-6">
            Masuk menggunakan akun yang telah dibuat oleh admin.
        </p>

        <!-- Status session -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Error validasi -->
        @if ($errors->any())
        <div class="mb-4 text-sm text-red-600">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Email --}}
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required autofocus
                    class="block mt-1 w-full" />
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" value="Password" />
                    @if (Route::has('password.request'))
                    <a class="text-xs text-blue-600 hover:underline"
                        href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                    @endif
                </div>

                <x-text-input id="password"
                    type="password"
                    name="password"
                    required autocomplete="current-password"
                    class="block mt-1 w-full" />
            </div>

            {{-- Ingat saya --}}
            <div class="flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
                    name="remember">
                <label for="remember_me" class="ml-2 text-xs text-slate-600">
                    Ingat saya
                </label>
            </div>

            {{-- Tombol --}}
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full inline-flex justify-center items-center px-4 py-2
                           bg-blue-600 border border-transparent rounded-md
                           font-semibold text-sm text-white
                           hover:bg-blue-700 focus:outline-none
                           focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                           transition">
                    MASUK
                </button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>