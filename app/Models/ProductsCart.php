<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductsCart extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'productsCartId';
    protected $fillable = [
    'cartID',
    'productID',
    'quantity',
    'subtotal',
    'state',
    'sellID'
];
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cartID', 'cartID');
    }
    public function producto()
    {
        return $this->belongsTo(Product::class, 'productID', 'productID'); 
    }
    public function sell()
    {
        return $this->belongsTo(Sell::class, 'sellID', 'sellID');
    }
}
