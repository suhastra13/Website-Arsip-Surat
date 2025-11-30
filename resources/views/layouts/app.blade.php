<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">

        {{-- SIDEBAR BIRU --}}
        <aside class="w-64 bg-blue-700 text-white flex flex-col">
            <div class="h-16 flex items-center px-4 border-b border-blue-500">
                <span class="font-semibold text-lg">
                    Arsip Surat
                </span>
            </div>

            {{-- MENU --}}
            <nav class="flex-1 py-4 space-y-1 text-sm font-medium">
                <a href="{{ route('dashboard') }}"
                    class="block px-4 py-2 {{ request()->routeIs('dashboard') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                    Dashboard
                </a>

                <a href="{{ route('surat.masuk.index') }}"
                    class="block px-4 py-2 {{ request()->routeIs('surat.masuk.*') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                    Surat Masuk
                </a>

                <a href="{{ route('surat.keluar.index') }}"
                    class="block px-4 py-2 {{ request()->routeIs('surat.keluar.*') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                    Surat Keluar
                </a>

                @auth
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('surat.create') }}"
                    class="block px-4 py-2 {{ request()->routeIs('surat.create') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                    Upload Surat
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="block px-4 py-2 {{ request()->routeIs('admin.users.index') ? 'bg-blue-800' : 'hover:bg-blue-600' }}">
                    Kelola Pengguna
                </a>
                @endif
                @endauth
            </nav>

            {{-- INFO USER + LOGOUT --}}
            @auth
            <div class="border-t border-blue-500 px-4 py-3 text-xs">
                <div class="font-semibold text-sm">
                    {{ Auth::user()->name }}
                </div>
                <div class="text-blue-100">
                    {{ Auth::user()->email }}
                </div>

                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="text-xs text-blue-100 hover:text-white">
                        Log Out
                    </button>
                </form>
            </div>
            @endauth
        </aside>

        {{-- AREA KONTEN --}}
        <div class="flex-1 flex flex-col">
            @isset($header)
            <header class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-6 py-4">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <main class="flex-1 bg-gray-50">
                <div class="max-w-7xl mx-auto px-6 py-6">
                    {{-- Card putih untuk konten --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>