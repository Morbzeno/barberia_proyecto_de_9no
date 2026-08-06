<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'productID';
    protected $table = 'products';

    protected $fillable = [
        'categoryID', 'name', 'sell_price', 'buy_price',
        'bar_code', 'stock', 'description', 'state', 'wholesale_price'
    ];

    // Relationship with the Image model
    public function images()
    {
        return $this->belongsToMany(Image::class, 'products_images', 'productID', 'imageID');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryID', 'categoryID');
    }
    public function productsCart()
    {
        return $this->hasMany(ProductsCart::class, 'productsCartId', 'productsCartId');
    }
}

