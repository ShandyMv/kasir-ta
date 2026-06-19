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
        'lead_time',
        'hari_kedaluwarsa',
    ];

    protected function casts(): array
    {
        return [
            'stok_saat_ini' => 'decimal:2',
            'stok_minimum' => 'decimal:2',
            'stok_maksimum' => 'decimal:2',
            'safety_stock' => 'decimal:2',
            'reorder_point' => 'decimal:2',
            'lead_time' => 'integer',
            'hari_kedaluwarsa' => 'integer',
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
