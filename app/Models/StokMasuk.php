<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_baku_id',
        'supplier_id',
        'jumlah',
        'batch_kode',
        'tanggal_masuk',
        'user_id',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal_masuk' => 'date',
        ];
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fifoBatch()
    {
        return $this->hasOne(FifoBatch::class);
    }
}
