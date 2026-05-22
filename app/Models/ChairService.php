<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChairService extends Model
{
    protected $table = 'chairs_services';
    protected $primaryKey = 'chairServiceID';
    protected $fillable = ['chairID', 'serviceID'];

    public function chair()
    {
        return $this->belongsTo(Chair::class, 'chairID', 'chairID');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'serviceID', 'serviceID');
    }
}
