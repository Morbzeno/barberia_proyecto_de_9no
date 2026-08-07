<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'imageID';

    protected $table = 'images';

    protected $fillable = ['image'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_images', 'imageID', 'productID');
    }
}
