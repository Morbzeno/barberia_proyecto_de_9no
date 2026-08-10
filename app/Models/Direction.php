<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Direction extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'directionID';

    protected $fillable = [
        'userID',
        'state',
        'city',
        'postal_code',
        'name',
        'residence',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }
    public function sell()
    {
        return $this->hasMany(Sell::class, 'directionID', 'directionID');
    }
}
