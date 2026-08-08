<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sells', 'status')) {
            Schema::table('sells', function (Blueprint $table) {
                $table->string('status')
                    ->default('paid')
                    ->after('purchase_method');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sells', 'status')) {
            Schema::table('sells', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
