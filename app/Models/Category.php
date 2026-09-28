<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'keterangan',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
