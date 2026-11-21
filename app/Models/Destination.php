<?php

namespace App\Models;

use App\Support\MediaUrl;
use Database\Factories\DestinationFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    /** @use HasFactory<DestinationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'country',
        'description',
        'image',
        'listings_label',
        'is_popular',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function popular(Builder $query): Builder
    {
        return $query->where('is_popular', true);
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

    public function displayListingsCount(): string
    {
        if ($this->listings_label !== null && trim($this->listings_label) !== '') {
            return $this->listings_label;
        }

        $count = $this->tours_count ?? $this->tours()->active()->count();

        return $count.' Listing';
    }
}
