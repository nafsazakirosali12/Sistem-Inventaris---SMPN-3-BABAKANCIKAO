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

    /**
     * Total physical units available for borrowing (status baik and not currently dipinjam)
     */
    public function getAvailableStockAttribute(): int
    {
        return $this->inventories()
            ->where('status', 'baik')
            ->whereDoesntHave('loans', function ($q) {
                $q->where('status', 'dipinjam');
            })
            ->count();
    }

    /**
     * List of physical units currently available for borrowing
     */
    public function getAvailableInventoriesAttribute()
    {
        return $this->inventories()
            ->where('status', 'baik')
            ->whereDoesntHave('loans', function ($q) {
                $q->where('status', 'dipinjam');
            })
            ->orderBy('kode_barang', 'asc')
            ->get();
    }
}
