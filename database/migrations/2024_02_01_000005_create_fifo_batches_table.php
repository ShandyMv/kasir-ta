<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fifo_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_baku_id')->constrained()->onDelete('restrict');
            $table->foreignId('stok_masuk_id')->constrained()->onDelete('cascade');
            $table->string('batch_kode', 50);
            $table->decimal('jumlah_awal', 10, 2);
            $table->decimal('sisa_stok', 10, 2);
            $table->date('tanggal_masuk');
            $table->timestamps();

            $table->index(['bahan_baku_id', 'tanggal_masuk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fifo_batches');
    }
};
