<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_supplier',
        'telepon',
        'alamat',
    ];

    public function stokMasuks()
    {
        return $this->hasMany(StokMasuk::class);
    }
}
