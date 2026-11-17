<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentUasGrade extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'nilai_akhir' => 'float',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Format deskripsi Capaian Kompetensi gabungan resmi Kurikulum Merdeka
     */
    public function getFormattedCapaianAttribute(): string
    {
        $parts = [];
        if (! empty($this->capaian_tertinggi)) {
            $parts[] = 'Mencapai Kompetensi dengan sangat baik dalam hal '.rtrim(trim($this->capaian_tertinggi), '.').'.';
        }
        if (! empty($this->capaian_perlu_peningkatan)) {
            $parts[] = 'Perlu peningkatan dalam hal '.rtrim(trim($this->capaian_perlu_peningkatan), '.').'.';
        }

        return implode("\n", $parts);
    }
}
