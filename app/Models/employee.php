<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class employee extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'employeeID';
    protected $fillable = ['userID', 'personID', 'payment', 'schedule', 'admin_type'];
}
