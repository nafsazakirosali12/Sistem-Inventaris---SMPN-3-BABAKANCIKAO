<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanDetail extends Model
{
    protected $fillable = [
        'loan_id',
        'inventory_id',
        'kode_barang',
        'status',
        'tanggal_kembali_aktual',
    ];

    protected $casts = [
        'tanggal_kembali_aktual' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
