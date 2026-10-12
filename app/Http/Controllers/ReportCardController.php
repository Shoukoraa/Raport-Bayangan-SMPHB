<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportCardController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with(['classroom', 'academicYear'])->paginate(20);

        return view('reports.index', compact('students'));
    }

    public function preview(Student $student)
    {
        $student->load(['classroom', 'grades.subject', 'attendance']);
        $school = SchoolSetting::first() ?? new SchoolSetting([
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang',
            'nama_kepala_sekolah' => 'Juliana Ruth Mathilda Hutapea, S.Th',
            'nip_kepala_sekolah' => '02980708020780',
            'nama_wali_kelas' => 'Farida Donna Gultom, S.Pd',
            'nip_wali_kelas' => '-',
            'judul_laporan' => 'Laporan Hasil Belajar Tengah Semester 1 (Raport Bayangan)',
            'tanggal_raport' => 'Karawang, 02 Oktober 2026',
        ]);
        $academicYear = $student->academicYear ?? AcademicYear::where('is_active', true)->first();

        return view('reports.preview', compact('student', 'school', 'academicYear'));
    }

    public function printAll()
    {
        $students = Student::with(['classroom', 'grades.subject', 'attendance'])->get();
        $school = SchoolSetting::first() ?? new SchoolSetting([
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang',
            'nama_kepala_sekolah' => 'Juliana Ruth Mathilda Hutapea, S.Th',
            'nip_kepala_sekolah' => '02980708020780',
            'nama_wali_kelas' => 'Farida Donna Gultom, S.Pd',
            'nip_wali_kelas' => '-',
            'judul_laporan' => 'Laporan Hasil Belajar Tengah Semester 1 (Raport Bayangan)',
            'tanggal_raport' => 'Karawang, 02 Oktober 2026',
        ]);
        $academicYear = AcademicYear::where('is_active', true)->first();

        return view('reports.print-all', compact('students', 'school', 'academicYear'));
    }

    public function excel(Student $student)
    {
        $student->load(['classroom', 'grades.subject', 'attendance']);
        $school = SchoolSetting::first() ?? new SchoolSetting([
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang',
            'nama_kepala_sekolah' => 'Juliana Ruth Mathilda Hutapea, S.Th',
            'nip_kepala_sekolah' => '02980708020780',
            'nama_wali_kelas' => 'Farida Donna Gultom, S.Pd',
            'nip_wali_kelas' => '-',
            'judul_laporan' => 'Laporan Hasil Belajar Tengah Semester 1 (Raport Bayangan)',
            'tanggal_raport' => 'Karawang, 02 Oktober 2026',
        ]);
        $academicYear = $student->academicYear ?? AcademicYear::where('is_active', true)->first();

        $templatePath = base_path('storage/templates/Raport_Bayangan_Polos.xlsx');
        if (!file_exists($templatePath)) {
            $templatePath = 'C:/laragon/www/SMPHB/Raport bayangan/Raport Bayangan Polos.xlsx';
        }

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($templatePath);

        // Update Sheet DATA
        $wsData = $spreadsheet->getSheetByName('DATA');
        if ($wsData) {
            $wsData->setCellValue('B3', $school->nama_sekolah ?? 'SMP HARAPAN BANGSA KARAWANG');
            $wsData->setCellValue('B4', $school->alamat ?? 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang');
            $wsData->setCellValue('B6', $academicYear->name ?? '2026/2027');
            $wsData->setCellValue('B7', $school->nama_kepala_sekolah ?? 'Juliana Ruth Mathilda Hutapea, S.Th');
            $wsData->setCellValueExplicit('B8', (string) ($school->nip_kepala_sekolah ?? '02980708020780'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $wsData->setCellValue('B9', $school->nama_wali_kelas ?? 'Farida Donna Gultom, S.Pd');
            $wsData->setCellValue('B10', $school->nip_wali_kelas ?? '');
            $wsData->setCellValue('B11', 'Karawang, 02 Oktober 2026');
            $wsData->setCellValue('B15', 'Pendidikan Agama Kristen & Budi Pekerti');
        }

        // Update Sheet INPUT_NILAI
        $wsIn = $spreadsheet->getSheetByName('INPUT_NILAI');
        if ($wsIn) {
            $wsIn->setCellValue('B3', $student->name);
            $wsIn->setCellValue('B4', $student->classroom->name ?? 'VII');
            $nisnNis = ($student->nisn ? $student->nisn : '') . ($student->nis ? ' - ' . $student->nis : '');
            $wsIn->setCellValue('B5', $nisnNis);
            $wsIn->setCellValue('B6', $academicYear->name ?? '2026/2027');

            $wsIn->setCellValue('B28', $student->attendance->sakit ?? 0);
            $wsIn->setCellValue('B29', $student->attendance->izin ?? 0);
            $wsIn->setCellValue('B30', $student->attendance->alpa ?? 0);
            $wsIn->setCellValue('B31', $student->attendance->note ?? 'Tetap semangat belajar ya nak!');

            // Map 17 subjects matching row 9 to 25
            $gradesByOrder = [];
            foreach ($student->grades as $g) {
                if ($g->subject && $g->subject->order_number) {
                    $gradesByOrder[$g->subject->order_number] = $g;
                }
            }

            for ($i = 1; $i <= 17; $i++) {
                $row = 8 + $i;
                if (isset($gradesByOrder[$i])) {
                    $g = $gradesByOrder[$i];
                    $wsIn->setCellValue('C' . $row, $g->pr !== null ? (int)round((float)$g->pr) : '-');
                    $wsIn->setCellValue('D' . $row, $g->uh !== null ? (int)round((float)$g->uh) : '-');
                    $wsIn->setCellValue('E' . $row, $g->tertulis !== null ? (int)round((float)$g->tertulis) : '-');
                    $wsIn->setCellValue('F' . $row, $g->praktek !== null ? (int)round((float)$g->praktek) : '-');
                }
            }
        }

        // Set active sheet to RAPORT
        $spreadsheet->setActiveSheetIndexByName('RAPORT');

        $cleanName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $student->name);
        $nis = $student->nis ?? '000';
        $filename = "Raport Bayangan - {$cleanName} ({$nis}).xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
