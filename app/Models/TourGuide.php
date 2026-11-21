<?php

namespace App\Models;

use App\Support\MediaUrl;
use Database\Factories\TourGuideFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourGuide extends Model
{
    /** @use HasFactory<TourGuideFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role',
        'bio',
        'photo',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function photoUrl(): string
    {
        return MediaUrl::resolve($this->photo, '/images/seed/guide-1.jpg');
    }
}
