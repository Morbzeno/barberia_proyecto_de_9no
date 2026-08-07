<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'cartID';
    protected $fillable = [
        'clientID', 'total'
    ];
    public function client()
    {
        return $this->belongsTo(Client::class, 'clientID', 'clientID');
    }

    // Relación original
    public function productsCart()
    {
        return $this->hasMany(ProductsCart::class, 'cartID', 'cartID');
    }

    // Alias para la App Android
    public function products_cart()
    {
        return $this->hasMany(ProductsCart::class, 'cartID', 'cartID');
    }

    public function sell()
    {
        return $this->hasOne(Sell::class, 'sellID', 'sellID');
    }
}
