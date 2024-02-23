<?php

namespace App\Models\Finance;

use App\Models\Catalogs\AcademicYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $table = 'discounts';

    protected $fillable = [
        'academic_year',
        'name',
        'percentage',
        'status',
    ];

    public function academicYearInfo()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year', 'id');
    }
}
