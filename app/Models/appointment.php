<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class appointment extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'appointmentID';
    protected $fillable = ['clientID', 'employeeID', 'date', 'startHour', 'finishHour', 'notes', 'status'];
}
