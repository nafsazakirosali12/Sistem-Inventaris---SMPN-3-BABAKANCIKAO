<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Loan extends Model
{
    protected $fillable = [
        'kode_peminjaman',
        'nama_peminjam',
        'user_id',
        'category_id',
        'inventory_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'alasan_tujuan',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function getIsTerlambatAttribute(): bool
    {
        return $this->status === 'dipinjam' && Carbon::now()->greaterThan($this->tanggal_kembali);
    }
}
