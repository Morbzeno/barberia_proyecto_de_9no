<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class client extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'clientID';
    protected $fillable = ['userID', 'personID'];
}
