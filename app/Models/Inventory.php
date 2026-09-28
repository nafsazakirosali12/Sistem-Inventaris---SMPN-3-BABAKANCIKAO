<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'category_id',
        'room_id',
        'status',
        'nomor_register',
        'merk_type',
        'ukuran_cc',
        'bahan',
        'tahun_pembelian',
        'asal_usul',
        'harga',
        'keterangan',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
