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
        'tanggal_kembali_aktual',
        'status',
        'alasan_tujuan',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali' => 'datetime',
        'tanggal_kembali_aktual' => 'datetime',
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

    /**
     * Check if loan is late (either currently overdue or returned past schedule)
     */
    public function getIsTerlambatAttribute(): bool
    {
        if ($this->status === 'dipinjam') {
            return Carbon::now()->greaterThan($this->tanggal_kembali);
        }
        if ($this->status === 'selesai' && $this->tanggal_kembali_aktual) {
            return $this->tanggal_kembali_aktual->greaterThan($this->tanggal_kembali);
        }
        return false;
    }
}
