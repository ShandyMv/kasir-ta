<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokKeluarDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stok_keluar_id',
        'fifo_batch_id',
        'jumlah_ambil',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_ambil' => 'decimal:2',
        ];
    }

    public function stokKeluar()
    {
        return $this->belongsTo(StokKeluar::class);
    }

    public function fifoBatch()
    {
        return $this->belongsTo(FifoBatch::class);
    }
}
