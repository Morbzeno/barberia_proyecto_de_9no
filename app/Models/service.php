<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'serviceID';
    protected $fillable = ['name', 'description', 'price', 'aproxDuration'];

    public function chair_services()
    {
        return $this->hasMany(ChairService::class, 'serviceID', 'serviceID');
    }

    public function appointment_details()
    {
        return $this->hasMany(AppointmentDetail::class, 'serviceID', 'serviceID');
    }

}
