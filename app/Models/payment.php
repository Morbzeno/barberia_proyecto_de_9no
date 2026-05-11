<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class payment extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'paymentID';
    protected $fillable = ['appointmentID', 'subtotal', 'paymentMethod'];

}
