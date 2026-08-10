<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'paymentID';

    protected $fillable = [
        'appointmentID',
        'sellID',
        'subtotal',
        'paymentMethod',
        'paypalOrderID',
        'paypalCaptureID',
        'currency',
        'status'
    ];


    // =====================================================
    // CITA
    // =====================================================

    public function appointment()
    {
        return $this->belongsTo(
            Appointment::class,
            'appointmentID',
            'appointmentID'
        );
    }


    // =====================================================
    // VENTA
    // =====================================================

    public function sell()
    {
        return $this->belongsTo(
            Sell::class,
            'sellID',
            'sellID'
        );
    }
}