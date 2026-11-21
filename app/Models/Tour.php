<?php

namespace App\Models;

use App\Support\MediaUrl;
use Database\Factories\TourFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    /** @use HasFactory<TourFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'destination_id',
        'tour_category_id',
        'title',
        'slug',
        'location',
        'price',
        'duration_days',
        'duration_label',
        'rating',
        'reviews_count',
        'summary',
        'description',
        'highlights',
        'image',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_days' => 'integer',
            'rating' => 'decimal:1',
            'reviews_count' => 'integer',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TourCategory::class, 'tour_category_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function featured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function imageUrl(): string
    {
        return MediaUrl::resolve($this->image, '/images/seed/tour-greece.jpg');
    }

    /**
     * @return list<string>
     */
    public function highlightsList(): array
    {
        if ($this->highlights === null || trim($this->highlights) === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $this->highlights) ?: [];

        return array_values(array_filter(array_map('trim', $lines), fn (string $line): bool => $line !== ''));
    }

    public function displayDuration(): string
    {
        if ($this->duration_label !== null && trim($this->duration_label) !== '') {
            return $this->duration_label;
        }

        return $this->duration_days.' Days';
    }
}
