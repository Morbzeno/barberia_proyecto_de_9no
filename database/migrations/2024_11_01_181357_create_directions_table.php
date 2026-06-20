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
        Schema::create('directions', function (Blueprint $table) {
            $table->id('directionID');
            $table->bigInteger('userID')->unsigned();
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            $table->string('state', 100);
            $table->string('city', 100);
            $table->string('residence', 100);
            $table->string('name', 100);
            $table->integer('postal_code');
            $table->string('description', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directions');
    }
};
