<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Loan extends Model
{
    protected $fillable = [
        'kode_peminjaman',
        'nama_peminjam',
        'user_id',
        'category_id',
        'inventory_id',
        'jumlah',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tanggal_kembali_aktual',
        'status',
        'alasan_tujuan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
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

    public function details(): HasMany
    {
        return $this->hasMany(LoanDetail::class, 'loan_id');
    }

    /**
     * Get array of all borrowed kode_barang from details relation
     */
    public function getDaftarKodeBarangAttribute(): array
    {
        if ($this->relationLoaded('details') && $this->details->isNotEmpty()) {
            return $this->details->pluck('kode_barang')->filter()->values()->all();
        }
        $codes = $this->details()->pluck('kode_barang')->filter()->values()->all();
        if (!empty($codes)) {
            return $codes;
        }
        if ($this->inventory && $this->inventory->kode_barang) {
            return [$this->inventory->kode_barang];
        }
        return [];
    }

    /**
     * Get total quantity of items borrowed
     */
    public function getJumlahBarangAttribute(): int
    {
        if ($this->jumlah && $this->jumlah > 0) {
            return $this->jumlah;
        }
        if ($this->relationLoaded('details') && $this->details->isNotEmpty()) {
            return $this->details->count();
        }
        return 1;
    }

    /**
     * Get summary text of categories in this loan, e.g. "Laptop (2 Unit), Proyektor (1 Unit)"
     */
    public function getCategorySummaryAttribute(): string
    {
        if ($this->relationLoaded('details') && $this->details->isNotEmpty()) {
            $counts = [];
            foreach ($this->details as $detail) {
                $catName = $detail->inventory?->category?->nama_kategori;
                if ($catName) {
                    $counts[$catName] = ($counts[$catName] ?? 0) + 1;
                }
            }
            if (!empty($counts)) {
                $parts = [];
                foreach ($counts as $name => $count) {
                    $parts[] = "{$name} ({$count} Unit)";
                }
                return implode(', ', $parts);
            }
        }

        $catName = $this->category?->nama_kategori ?? ($this->inventory?->category?->nama_kategori ?? 'Inventaris');
        $qty = $this->jumlah_barang;
        return "{$catName} ({$qty} Unit)";
    }

    /**
     * Get unique list of category names in this loan (comma-separated), e.g. "Laptop, Proyektor"
     */
    public function getAllCategoriesStringAttribute(): string
    {
        $categories = collect();

        if ($this->relationLoaded('details') ? $this->details->isNotEmpty() : $this->details()->exists()) {
            $details = $this->relationLoaded('details') ? $this->details : $this->details()->with('inventory.category')->get();
            $categories = $details->map(function ($d) {
                return $d->inventory?->category?->nama_kategori;
            })->filter()->unique()->values();
        }

        if ($categories->isEmpty()) {
            if ($this->category?->nama_kategori) {
                $categories->push($this->category->nama_kategori);
            } elseif ($this->inventory?->category?->nama_kategori) {
                $categories->push($this->inventory->category->nama_kategori);
            }
        }

        return $categories->isNotEmpty() ? $categories->implode(', ') : ($this->category->nama_kategori ?? 'Barang');
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

    /**
     * Count of active (currently borrowed) units in this loan
     */
    public function getActiveDetailsCountAttribute(): int
    {
        if ($this->relationLoaded('details')) {
            return $this->details->where('status', 'dipinjam')->count();
        }
        return $this->details()->where('status', 'dipinjam')->count();
    }

    /**
     * Count of returned units in this loan
     */
    public function getFinishedDetailsCountAttribute(): int
    {
        if ($this->relationLoaded('details')) {
            return $this->details->where('status', 'selesai')->count();
        }
        return $this->details()->where('status', 'selesai')->count();
    }

    /**
     * Check if loan is partially returned (some finished, some still active)
     */
    public function getIsPartiallyReturnedAttribute(): bool
    {
        $active = $this->active_details_count;
        $finished = $this->finished_details_count;
        return $active > 0 && $finished > 0;
    }
}
