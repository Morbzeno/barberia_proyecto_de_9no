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
        Schema::create('sells', function (Blueprint $table) {
            $table->id('sellID');
            $table->unsignedBigInteger('cartID');
            $table->foreign('cartID')->references('cartID')->on('carts')->onDelete('cascade');
            $table->unsignedBigInteger('directionID');
            $table->foreign('directionID')->references('directionID')->on('directions')->onDelete('cascade');
            $table->unsignedBigInteger('clientID');
            $table->foreign('clientID')->references('clientID')->on('clients')->onDelete('cascade');
            $table->decimal('total', 10, 2 );
            $table->decimal('iva', 10, 2 );
            $table->string('purchase_method')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sells');
    }
};
