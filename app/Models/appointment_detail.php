<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class appointment_detail extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'appointmentDetailID';
    protected $fillable = ['appointmentID', 'serviceID', 'totalPrice'];
}
