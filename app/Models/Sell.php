<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sell extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'sellId';
    protected $fillable = [
        'cartID', 'clientID', 'directionID', 'total', 'iva', 'purchase_method'
    ];
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cartID', 'cartID');
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'clientID', 'clientID');
    }
    public function direction()
    {
        return $this->belongsTo(Direction::class, 'directionID', 'directionID');
    }
    public function productsCart()
    {
        return $this->hasMany(ProductsCart::class, 'productCartID', 'productCartID');
    }


}
