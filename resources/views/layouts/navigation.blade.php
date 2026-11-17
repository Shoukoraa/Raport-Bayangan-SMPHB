<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 print:hidden">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('portal') }}" class="flex items-center gap-3 group">
                        <x-application-logo class="block h-10 w-auto" />
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-xs font-black tracking-wider uppercase text-blue-900 group-hover:text-blue-700 transition">SMP Harapan Bangsa</span>
                            <span class="text-[10px] text-gray-500 font-semibold tracking-tight">Portal Sistem Raport</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links Utama -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('portal')" :active="request()->routeIs('portal') || request()->routeIs('dashboard')">
                        {{ __('Portal Utama') }}
                    </x-nav-link>

                    <!-- Modul Raport Bayangan (UTS) -->
                    <x-nav-link :href="route('uts.dashboard')" :active="request()->routeIs('uts.*') || request()->routeIs('reports.*') || request()->routeIs('grades.*')">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <span>{{ __('Workspace UTS') }}</span>
                        </span>
                    </x-nav-link>

                    <!-- Modul Raport Akhir Semester (UAS / Pemerintah) -->
                    <x-nav-link :href="route('uas.dashboard')" :active="request()->routeIs('uas.*') || request()->routeIs('uas-reports.*')">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            <span>{{ __('Workspace UAS') }}</span>
                        </span>
                    </x-nav-link>

                    <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                        {{ __('Data Siswa') }}
                    </x-nav-link>

                    <!-- Dropdown Menu Pengaturan & Admin (Tidak Menumpuk di Navbar) -->
                    <div class="inline-flex items-center">
                        <x-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-transparent text-sm font-semibold rounded-lg {{ request()->routeIs('academic-years.*', 'subjects.*', 'settings.*') ? 'text-blue-800 bg-blue-50 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} transition">
                                    <span>Pengaturan</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-2 text-[11px] font-bold uppercase text-gray-400 tracking-wider border-b border-gray-100">
                                    Manajemen Akademik
                                </div>
                                <x-dropdown-link :href="route('academic-years.index')" class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>Tahun Pelajaran & Semester</span>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('subjects.index')" class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    <span>Mata Pelajaran</span>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('settings.school')" class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span>Kop Surat & Data Sekolah</span>
                                </x-dropdown-link>

                                <div class="border-t border-gray-100 my-1"></div>
                                <div class="px-4 py-2 text-[11px] font-bold uppercase text-gray-400 tracking-wider border-b border-gray-100">
                                    Keamanan Data
                                </div>
                                <x-dropdown-link :href="route('settings.database')" class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 5c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3"></path></svg>
                                    <span>Cadangan & Pemulihan Data</span>
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>

            <!-- Profile & Logout Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-4">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-semibold rounded-md text-gray-600 bg-white hover:text-gray-900 focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 text-gray-400" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil Akun') }}
                        </x-dropdown-link>

                        <div class="border-t border-gray-100 my-1"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Keluar (Log Out)') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-b border-gray-200 shadow-sm">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('portal')" :active="request()->routeIs('portal') || request()->routeIs('dashboard')">
                {{ __('Portal Utama') }}
            </x-responsive-nav-link>

            <div class="pt-2 pb-1 px-4 text-[11px] font-bold uppercase text-blue-700 tracking-wider">
                Raport Bayangan (UTS)
            </div>
            <x-responsive-nav-link :href="route('uts.dashboard')" :active="request()->routeIs('uts.*')">
                {{ __('Dashboard UTS') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*') || request()->routeIs('grades.*')">
                {{ __('Kelola & Cetak Nilai UTS') }}
            </x-responsive-nav-link>

            <div class="pt-2 pb-1 px-4 text-[11px] font-bold uppercase text-emerald-700 tracking-wider">
                Raport Akhir Semester (UAS)
            </div>
            <x-responsive-nav-link :href="route('uas.dashboard')" :active="request()->routeIs('uas.*')">
                {{ __('Dashboard UAS') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('uas-reports.index')" :active="request()->routeIs('uas-reports.*')">
                {{ __('Kelola & Cetak Raport UAS') }}
            </x-responsive-nav-link>

            <div class="pt-2 pb-1 px-4 text-[11px] font-bold uppercase text-slate-700 tracking-wider">
                Data Master
            </div>
            <x-responsive-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                {{ __('Data Siswa') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('subjects.index')" :active="request()->routeIs('subjects.*')">
                {{ __('Mata Pelajaran') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('settings.school')" :active="request()->routeIs('settings.school*')">
                {{ __('Kop Surat') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('settings.database')" :active="request()->routeIs('settings.database*')">
                {{ __('Cadangan & Pemulihan Data') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-2 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-xs text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil Akun') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Keluar') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
