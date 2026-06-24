<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products_carts', function (Blueprint $table) {
            $table->id('productsCartId');
            $table->unsignedBigInteger('cartID');
            $table->foreign('cartID')->references('cartID')->on('carts')->onDelete('cascade');
            $table->unsignedBigInteger('productID');
            $table->foreign('productID')->references('productID')->on('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2 );
            $table->enum('state', ['waiting', 'sell'])->default('waiting');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_carts');
    }
};
