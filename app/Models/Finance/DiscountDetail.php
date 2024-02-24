<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountDetail extends Model
{
    use HasFactory;

    protected $table = 'discount_details';

    protected $fillable = [
        'name',
        'percentage',
        'status',
    ];
}
