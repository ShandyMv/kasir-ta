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
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->decimal('safety_stock', 10, 2)->default(0)->after('stok_maksimum');
            $table->decimal('reorder_point', 10, 2)->default(0)->after('safety_stock');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->dropColumn(['safety_stock', 'reorder_point']);
        });
    }
};
