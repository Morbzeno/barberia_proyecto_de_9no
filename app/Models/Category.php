<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'categoryID';

    protected $fillable = [
        "name",
        "description",
        "tags",

    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'productID', 'productID');
    }
protected $casts = [
        'tags' => 'array', // <--- Añade esta línea
    ];
}
