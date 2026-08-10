<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sells', function (Blueprint $table) {
            $table->unsignedBigInteger('directionID')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('sells', function (Blueprint $table) {
            $table->unsignedBigInteger('directionID')
                ->nullable(false)
                ->change();
        });
    }
};