<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tour_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'travel_date',
        'guests',
        'total_price',
        'special_requests',
        'status',
        'admin_notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
            'guests' => 'integer',
            'total_price' => 'decimal:2',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function forCustomer(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $sub) use ($user): void {
            $sub->where('user_id', $user->id)
                ->orWhereRaw('LOWER(customer_email) = ?', [Str::lower($user->email)]);
        });
    }

    public function belongsToCustomer(User $user): bool
    {
        return $this->user_id === $user->id || strcasecmp($this->customer_email, $user->email) === 0;
    }
}
