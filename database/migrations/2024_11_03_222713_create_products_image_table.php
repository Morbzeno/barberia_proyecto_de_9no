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
        Schema::create('products_images', function (Blueprint $table) {
            $table->id('productImageID');
            $table->unsignedBigInteger('imageID');
            $table->unsignedBigInteger('productID');
            $table->foreign('imageID')->references('imageID')->on('images')->onDelete('cascade');
            $table->foreign('productID')->references('productID')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_image');
    }
};
