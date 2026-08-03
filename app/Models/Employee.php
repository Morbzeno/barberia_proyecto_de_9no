<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'employeeID';
    protected $fillable = ['userID', 'personID', 'payment', 'schedule', 'admin_type', 'rfc'];

    public function user(){
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

    public function person(){
        return $this->belongsTo(Person::class, 'personID', 'personID');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'employeeID', 'employeeID');
    }
    protected $casts = [
        'schedule' => 'array', // 👈 ¡ESTO ES LA CLAVE!
    ];
}
