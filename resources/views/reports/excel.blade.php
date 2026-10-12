<table>
    <!-- HEADER -->
    <tr>
        <td colspan="6" style="text-align: center; font-size: 16px; font-weight: bold;">[ LOGO SEKOLAH ]</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: center; font-size: 18px; font-weight: bold;">{{ $school->nama_sekolah ?? 'SMP HARAPAN BANGSA' }}</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: center;">{{ $school->alamat ?? 'Alamat Sekolah' }}</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: center;">Laporan Hasil Belajar Tengah Semester {{ $academicYear->semester }}</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: center;">Tahun Pelajaran {{ $academicYear->name }}</td>
    </tr>
    <tr><td colspan="6"></td></tr>

    <!-- IDENTITAS SISWA -->
    <tr>
        <td colspan="2">NAMA SISWA</td>
        <td colspan="4">: {{ $student->name }}</td>
    </tr>
    <tr>
        <td colspan="2">KELAS</td>
        <td colspan="4">: {{ $student->classroom->name }}</td>
    </tr>
    <tr>
        <td colspan="2">NOMOR INDUK</td>
        <td colspan="4">: {{ $student->nis ?? '-' }}</td>
    </tr>
    <tr><td colspan="6"></td></tr>

    <!-- TABEL NILAI -->
    <tr>
        <td rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold; width: 5px;">No</td>
        <td rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold; width: 35px;">MATA PELAJARAN</td>
        <td colspan="2" style="border: 1px solid #000; text-align: center; font-weight: bold;">FORMATIVE HARIAN</td>
        <td colspan="2" style="border: 1px solid #000; text-align: center; font-weight: bold;">SUMATIF</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">PR</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">UH</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">TERTULIS</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">PRAKTEK</td>
    </tr>

    @foreach($student->grades as $index => $grade)
    <tr>
        <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
        <td style="border: 1px solid #000;">{{ $grade->subject->name }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $grade->pr }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $grade->uh }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $grade->tertulis }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $grade->praktek }}</td>
    </tr>
    @endforeach
    
    <!-- JUMLAH NILAI & RATA-RATA -->
    <tr>
        <td colspan="2" style="border: 1px solid #000; text-align: center; font-weight: bold;">JUMLAH NILAI</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $student->grades->sum('pr') }}</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $student->grades->sum('uh') }}</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $student->grades->sum('tertulis') }}</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $student->grades->sum('praktek') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="border: 1px solid #000; text-align: center; font-weight: bold;">RATA-RATA</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($student->grades->avg('pr') ?? 0, 1) }}</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($student->grades->avg('uh') ?? 0, 1) }}</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($student->grades->avg('tertulis') ?? 0, 1) }}</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($student->grades->avg('praktek') ?? 0, 1) }}</td>
    </tr>
    
    <tr><td colspan="6"></td></tr>

    <!-- KEHADIRAN & CATATAN -->
    <tr>
        <td colspan="2" style="border: 1px solid #000; font-weight: bold;">Keterangan</td>
        <td colspan="4" style="border: 1px solid #000; font-weight: bold;">Catatan</td>
    </tr>
    <tr>
        <td colspan="2" style="border: 1px solid #000;">
            Sakit = {{ $student->attendance->sakit ?? 0 }} <br>
            Izin = {{ $student->attendance->izin ?? 0 }} <br>
            Alpa = {{ $student->attendance->alpa ?? 0 }}
        </td>
        <td colspan="4" style="border: 1px solid #000; vertical-align: top;">
            {{ $student->attendance->note ?? '-' }}
        </td>
    </tr>
    
    <tr><td colspan="6"></td></tr>
    <tr><td colspan="6"></td></tr>
    
    <!-- TANDA TANGAN -->
    <tr>
        <td colspan="3" style="text-align: center;">Mengetahui,</td>
        <td colspan="3" style="text-align: center;">{{ $school->kabupaten ?? 'Karawang' }}, {{ date('d F Y') }}</td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: center;">Kepala Sekolah {{ $school->nama_sekolah ?? 'SMP Harapan Bangsa' }}</td>
        <td colspan="3" style="text-align: center;">Wali Kelas</td>
    </tr>
    <tr><td colspan="6"></td></tr>
    <tr><td colspan="6"></td></tr>
    <tr><td colspan="6"></td></tr>
    <tr>
        <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $school->nama_kepala_sekolah ?? '_____________________' }}</td>
        <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $school->nama_wali_kelas ?? '_____________________' }}</td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: center;">NIP/NIPY: {{ $school->nip_kepala_sekolah ?? '-' }}</td>
        <td colspan="3" style="text-align: center;">NIP/NIPY: {{ $school->nip_wali_kelas ?? '-' }}</td>
    </tr>
</table>
