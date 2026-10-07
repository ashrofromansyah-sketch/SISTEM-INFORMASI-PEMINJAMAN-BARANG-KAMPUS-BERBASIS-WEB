<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'loan_id',
        'title',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Kirim notifikasi dalam aplikasi ke satu atau banyak pengguna.
     *
     * @param  int|array<int>  $userIds
     */
    public static function send(int|array $userIds, string $title, string $message, ?int $loanId = null): void
    {
        foreach ((array) $userIds as $userId) {
            static::create([
                'user_id' => $userId,
                'loan_id' => $loanId,
                'title' => $title,
                'message' => $message,
            ]);
        }
    }
}
