<?php

namespace App\Models;

use App\Support\MediaUrl;
use Database\Factories\TourCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourCategory extends Model
{
    /** @use HasFactory<TourCategoryFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'image',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
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
    protected function roots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return list<int>
     */
    public function descendantIds(): array
    {
        if (! $this->exists) {
            return [];
        }

        $parentsById = static::query()->pluck('parent_id', 'id')->all();
        $childrenMap = [];

        foreach ($parentsById as $id => $parentId) {
            if ($parentId !== null) {
                $childrenMap[(int) $parentId][] = (int) $id;
            }
        }

        $descendants = [];
        $visited = [(int) $this->id => true];
        $queue = $childrenMap[(int) $this->id] ?? [];

        while ($queue !== []) {
            $currentId = array_shift($queue);
            if (isset($visited[$currentId])) {
                continue;
            }

            $visited[$currentId] = true;
            $descendants[] = $currentId;

            foreach ($childrenMap[$currentId] ?? [] as $childId) {
                if (! isset($visited[$childId])) {
                    $queue[] = $childId;
                }
            }
        }

        return $descendants;
    }

    public function displayNameWithParent(): string
    {
        if ($this->parent !== null) {
            return $this->parent->name.' › '.$this->name;
        }

        return $this->name;
    }

    public function imageUrl(): string
    {
        return MediaUrl::resolve($this->image, '/images/seed/cat-cruise.jpg');
    }
}
