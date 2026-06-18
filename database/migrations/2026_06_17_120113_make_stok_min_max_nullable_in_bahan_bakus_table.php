<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->decimal('stok_minimum', 10, 2)->nullable()->change();
            $table->decimal('stok_maksimum', 10, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->decimal('stok_minimum', 10, 2)->nullable(false)->default(0)->change();
            $table->decimal('stok_maksimum', 10, 2)->nullable(false)->default(0)->change();
        });
    }
};
