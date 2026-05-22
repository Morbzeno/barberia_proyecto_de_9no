<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chair extends Model
{
    protected $table = 'chairs';
    protected $primaryKey = 'chairID';
    protected $fillable = ['chairName'];

    public function chair_services()
    {
        return $this->hasMany(ChairService::class, 'chairID', 'chairID');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'chairID', 'chairID');
    }
}
