<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chair extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $table = 'chairs';
    protected $primaryKey = 'chairID';
    protected $fillable = ['chairName'];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'chairs_services', 'chairID', 'serviceID', 'chairID', 'serviceID');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'chairID', 'chairID');
    }
}
