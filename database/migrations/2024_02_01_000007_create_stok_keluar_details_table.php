<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_keluar_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_keluar_id')->constrained()->onDelete('cascade');
            $table->foreignId('fifo_batch_id')->constrained()->onDelete('restrict');
            $table->decimal('jumlah_ambil', 10, 2);
            $table->timestamps();

            $table->index(['stok_keluar_id', 'fifo_batch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_keluar_details');
    }
};
