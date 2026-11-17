<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentExtracurricular;
use App\Models\StudentUasGrade;
use App\Models\StudentUasNote;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UasReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Classroom $classroom;

    protected AcademicYear $academicYear;

    protected Student $student;

    protected Subject $subject1;

    protected Subject $subject2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'semester' => 1,
            'is_active' => true,
        ]);

        $this->classroom = Classroom::create(['name' => '7B']);

        $this->student = Student::create([
            'name' => 'Adara Adelia Putri',
            'nis' => '252670004',
            'nisn' => '0137829848',
            'gender' => 'Perempuan',
            'status' => 'Aktif',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->academicYear->id,
        ]);

        $this->subject1 = Subject::create([
            'name' => 'Pendidikan Pancasila',
            'code' => 'PPKN',
            'order_number' => 1,
            'is_active' => true,
        ]);

        $this->subject2 = Subject::create([
            'name' => 'Bahasa Indonesia',
            'code' => 'BIND',
            'order_number' => 2,
            'is_active' => true,
        ]);
    }

    public function test_user_can_view_uas_reports_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('uas-reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Raport Akhir Semester (UAS / PAS)');
        $response->assertSee('Adara Adelia Putri');
    }

    public function test_user_can_view_uas_edit_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('uas-reports.edit', $this->student->id));

        $response->assertStatus(200);
        $response->assertSee('Adara Adelia Putri');
        $response->assertSee('Pendidikan Pancasila');
        $response->assertSee('Bahasa Indonesia');
        $response->assertSee('Salin Nilai dari UTS');
    }

    public function test_user_can_store_uas_grades_ekskul_and_notes(): void
    {
        $payload = [
            'academic_year_id' => $this->academicYear->id,
            'grades' => [
                $this->subject1->id => [
                    'nilai_akhir' => 90,
                    'capaian_tertinggi' => 'Peserta didik mampu menjelaskan proses lahirnya Pancasila',
                    'capaian_perlu_peningkatan' => null,
                ],
                $this->subject2->id => [
                    'nilai_akhir' => 88,
                    'capaian_tertinggi' => 'Mampu memahami teks deskripsi dengan cermat',
                    'capaian_perlu_peningkatan' => 'Mengenal kalimat langsung dan tidak langsung',
                ],
            ],
            'ekskul' => [
                [
                    'kegiatan' => 'Pramuka',
                    'predikat' => 'Baik',
                    'keterangan' => 'Aktif mengikuti kegiatan kepramukaan mingguan',
                ],
            ],
            'attendance' => [
                'sakit' => 1,
                'izin' => 0,
                'alpa' => 0,
            ],
            'note' => [
                'fase' => 'D',
                'kokurikuler_deskripsi' => 'Ananda aktif dan berkolaborasi baik dalam projek suara demokrasi.',
                'catatan_wali_kelas' => 'Tingkatkan prestasimu dan tetap giat belajar.',
                'tanggal_raport' => 'Karawang, 22 Desember 2026',
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('uas-reports.store', $this->student->id), $payload);

        $response->assertRedirect(route('uas-reports.edit', [
            'student' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
        ]));

        $this->assertDatabaseHas('student_uas_grades', [
            'student_id' => $this->student->id,
            'subject_id' => $this->subject1->id,
            'nilai_akhir' => 90,
            'academic_year_id' => $this->academicYear->id,
        ]);

        $this->assertDatabaseHas('student_extracurriculars', [
            'student_id' => $this->student->id,
            'kegiatan' => 'Pramuka',
            'academic_year_id' => $this->academicYear->id,
        ]);

        $this->assertDatabaseHas('student_uas_notes', [
            'student_id' => $this->student->id,
            'fase' => 'D',
            'tanggal_raport' => 'Karawang, 22 Desember 2026',
        ]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'sakit' => 1,
            'academic_year_id' => $this->academicYear->id,
        ]);
    }

    public function test_user_can_preview_uas_report_card(): void
    {
        StudentUasGrade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject1->id,
            'academic_year_id' => $this->academicYear->id,
            'nilai_akhir' => 90,
            'capaian_tertinggi' => 'Memahami proses lahirnya Pancasila',
        ]);

        StudentExtracurricular::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'kegiatan' => 'Pramuka',
            'keterangan' => 'Aktif dalam kepramukaan',
            'order_number' => 1,
        ]);

        StudentUasNote::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'fase' => 'D',
            'kokurikuler_deskripsi' => 'Menunjukkan capaian yang baik dalam dimensi kemandirian',
            'catatan_wali_kelas' => 'Terus pertahankan semangat belajarmu!',
            'tanggal_raport' => 'Karawang, 22 Desember 2026',
        ]);

        $response = $this->actingAs($this->user)->get(route('uas-reports.preview', $this->student->id));

        $response->assertStatus(200);
        $response->assertSee('LAPORAN HASIL BELAJAR');
        $response->assertSee('ADARA ADELIA PUTRI');
        $response->assertSee('Pendidikan Pancasila');
        $response->assertSee('90');
        $response->assertSee('Pramuka');
        $response->assertSee('Kokurikuler');
        $response->assertSee('Ketidakhadiran');
        $response->assertSee('Catatan Wali Kelas');
        $response->assertSee('Halaman : 1');
        $response->assertSee('Halaman : 2');
        $response->assertSee('Halaman : 3');
    }

    public function test_user_can_view_print_all_uas_reports(): void
    {
        $response = $this->actingAs($this->user)->get(route('uas-reports.print-all'));

        $response->assertStatus(200);
        $response->assertSee('LAPORAN HASIL BELAJAR');
        $response->assertSee('ADARA ADELIA PUTRI');
    }

    public function test_uas_report_preview_numbers_subjects_sequentially_without_gaps(): void
    {
        // Add additional subjects up to 17 subjects total
        for ($i = 3; $i <= 17; $i++) {
            Subject::create([
                'name' => "Mata Pelajaran {$i}",
                'code' => "MP{$i}",
                'order_number' => $i,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($this->user)->get(route('uas-reports.preview', $this->student->id));
        $response->assertStatus(200);

        // Page 1 has 9 subjects, Page 2 has 8 subjects (10 to 17)
        // Verify sequential numbering 9, 10, 11... 17 exists and there is no skip to 19
        $content = $response->getContent();
        $this->assertStringContainsString('<td style="text-align: center; vertical-align: middle;">9</td>', $content);
        $this->assertStringContainsString('<td style="text-align: center; vertical-align: middle;">10</td>', $content);
        $this->assertStringContainsString('<td style="text-align: center; vertical-align: middle;">17</td>', $content);
        $this->assertStringNotContainsString('<td style="text-align: center; vertical-align: middle;">18</td>', $content);
        $this->assertStringNotContainsString('<td style="text-align: center; vertical-align: middle;">19</td>', $content);
    }
}
