<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentDetail extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'appointmentDetailID';
    protected $fillable = ['appointmentID', 'serviceID', 'totalPrice'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'serviceID', 'servicesID');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointmentID', 'appointmentID');
    }

    

}
