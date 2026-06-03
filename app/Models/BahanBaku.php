<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BahanBaku extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'satuan_id',
        'stok_saat_ini',
        'stok_minimum',
        'stok_maksimum',
    ];

    protected function casts(): array
    {
        return [
            'stok_saat_ini' => 'decimal:2',
            'stok_minimum' => 'decimal:2',
            'stok_maksimum' => 'decimal:2',
        ];
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    public function stokMasuks()
    {
        return $this->hasMany(StokMasuk::class);
    }

    public function stokKeluars()
    {
        return $this->hasMany(StokKeluar::class);
    }

    public function fifoBatches()
    {
        return $this->hasMany(FifoBatch::class);
    }
}
