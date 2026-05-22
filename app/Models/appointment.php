<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'appointmentID';
    protected $fillable = ['clientID', 'employeeID', 'startHour', 'finishHour', 'notes', 'status'];
    
    public function client()
    {
        return $this->belongsTo(User::class, 'clientID', 'userID');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeID', 'employeeID');
    }
    public function appointmentDetails()
    {
        return $this->hasMany(AppointmentDetail::class, 'appointmentID', 'appointmentID');
    }
    public function payment()
    {
        return $this->hasOne(Payment::class, 'appointmentID', 'appointmentID');
    }

    public function chair()
    {
        return $this->belongsTo(Chair::class, 'chairID', 'chairID');
    }
}

