<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FifoBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_baku_id',
        'stok_masuk_id',
        'batch_kode',
        'jumlah_awal',
        'sisa_stok',
        'tanggal_masuk',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_awal' => 'decimal:2',
            'sisa_stok' => 'decimal:2',
            'tanggal_masuk' => 'date',
        ];
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }

    public function stokMasuk()
    {
        return $this->belongsTo(StokMasuk::class);
    }

    public function stokKeluarDetails()
    {
        return $this->hasMany(StokKeluarDetail::class);
    }
}
