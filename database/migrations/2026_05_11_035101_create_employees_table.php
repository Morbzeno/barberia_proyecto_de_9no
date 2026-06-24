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
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employeeID');
            $table->foreignId('userID');
            $table->foreignId('personID');
            $table->decimal('payment', 10, 2);
            $table->json('schedule');
            $table->string('rfc');
            $table->enum('admin_type', ['barber', 'admin'])->default('barber');
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            $table->foreign('personID')->references('personID')->on('persons')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
