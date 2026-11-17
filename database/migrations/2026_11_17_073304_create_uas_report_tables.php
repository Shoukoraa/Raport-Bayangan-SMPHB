<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_uas_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->text('capaian_tertinggi')->nullable();
            $table->text('capaian_perlu_peningkatan')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'academic_year_id'], 'student_subject_uas_unique');
        });

        Schema::create('student_extracurriculars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('kegiatan');
            $table->string('predikat')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('order_number')->default(1);
            $table->timestamps();
        });

        Schema::create('student_uas_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('fase')->default('D');
            $table->text('kokurikuler_deskripsi')->nullable();
            $table->text('catatan_wali_kelas')->nullable();
            $table->string('tanggal_raport')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id'], 'student_uas_notes_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_uas_notes');
        Schema::dropIfExists('student_extracurriculars');
        Schema::dropIfExists('student_uas_grades');
    }
};
