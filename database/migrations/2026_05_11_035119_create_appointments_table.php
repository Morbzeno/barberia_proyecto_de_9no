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
            $table->date('date');
            $table->time('startHour', 0);
            $table->time('finishHour', 0);
            $table->enum('status', ['pending', 'in_process', 'cancelled', 'Finished']);
            $table->text('notes')->default('none');
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
