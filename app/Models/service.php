<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class service extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'servicesID';
    protected $fillable = ['name', 'description', 'price', 'aproxDuration'];
}
