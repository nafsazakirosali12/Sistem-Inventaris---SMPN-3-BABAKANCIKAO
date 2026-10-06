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
        'foto',
        'category_id',
        'room_id',
        'status',
        'status_unit',
        'nomor_register',
        'merk_type',
        'ukuran_cc',
        'bahan',
        'tahun_pembelian',
        'nomor_pabrik',
        'nomor_rangka',
        'nomor_mesin',
        'nomor_polisi',
        'nomor_bpkb',
        'asal_usul',
        'harga',
        'deskripsi',
        'keterangan',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
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

    /**
     * Status Unit Label (Tersedia, Dipinjam, Rusak, Perlu Perbaikan, Hilang)
     */
    public function getStatusUnitAttribute(): string
    {
        return strtolower($this->status ?? 'baik');
    }

    public function setStatusUnitAttribute($value): void
    {
        $this->attributes['status'] = strtolower((string)$value);
    }

    /**
     * Mutator for Harga: ensures numeric storage without 'Rp' prefix or formatting artifacts
     */
    public function setHargaAttribute($value): void
    {
        if (is_string($value)) {
            // Remove 'Rp', dots, spaces
            $cleaned = preg_replace('/[^0-9]/', '', $value);
            $this->attributes['harga'] = is_numeric($cleaned) && $cleaned !== '' ? (float)$cleaned : 0;
        } else {
            $this->attributes['harga'] = is_numeric($value) ? (float)$value : 0;
        }
    }

    /**
     * Clean Indonesian formatted currency string: e.g. "Rp 5.000.000"
     */
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga ?? 0, 0, ',', '.');
    }

    /**
     * Get photo URL or null if file not found
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto && file_exists(public_path($this->foto))) {
            return asset($this->foto);
        }
        return null;
    }

    /**
     * Status Unit Label (Baik/Tersedia, Dipinjam, Rusak, Perlu Perbaikan, Hilang)
     */
    public function getStatusLabelAttribute(): string
    {
        $status = strtolower($this->status ?? 'baik');
        return match ($status) {
            'dipinjam'        => 'Dipinjam',
            'rusak'           => 'Rusak',
            'perlu_perbaikan' => 'Perlu Perbaikan',
            'hilang'          => 'Hilang',
            default           => 'Tersedia',
        };
    }

    /**
     * Status Unit badge CSS classes
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $status = strtolower($this->status ?? 'baik');
        return match ($status) {
            'dipinjam'        => 'bg-[#DBEAFE] text-[#1E40AF]',
            'rusak'           => 'bg-[#FEE2E2] text-[#991B1B]',
            'perlu_perbaikan' => 'bg-[#FEF3C7] text-[#92400E]',
            'hilang'          => 'bg-[#F3F4F6] text-[#374151]',
            default           => 'bg-[#DCFCE7] text-[#166534]',
        };
    }

    /**
     * Status Unit dot color class
     */
    public function getStatusDotClassAttribute(): string
    {
        $status = strtolower($this->status ?? 'baik');
        return match ($status) {
            'dipinjam'        => 'bg-[#2563EB]',
            'rusak'           => 'bg-[#DC2626]',
            'perlu_perbaikan' => 'bg-[#F59E0B]',
            'hilang'          => 'bg-[#6B7280]',
            default           => 'bg-[#16A34A]',
        };
    }

    /**
     * Check if unit is currently borrowed based on active loan transactions in loans table
     */
    public function getIsCurrentlyBorrowedAttribute(): bool
    {
        return $this->loans()->where('status', 'dipinjam')->exists();
    }
}
