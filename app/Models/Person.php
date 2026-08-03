<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;
    protected $table = 'persons';
    protected $primaryKey = 'personID';
    protected $fillable = ['name', 'last_name', 'rfc', 'phone_number'];

    public function employee(){
        return $this->hasOne(Employee::class, 'personID', 'personID');
    }
    public function client(){
        return $this->hasOne(Client::class, 'personID', 'personID');
    }
}
