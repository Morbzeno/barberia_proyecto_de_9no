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
        Schema::create('clients', function (Blueprint $table) {
            $table->id('clientID');
            $table->foreignId('userID');
            $table->foreignId('personID');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            $table->foreign('personID')->references('personID')->on('persons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
