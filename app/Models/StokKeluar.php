<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_baku_id',
        'jumlah_keluar',
        'tanggal_keluar',
        'user_id',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_keluar' => 'decimal:2',
            'tanggal_keluar' => 'date',
        ];
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(StokKeluarDetail::class);
    }
}
