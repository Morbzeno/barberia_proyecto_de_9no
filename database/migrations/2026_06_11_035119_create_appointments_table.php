<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Event\Test\Finished;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointmentID');
            $table->foreignId('clientID');
            $table->foreignId('employeeID');
            $table->foreignId('chairID');
            $table->dateTime('startHour');
            $table->dateTime('finishHour');
            $table->enum('status', ['pending', 'in_process', 'cancelled', 'Finished']);
            $table->text('notes')->nullable();
            $table->foreign('clientID')->references('clientID')->on('clients')->onDelete('cascade');
            $table->foreign('employeeID')->references('employeeID')->on('employees')->onDelete('cascade');
            $table->foreign('chairID')->references('chairID')->on('chairs')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
