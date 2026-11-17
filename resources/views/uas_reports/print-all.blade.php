<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Semua Raport UAS - {{ $academicYear->full_name ?? 'Semester Aktif' }}</title>
    <style>
        /* Pengaturan Cetak A4 Presisi & Hilangkan Header/Footer Default Browser */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background-color: #f1f5f9;
            margin: 0;
            padding: 24px 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            font-size: 9pt;
            line-height: 1.3;
        }

        /* Kontainer Lembar Halaman Pasti A4 (210mm x 297mm) */
        .page {
            width: 210mm;
            height: 297mm;
            padding: 12mm 14mm 18mm 14mm;
            box-sizing: border-box;
            position: relative;
            page-break-after: always;
            break-after: always;
            overflow: hidden;
            background: white;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            margin: 0 auto 24px auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .page:last-child {
            margin-bottom: 0;
        }

        /* Styling Tabel dan Kotak Resmi */
        .table-official {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #334155;
            font-size: 8.5pt;
        }

        .table-official th,
        .table-official td {
            border: 1px solid #334155;
            padding: 3.5px 5px;
        }

        .table-official th {
            background-color: #EFF3F8 !important;
            font-weight: bold;
            text-align: center;
        }

        .box-header {
            background-color: #EFF3F8 !important;
            border: 1px solid #334155;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            padding: 3.5px 6px;
        }

        .box-content {
            border: 1px solid #334155;
            border-top: none;
            padding: 5px 8px;
            font-size: 8.5pt;
            line-height: 1.35;
        }

        /* Footer Halaman Resmi */
        .page-footer {
            position: absolute;
            bottom: 8mm;
            left: 14mm;
            right: 14mm;
            border-top: 1px solid #334155;
            padding-top: 4px;
            font-size: 8.5pt;
            font-style: italic;
            display: flex;
            justify-content: space-between;
            font-family: 'Liberation Serif', 'Times New Roman', Times, serif;
            color: #000;
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .page {
                margin: 0 !important;
                box-shadow: none !important;
                page-break-after: always !important;
                break-after: always !important;
            }

            .page:last-child {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- TOMBOL AKSI MELAYANG DI LAYAR (HILANG SAAT DICETAK) -->
    <div class="no-print" style="position: fixed; top: 16px; right: 20px; z-index: 9999; display: flex; gap: 10px; align-items: center; background: white; padding: 8px 14px; border-radius: 12px; box-shadow: 0 4px 14px rgba(0,0,0,0.15); border: 1px solid #cbd5e1;">
        <span style="font-size: 12px; font-weight: bold; color: #334155;">
            Total: {{ $students->count() }} Siswa ({{ $students->count() * 3 }} Halaman A4)
        </span>
        <a href="{{ route('uas-reports.index') }}" style="text-decoration: none; padding: 8px 14px; background-color: #334155; color: white; border-radius: 8px; font-weight: bold; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
            &larr; Kembali ke Raport UAS
        </a>
        <button onclick="window.print()" style="cursor: pointer; border: none; padding: 8px 16px; background-color: #047857; color: white; border-radius: 8px; font-weight: bold; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Cetak Semua Raport (PDF)</span>
        </button>
    </div>

    @php
        $activeSubjects = $subjects->where('is_active', true)->values();
        $totalSubj = $activeSubjects->count();
        $splitCount = (int) ceil($totalSubj / 2);
        $page1Subjects = $activeSubjects->slice(0, $splitCount)->values();
        $page2Subjects = $activeSubjects->slice($splitCount)->values();
    @endphp

    @foreach($students as $student)
        @php
            $uasGrades = $student->uasGrades->keyBy('subject_id');
            $extracurriculars = $student->extracurriculars ?? collect();
            $uasNote = $student->uasNote;
            $attendance = $student->attendance;

            $kelasStr = $student->classroom->name ?? '7 B';
            if (!str_starts_with(strtolower($kelasStr), 'kelas')) {
                $kelasStr = 'Kelas ' . $kelasStr;
            }

            $firstEkskul = $extracurriculars->first();
            $remainingEkskuls = $extracurriculars->slice(1)->values();
        @endphp

        <!-- ======================================================== -->
        <!-- HALAMAN 1 DARI 3: IDENTITAS + MAPEL BAGIAN 1             -->
        <!-- ======================================================== -->
        <div class="page">
            <!-- KOP IDENTITAS SISWA (HALAMAN 1) -->
            <table style="width: 100%; border-collapse: collapse; font-size: 9pt; line-height: 1.35; margin-bottom: 4px;">
                <tr>
                    <td style="width: 100px; vertical-align: top;">Nama Murid</td>
                    <td style="width: 12px; vertical-align: top;">:</td>
                    <td style="font-weight: bold; text-transform: uppercase; vertical-align: top; width: 45%;">{{ $student->name }}</td>
                    <td style="width: 100px; vertical-align: top;">Kelas</td>
                    <td style="width: 12px; vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $kelasStr }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">NIS/NISN</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</td>
                    <td style="vertical-align: top;">Fase</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $uasNote->fase ?? 'D' }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Sekolah</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $school->nama_sekolah ?? 'SMP HARAPAN BANGSA KARAWANG' }}</td>
                    <td style="vertical-align: top;">Semester</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $academicYear->semester ?? 1 }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Alamat</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $school->alamat ?? 'Jl. R.E. Martadinata no. 8 Karawang' }}</td>
                    <td style="vertical-align: top;">Tahun Ajaran</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $academicYear->name ?? '2025/2026' }}</td>
                </tr>
            </table>
            <div style="border-bottom: 1px solid #334155; margin-bottom: 6px;"></div>

            <!-- JUDUL LAPORAN HASIL BELAJAR -->
            <div style="text-align: center; font-weight: bold; font-size: 11.5pt; margin: 4px 0 8px 0; letter-spacing: 0.5px;">
                LAPORAN HASIL BELAJAR
            </div>

            <!-- TABEL NILAI MATA PELAJARAN BAGIAN PERTAMA -->
            <table class="table-official">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th style="width: 155px;">Mata Pelajaran</th>
                        <th style="width: 65px;">Nilai Akhir</th>
                        <th>Capaian Kompetensi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($page1Subjects as $subIndex => $subject)
                        @php
                            $grade = $uasGrades->get($subject->id);
                            $na = $grade && $grade->nilai_akhir !== null ? number_format($grade->nilai_akhir, 0) : '-';

                            $capaianParts = [];
                            if (!empty($grade?->capaian_tertinggi)) {
                                $t = trim($grade->capaian_tertinggi);
                                if (!str_starts_with(strtolower($t), 'mencapai') && !str_starts_with(strtolower($t), 'menunjukkan')) {
                                    $capaianParts[] = 'Mencapai Kompetensi dengan sangat baik dalam hal ' . rtrim($t, '.') . '.';
                                } else {
                                    $capaianParts[] = rtrim($t, '.') . '.';
                                }
                            }
                            if (!empty($grade?->capaian_perlu_peningkatan)) {
                                $p = trim($grade->capaian_perlu_peningkatan);
                                if (!str_starts_with(strtolower($p), 'perlu')) {
                                    $capaianParts[] = 'Perlu peningkatan dalam hal ' . rtrim($p, '.') . '.';
                                } else {
                                    $capaianParts[] = rtrim($p, '.') . '.';
                                }
                            }
                        @endphp
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">{{ $loop->iteration }}</td>
                            <td style="vertical-align: middle; font-weight: normal;">{{ $subject->name }}</td>
                            <td style="text-align: center; vertical-align: middle; font-size: 9pt;">{{ $na }}</td>
                            <td style="vertical-align: top; font-size: 8pt; line-height: 1.25; text-align: left;">
                                @if(count($capaianParts) > 0)
                                    @foreach($capaianParts as $part)
                                        <div style="margin-bottom: 2px;">{{ $part }}</div>
                                    @endforeach
                                @else
                                    &nbsp;
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- FOOTER HALAMAN 1 -->
            <div class="page-footer">
                <span>{{ $kelasStr }} &nbsp;|&nbsp; {{ strtoupper($student->name) }} &nbsp;|&nbsp; {{ $student->nis ?? '-' }}</span>
                <span>Halaman : 1</span>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- HALAMAN 2 DARI 3: LANJUTAN MAPEL + KOKURIKULER + EKSKUL 1 -->
        <!-- ======================================================== -->
        <div class="page">
            <!-- KOP IDENTITAS SISWA (HALAMAN 2) -->
            <table style="width: 100%; border-collapse: collapse; font-size: 9pt; line-height: 1.35; margin-bottom: 4px;">
                <tr>
                    <td style="width: 100px; vertical-align: top;">Nama Murid</td>
                    <td style="width: 12px; vertical-align: top;">:</td>
                    <td style="font-weight: bold; text-transform: uppercase; vertical-align: top; width: 45%;">{{ $student->name }}</td>
                    <td style="width: 100px; vertical-align: top;">Kelas</td>
                    <td style="width: 12px; vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $kelasStr }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">NIS/NISN</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</td>
                    <td style="vertical-align: top;">Fase</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $uasNote->fase ?? 'D' }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Sekolah</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $school->nama_sekolah ?? 'SMP HARAPAN BANGSA KARAWANG' }}</td>
                    <td style="vertical-align: top;">Semester</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $academicYear->semester ?? 1 }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Alamat</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $school->alamat ?? 'Jl. R.E. Martadinata no. 8 Karawang' }}</td>
                    <td style="vertical-align: top;">Tahun Ajaran</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $academicYear->name ?? '2025/2026' }}</td>
                </tr>
            </table>
            <div style="border-bottom: 1px solid #334155; margin-bottom: 8px;"></div>

            <!-- TABEL LANJUTAN MATA PELAJARAN -->
            <table class="table-official">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th style="width: 155px;">Mata Pelajaran</th>
                        <th style="width: 65px;">Nilai Akhir</th>
                        <th>Capaian Kompetensi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($page2Subjects as $subIndex => $subject)
                        @php
                            $grade = $uasGrades->get($subject->id);
                            $na = $grade && $grade->nilai_akhir !== null ? number_format($grade->nilai_akhir, 0) : '-';
                            $realNum = $splitCount + $subIndex + 1;

                            $capaianParts = [];
                            if (!empty($grade?->capaian_tertinggi)) {
                                $t = trim($grade->capaian_tertinggi);
                                if (!str_starts_with(strtolower($t), 'mencapai') && !str_starts_with(strtolower($t), 'menunjukkan')) {
                                    $capaianParts[] = 'Mencapai Kompetensi dengan sangat baik dalam hal ' . rtrim($t, '.') . '.';
                                } else {
                                    $capaianParts[] = rtrim($t, '.') . '.';
                                }
                            }
                            if (!empty($grade?->capaian_perlu_peningkatan)) {
                                $p = trim($grade->capaian_perlu_peningkatan);
                                if (!str_starts_with(strtolower($p), 'perlu')) {
                                    $capaianParts[] = 'Perlu peningkatan dalam hal ' . rtrim($p, '.') . '.';
                                } else {
                                    $capaianParts[] = rtrim($p, '.') . '.';
                                }
                            }
                        @endphp
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">{{ $page1Subjects->count() + $loop->iteration }}</td>
                            <td style="vertical-align: middle; font-weight: normal;">{{ $subject->name }}</td>
                            <td style="text-align: center; vertical-align: middle; font-size: 9pt;">{{ $na }}</td>
                            <td style="vertical-align: top; font-size: 8pt; line-height: 1.25; text-align: left;">
                                @if(count($capaianParts) > 0)
                                    @foreach($capaianParts as $part)
                                        <div style="margin-bottom: 2px;">{{ $part }}</div>
                                    @endforeach
                                @else
                                    &nbsp;
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- KOTAK KOKURIKULER (P5) -->
            <div style="margin-top: 10px;">
                <div class="box-header">Kokurikuler</div>
                <div class="box-content" style="text-align: justify; min-height: 48px;">
                    {{ $uasNote->kokurikuler_deskripsi ?? '' }}
                </div>
            </div>

            <!-- TABEL EKSTRAKURIKULER (ITEM PERTAMA) -->
            <div style="margin-top: 10px;">
                <table class="table-official">
                    <thead>
                        <tr>
                            <th style="width: 30px;">No</th>
                            <th style="width: 155px;">Ekstrakurikuler</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">1</td>
                            <td style="vertical-align: middle;">{{ $firstEkskul->kegiatan ?? '-' }}</td>
                            <td style="vertical-align: middle; font-size: 8.5pt;">{{ $firstEkskul->keterangan ?? ($firstEkskul ? '-' : '-') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- FOOTER HALAMAN 2 -->
            <div class="page-footer">
                <span>{{ $kelasStr }} &nbsp;|&nbsp; {{ strtoupper($student->name) }} &nbsp;|&nbsp; {{ $student->nis ?? '-' }}</span>
                <span>Halaman : 2</span>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- HALAMAN 3 DARI 3: EKSKUL LANJUTAN + PRESENSI + TTD       -->
        <!-- ======================================================== -->
        <div class="page">
            <!-- KOP IDENTITAS SISWA (HALAMAN 3) -->
            <table style="width: 100%; border-collapse: collapse; font-size: 9pt; line-height: 1.35; margin-bottom: 4px;">
                <tr>
                    <td style="width: 100px; vertical-align: top;">Nama Murid</td>
                    <td style="width: 12px; vertical-align: top;">:</td>
                    <td style="font-weight: bold; text-transform: uppercase; vertical-align: top; width: 45%;">{{ $student->name }}</td>
                    <td style="width: 100px; vertical-align: top;">Kelas</td>
                    <td style="width: 12px; vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $kelasStr }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">NIS/NISN</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</td>
                    <td style="vertical-align: top;">Fase</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $uasNote->fase ?? 'D' }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Sekolah</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $school->nama_sekolah ?? 'SMP HARAPAN BANGSA KARAWANG' }}</td>
                    <td style="vertical-align: top;">Semester</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $academicYear->semester ?? 1 }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Alamat</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $school->alamat ?? 'Jl. R.E. Martadinata no. 8 Karawang' }}</td>
                    <td style="vertical-align: top;">Tahun Ajaran</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $academicYear->name ?? '2025/2026' }}</td>
                </tr>
            </table>
            <div style="border-bottom: 1px solid #334155; margin-bottom: 8px;"></div>

            <!-- LANJUTAN EKSTRAKURIKULER (JIKA ADA LEBIH DARI 1) -->
            @if($remainingEkskuls->count() > 0)
                <div style="margin-bottom: 10px;">
                    <table class="table-official">
                        <thead>
                            <tr>
                                <th style="width: 30px;">No</th>
                                <th style="width: 155px;">Ekstrakurikuler</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($remainingEkskuls as $remIdx => $remEkskul)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">{{ $remIdx + 2 }}</td>
                                    <td style="vertical-align: middle;">{{ $remEkskul->kegiatan }}</td>
                                    <td style="vertical-align: middle; font-size: 8.5pt;">{{ $remEkskul->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- BARIS SEJAJAR 2 KOLOM: KETIDAKHADIRAN & CATATAN WALI KELAS -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <tr>
                    <!-- KOTAK KETIDAKHADIRAN (LEBAR ~34%) -->
                    <td style="width: 34%; vertical-align: top; padding-right: 8px;">
                        <div class="box-header">Ketidakhadiran</div>
                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #334155; border-top: none; font-size: 8.5pt;">
                            <tr>
                                <td style="padding: 4px 6px; width: 62%;">Sakit</td>
                                <td style="padding: 4px 6px; text-align: left;">: &nbsp;{{ $attendance->sakit ?? 0 }} &nbsp;hari</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 6px; border-top: 1px solid #334155;">Izin</td>
                                <td style="padding: 4px 6px; border-top: 1px solid #334155; text-align: left;">: &nbsp;{{ $attendance->izin ?? 0 }} &nbsp;hari</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 6px; border-top: 1px solid #334155;">Tanpa Keterangan</td>
                                <td style="padding: 4px 6px; border-top: 1px solid #334155; text-align: left;">: &nbsp;{{ $attendance->alpa ?? 0 }} &nbsp;hari</td>
                            </tr>
                        </table>
                    </td>

                    <!-- KOTAK CATATAN WALI KELAS (LEBAR ~66%) -->
                    <td style="width: 66%; vertical-align: top;">
                        <div class="box-header">Catatan Wali Kelas</div>
                        <div class="box-content" style="min-height: 58px;">
                            {{ $uasNote->catatan_wali_kelas ?? '' }}
                        </div>
                    </td>
                </tr>
            </table>

            <!-- KOTAK TANGGAPAN ORANG TUA/WALI MURID -->
            <div style="margin-bottom: 12px;">
                <div class="box-header">Tanggapan Orang Tua/Wali Murid</div>
                <div style="border: 1px solid #334155; border-top: none; height: 18mm;"></div>
            </div>

            <!-- BLOK TANDA TANGAN 3 POSISI RESMI -->
            <table style="width: 100%; border-collapse: collapse; font-size: 9pt; margin-top: 6px;">
                <tr>
                    <td style="width: 45%; vertical-align: top; text-align: left;">
                        <div>Orang Tua Murid</div>
                        <div style="height: 18mm;"></div>
                        <div>...................................................</div>
                    </td>
                    <td style="width: 10%;"></td>
                    <td style="width: 45%; vertical-align: top; text-align: right;">
                        <div style="text-align: left; display: inline-block; min-width: 190px;">
                            <div>{{ $school->tanggal_raport ?? 'Karawang, 22 Desember 2026' }}</div>
                            <div>Wali Kelas</div>
                            <div style="height: 18mm;"></div>
                            <div style="font-weight: bold; text-decoration: underline;">{{ $school->nama_wali_kelas ?? 'Farida Donna Gultom, S.Pd' }}</div>
                            <div>NIP. {{ $school->nip_wali_kelas ?? '-' }}</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center; padding-top: 6px;">
                        <div>Mengetahui,</div>
                        <div>Kepala Sekolah</div>
                        <div style="height: 18mm;"></div>
                        <div style="font-weight: bold; text-decoration: underline;">{{ $school->nama_kepala_sekolah ?? 'Juliana Ruth Mathilda Hutapea, S.Th' }}</div>
                        <div>NIP. {{ $school->nip_kepala_sekolah ?? '02980708020780' }}</div>
                    </td>
                </tr>
            </table>

            <!-- FOOTER HALAMAN 3 -->
            <div class="page-footer">
                <span>{{ $kelasStr }} &nbsp;|&nbsp; {{ strtoupper($student->name) }} &nbsp;|&nbsp; {{ $student->nis ?? '-' }}</span>
                <span>Halaman : 3</span>
            </div>
        </div>
    @endforeach

</body>
</html>
