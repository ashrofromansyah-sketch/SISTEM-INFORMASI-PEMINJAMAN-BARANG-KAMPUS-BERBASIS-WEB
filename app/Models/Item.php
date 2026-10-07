<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    public const CONDITIONS = ['baik', 'rusak ringan', 'rusak berat'];

    protected $fillable = [
        'category_id',
        'item_code',
        'name',
        'description',
        'total_quantity',
        'available_quantity',
        'condition',
        'location',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'total_quantity' => 'integer',
            'available_quantity' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function loanDetails(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    /** Barang aktif dan tidak rusak berat boleh dipinjam. */
    public function isBorrowable(): bool
    {
        return $this->is_active && $this->condition !== 'rusak berat';
    }

    /**
     * Jumlah unit yang sudah dialokasikan pada suatu periode (BR-01).
     * Pengajuan berstatus "menunggu" tidak mengurangi stok.
     * Peminjaman "dipinjam" tetap dihitung selama belum dikembalikan,
     * termasuk yang sudah lewat jatuh tempo.
     */
    public function reservedBetween(string $start, string $end, ?int $exceptLoanId = null): int
    {
        return (int) LoanDetail::query()
            ->where('item_id', $this->id)
            ->whereHas('loan', function ($loan) use ($start, $end, $exceptLoanId) {
                $loan->whereIn('status', ['disetujui', 'dipinjam'])
                    ->whereDate('loan_date', '<=', $end)
                    ->where(function ($w) use ($start) {
                        $w->whereDate('due_date', '>=', $start)
                            ->orWhere('status', 'dipinjam');
                    });

                if ($exceptLoanId) {
                    $loan->where('id', '!=', $exceptLoanId);
                }
            })
            ->sum('quantity');
    }

    /** Jumlah unit yang masih bisa dipinjam pada periode tertentu. */
    public function availableBetween(string $start, string $end, ?int $exceptLoanId = null): int
    {
        return max(0, $this->total_quantity - $this->reservedBetween($start, $end, $exceptLoanId));
    }
}
