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
        Schema::create('appointment_details', function (Blueprint $table) {
            $table->id('appointmentDetailID');
            $table->foreignId('appointmentID');
            $table->foreignId('serviceID');
            $table->decimal('totalPrice', 8, 2);
            $table->foreign('appointmentID')->references('appointmentID')->on('appointments')->onDelete('cascade');
            $table->foreign('serviceID')->references('serviceID')->on('services')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_details');
    }
};
