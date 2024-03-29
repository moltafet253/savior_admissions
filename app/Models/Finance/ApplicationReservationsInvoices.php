<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationReservationsInvoices extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'application_reservations_invoices';

    public function applicationReservationInformation()
    {
        return $this->belongsTo(ApplicationReservationsInvoices::class, 'a_reservation_id', 'id');
    }
}
