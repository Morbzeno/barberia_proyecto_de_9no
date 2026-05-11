<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class person extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'personID';
    protected $fillable = ['name', 'last_name', 'rfc', 'phone_number'];
}
