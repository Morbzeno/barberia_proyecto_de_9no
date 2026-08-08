<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products_carts', 'sellID')) {

            Schema::table('products_carts', function (Blueprint $table) {
                $table->unsignedBigInteger('sellID')
                    ->nullable()
                    ->after('state');

                $table->foreign('sellID')
                    ->references('sellID')
                    ->on('sells')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products_carts', 'sellID')) {

            Schema::table('products_carts', function (Blueprint $table) {
                $table->dropForeign(['sellID']);
                $table->dropColumn('sellID');
            });
        }
    }
};
