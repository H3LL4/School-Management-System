<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    public const METHODS  = ['Cash', 'Bank Transfer', 'Card'];
    public const STATUSES = ['Paid', 'Pending', 'Refunded'];

    protected $fillable = ['enrollment_id', 'reference', 'amount', 'method', 'status', 'paid_at', 'note'];

    protected function casts(): array
    {
        return [
            'amount'  => 'decimal:2',
            'paid_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        // Every transaction gets a readable reference such as PAY-261031-K3X9Q
        static::creating(function (Payment $payment) {
            $payment->reference ??= 'PAY-' . now()->format('ymd') . '-' . Str::upper(Str::random(5));
        });
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'Paid'    => 'success',
            'Pending' => 'warning',
            default   => 'secondary',
        };
    }
}
