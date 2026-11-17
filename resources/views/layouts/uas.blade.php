<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle ?? 'Sistem Raport Resmi (UAS) - SMP Harapan Bangsa' }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen flex flex-col print:bg-white print:min-h-0">
            <!-- Header & Navbar UAS (Identitas Hijau Emerald) -->
            <header class="print:hidden bg-emerald-900 text-white shadow-md border-b-4 border-emerald-600">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-20">
                        <!-- Identitas Sekolah & Workspace -->
                        <div class="flex items-center gap-4">
                            <a href="{{ route('uas.dashboard') }}" class="flex items-center gap-3">
                                <img src="{{ asset('images/logo.png') }}" alt="Logo SMP Harapan Bangsa" class="h-12 w-12 object-contain bg-white rounded-full p-1 shadow-sm">
                                <div class="flex flex-col">
                                    <span class="text-xs font-semibold tracking-wider uppercase text-emerald-200">SMP Harapan Bangsa</span>
                                    <h1 class="text-lg sm:text-xl font-bold tracking-tight text-white leading-tight">
                                        Sistem Raport Resmi (UAS)
                                    </h1>
                                </div>
                            </a>
                        </div>

                        <!-- Menu Utama Workspace UAS & Navigasi Cepat -->
                        <div class="hidden lg:flex items-center gap-6">
                            <nav class="flex items-center gap-1.5" aria-label="Menu Utama UAS">
                                <a href="{{ route('uas.dashboard') }}" 
                                   class="px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('uas.dashboard') ? 'bg-emerald-800 text-white shadow-xs font-bold' : 'text-emerald-100 hover:bg-emerald-800/70 hover:text-white' }}">
                                    Dashboard UAS
                                </a>
                                <a href="{{ route('uas-reports.index') }}" 
                                   class="px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('uas-reports.*') ? 'bg-emerald-800 text-white shadow-xs font-bold' : 'text-emerald-100 hover:bg-emerald-800/70 hover:text-white' }}">
                                    Kelola & Cetak Raport UAS
                                </a>
                                <a href="{{ route('uas.settings.school') }}" 
                                   class="px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('uas.settings.*') ? 'bg-emerald-800 text-white shadow-xs font-bold' : 'text-emerald-100 hover:bg-emerald-800/70 hover:text-white' }}">
                                    Pengaturan Kop UAS
                                </a>
                            </nav>

                            <div class="h-8 w-px bg-emerald-700"></div>

                            <!-- Tombol Cepat Pindah Workspace & Portal -->
                            <div class="flex items-center gap-2.5">
                                <a href="{{ route('uts.dashboard') }}" 
                                   title="Beralih ke Sistem Raport Bayangan UTS" 
                                   class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-bold rounded-lg shadow-sm border border-blue-600 transition-colors">
                                    <span>⇄ Pindah ke UTS</span>
                                </a>
                                <a href="{{ route('portal') }}" 
                                   title="Kembali ke Halaman Pilihan Portal Utama" 
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-lg shadow-sm border border-slate-600 transition-colors">
                                    <span>⌂ Portal Utama</span>
                                </a>
                            </div>

                            <!-- User Dropdown Ringkas -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" type="button" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-950/70 text-emerald-200 hover:text-white border border-emerald-700">
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 text-slate-800 z-50">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-slate-100">Profil Saya</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Keluar Sistem</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Menu Button -->
                        <div class="lg:hidden flex items-center gap-2" x-data="{ mobileNav: false }">
                            <a href="{{ route('portal') }}" class="px-2.5 py-1.5 bg-slate-800 text-white rounded text-xs font-bold">⌂ Portal</a>
                            <a href="{{ route('uts.dashboard') }}" class="px-2.5 py-1.5 bg-blue-700 text-white rounded text-xs font-bold">⇄ UTS</a>
                        </div>
                    </div>

                    <!-- Subnav Mobile -->
                    <div class="lg:hidden flex overflow-x-auto py-2.5 gap-2 border-t border-emerald-800 text-xs">
                        <a href="{{ route('uas.dashboard') }}" class="px-3 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('uas.dashboard') ? 'bg-emerald-800 text-white font-bold' : 'text-emerald-200' }}">Dashboard UAS</a>
                        <a href="{{ route('uas-reports.index') }}" class="px-3 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('uas-reports.*') ? 'bg-emerald-800 text-white font-bold' : 'text-emerald-200' }}">Kelola & Cetak Raport UAS</a>
                        <a href="{{ route('uas.settings.school') }}" class="px-3 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('uas.settings.*') ? 'bg-emerald-800 text-white font-bold' : 'text-emerald-200' }}">Pengaturan Kop UAS</a>
                    </div>
                </div>
            </header>

            <!-- Page Header (Optional slot) -->
            @isset($header)
                <div class="print:hidden bg-white border-b border-slate-200 shadow-2xs">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <!-- Main Content -->
            <main class="flex-1 print:m-0 print:p-0">
                {{ $slot }}
            </main>

            <!-- Footer Khusus UAS -->
            <footer class="print:hidden bg-white border-t border-slate-200 py-4 mt-auto text-center text-xs text-slate-700">
                <div class="max-w-7xl mx-auto px-4">
                    SMP Harapan Bangsa &mdash; Workspace Raport Resmi (UAS / PAS / Kurikulum Merdeka)
                </div>
            </footer>
        </div>
    </body>
</html>
