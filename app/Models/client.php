<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'clientID';
    protected $fillable = ['userID', 'personID'];

    public function user(){
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

    public function person(){
        return $this->belongsTo(Person::class, 'personID', 'personID');
    }

    public function appointments(){
        return $this->hasMany(Appointment::class, 'clientID', 'clientID');
    }
}
