<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Tahun Pelajaran & Semester') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Atur semester yang sedang aktif dan lihat riwayat arsip nilai siswa.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('portal') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-bold text-xs border border-slate-300 transition-colors">
                    <span>⌂ Portal Utama</span>
                </a>
                @if($activeYear)
                    <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                        <span>Aktif: {{ $activeYear->name }} - Semester {{ $activeYear->semester }} ({{ $activeYear->semester_name }})</span>
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        editModalOpen: false,
        editId: null,
        editName: '',
        editSemester: '1',
        openEdit(id, name, semester) {
            this.editId = id;
            this.editName = name;
            this.editSemester = semester;
            this.editModalOpen = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-xs flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm font-semibold text-emerald-900">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-xs">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-rose-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <span class="text-sm font-bold text-rose-900">Perhatian:</span>
                            <ul class="list-disc list-inside text-xs text-rose-800 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Statistik Kartu -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase text-gray-500 tracking-wider">Tahun Pelajaran Aktif</div>
                        <div class="text-xl font-extrabold text-blue-900 mt-1">
                            {{ $activeYear ? $activeYear->name : 'Belum Dipilih' }}
                        </div>
                        <div class="text-xs text-blue-700 font-semibold mt-0.5">
                            Semester {{ $activeYear ? $activeYear->semester . ' (' . $activeYear->semester_name . ')' : '-' }}
                        </div>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-700 rounded-xl">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase text-gray-500 tracking-wider">Total Riwayat Periode</div>
                        <div class="text-xl font-extrabold text-indigo-900 mt-1">
                            {{ $academicYears->count() }} Periode
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">Tersimpan dalam database</div>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-700 rounded-xl">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase text-gray-500 tracking-wider">Total Nilai Riwayat</div>
                        <div class="text-xl font-extrabold text-emerald-900 mt-1">
                            {{ $academicYears->sum('student_grades_count') }} Catatan Nilai
                        </div>
                        <div class="text-xs text-emerald-700 font-semibold mt-0.5">Arsip aman dari tahun ke tahun</div>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Grid Form Tambah & Tabel Daftar -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Form Tambah Tahun Pelajaran Baru -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-xs h-fit">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                        <div class="p-1.5 bg-blue-100 text-blue-800 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Tambah Periode Baru</h3>
                            <p class="text-xs text-gray-500">Mulai semester / tahun ajaran baru.</p>
                        </div>
                    </div>

                    <form action="{{ route('academic-years.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                                Tahun Pelajaran <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: 2026/2027" required
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600 shadow-2xs font-mono">
                            <p class="text-[11px] text-gray-400 mt-1">Gunakan format 4 digit tahun / 4 digit tahun.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                                Semester <span class="text-rose-600">*</span>
                            </label>
                            <select name="semester" required class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600 shadow-2xs">
                                <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                                <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Semester 2 (Genap)</option>
                            </select>
                        </div>

                        <div class="pt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-700 focus:ring-blue-600">
                                <span class="text-xs font-semibold text-gray-700">Langsung jadikan periode aktif</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-bold text-sm shadow-sm transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Simpan Periode Baru</span>
                        </button>
                    </form>
                </div>

                <!-- Tabel Riwayat Periode -->
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Daftar Arsip & Riwayat Semester</h3>
                            <p class="text-xs text-gray-500">Klik "Jadikan Aktif" untuk berpindah periode kerja saat ini.</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md">
                            {{ $academicYears->count() }} Entri
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-gray-700 text-xs font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="px-5 py-3.5 text-left">Tahun Pelajaran</th>
                                    <th class="px-5 py-3.5 text-center">Semester</th>
                                    <th class="px-5 py-3.5 text-center">Status</th>
                                    <th class="px-5 py-3.5 text-center">Data Tersimpan</th>
                                    <th class="px-5 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($academicYears as $year)
                                    <tr class="hover:bg-slate-50 transition-colors {{ $year->is_active ? 'bg-emerald-50/40' : '' }}">
                                        <td class="px-5 py-4 whitespace-nowrap font-bold text-gray-900">
                                            <div class="flex items-center gap-2">
                                                <span>{{ $year->name }}</span>
                                                @if($year->is_active)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-600 text-white shadow-2xs">
                                                        AKTIF
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold {{ $year->semester == 1 ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-cyan-100 text-cyan-900 border border-cyan-300' }}">
                                                Semester {{ $year->semester }} ({{ $year->semester_name }})
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-center">
                                            @if($year->is_active)
                                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800">
                                                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Sedang Aktif
                                                </span>
                                            @else
                                                <form action="{{ route('academic-years.set-active', $year->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-emerald-600 hover:text-white border border-emerald-600 text-emerald-700 rounded-md text-xs font-bold transition-all shadow-2xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Jadikan Aktif
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-center text-xs text-gray-600">
                                            <span class="font-bold text-gray-900">{{ $year->student_grades_count }}</span> Nilai
                                            <span class="text-gray-300 mx-1">|</span>
                                            <span class="font-bold text-gray-900">{{ $year->attendances_count }}</span> Absensi
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-right">
                                            <div class="inline-flex items-center gap-2">
                                                <button type="button" @click="openEdit({{ $year->id }}, '{{ $year->name }}', '{{ $year->semester }}')"
                                                    class="inline-flex items-center px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-xs font-bold transition-all">
                                                    Edit
                                                </button>
                                                @if(!$year->is_active && $year->student_grades_count == 0)
                                                    <form action="{{ route('academic-years.destroy', $year->id) }}" method="POST" class="inline"
                                                        onsubmit="return confirm('Hapus periode {{ $year->full_name }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-2.5 py-1 bg-rose-50 hover:bg-rose-700 hover:text-white text-rose-700 border border-rose-200 rounded-md text-xs font-bold transition-all">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            Belum ada data tahun pelajaran. Silakan tambahkan melalui form di samping.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        <!-- MODAL EDIT TAHUN PELAJARAN -->
        <div x-show="editModalOpen" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-gray-200 space-y-4"
                @click.away="editModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Edit Periode Pelajaran</h3>
                    <button type="button" @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form :action="'/academic-years/' + editId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tahun Pelajaran</label>
                        <input type="text" name="name" x-model="editName" required
                            class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Semester</label>
                        <select name="semester" x-model="editSemester" required
                            class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">
                            <option value="1">Semester 1 (Ganjil)</option>
                            <option value="2">Semester 2 (Genap)</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="editModalOpen = false"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
