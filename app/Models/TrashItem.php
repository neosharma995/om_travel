<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrashItem extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'item_type',
        'item_id',
        'title',
        'subtitle',
        'data',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    /**
     * @return class-string<Model>|null
     */
    public function modelClass(): ?string
    {
        return match ($this->item_type) {
            'booking' => Booking::class,
            'inquiry' => Inquiry::class,
            'tour' => Tour::class,
            'destination' => Destination::class,
            'blog_post' => BlogPost::class,
            'category' => TourCategory::class,
            'guide' => TourGuide::class,
            'testimonial' => Testimonial::class,
            'gallery' => GalleryItem::class,
            'activity' => Activity::class,
            'tour_type' => TourType::class,
            default => null,
        };
    }

    public function typeLabel(): string
    {
        return match ($this->item_type) {
            'booking' => 'Booking',
            'inquiry' => 'Inquiry',
            'tour' => 'Tour Package',
            'destination' => 'Destination',
            'blog_post' => 'Blog Article',
            'category' => 'Tour Category',
            'guide' => 'Tour Guide',
            'testimonial' => 'Testimonial',
            'gallery' => 'Gallery Item',
            'activity' => 'Activity',
            'tour_type' => 'Tour Type',
            default => ucfirst(str_replace('_', ' ', $this->item_type)),
        };
    }

    public static function itemTypeFor(Model $model): string
    {
        return match ($model::class) {
            Booking::class => 'booking',
            Inquiry::class => 'inquiry',
            Tour::class => 'tour',
            Destination::class => 'destination',
            BlogPost::class => 'blog_post',
            TourCategory::class => 'category',
            TourGuide::class => 'guide',
            Testimonial::class => 'testimonial',
            GalleryItem::class => 'gallery',
            Activity::class => 'activity',
            TourType::class => 'tour_type',
            default => strtolower(class_basename($model)),
        };
    }

    public static function trash(Model $model, ?string $title = null, ?string $subtitle = null): self
    {
        $type = static::itemTypeFor($model);

        if ($title === null) {
            $title = match ($type) {
                'booking' => ($model->customer_name ?? 'Traveller').' — '.($model->tour?->title ?? 'Journey Booking'),
                'inquiry' => ($model->name ?? 'Inquirer').' ('.($model->subject ?? 'Message').')',
                default => $model->title ?? $model->name ?? (class_basename($model).' #'.$model->getKey()),
            };
        }

        if ($subtitle === null) {
            $subtitle = match ($type) {
                'booking' => 'Total: ₹'.number_format((float) ($model->total_price ?? 0), 2).' • '.($model->travel_date ? (is_string($model->travel_date) ? $model->travel_date : $model->travel_date->format('M d, Y')) : 'Date N/A'),
                'inquiry' => ($model->email ?? '').' • Status: '.($model->status ?? 'new'),
                'tour' => '₹'.number_format((float) ($model->price ?? 0), 0).' • '.($model->destination?->name ?? 'Himachal'),
                'destination' => ($model->country ?? 'India').' • '.($model->listings_label ?? 'Destination'),
                'blog_post' => ($model->category ?? 'Blog').' • '.($model->read_time ?? 'Read'),
                'guide' => ($model->role ?? 'Tour Guide'),
                'testimonial' => ($model->client_role ?? 'Client').' • Rating: '.($model->rating ?? 5).'/5',
                'category' => 'Order: '.($model->sort_order ?? 0),
                'activity' => 'Activity • Order: '.($model->sort_order ?? 0),
                'tour_type' => 'Tour Type • Order: '.($model->sort_order ?? 0),
                'gallery' => ($model->location ?? 'Gallery Photo'),
                default => null,
            };
        }

        $trashItem = static::query()->create([
            'item_type' => $type,
            'item_id' => $model->getKey(),
            'title' => (string) $title,
            'subtitle' => $subtitle,
            'data' => $model->getAttributes(),
        ]);

        $model->delete();

        return $trashItem;
    }

    public function restoreItem(): ?Model
    {
        $class = $this->modelClass();
        if ($class === null) {
            $this->delete();

            return null;
        }

        $attributes = $this->data ?? [];

        $model = $class::unguarded(function () use ($class, $attributes) {
            return $class::query()->updateOrCreate(
                ['id' => $this->item_id ?? ($attributes['id'] ?? null)],
                $attributes
            );
        });

        $this->delete();

        return $model;
    }

    public function purge(): void
    {
        $attributes = $this->data ?? [];
        if (! empty($attributes['image'])) {
            MediaUrl::deleteIfStored($attributes['image']);
        }
        if (! empty($attributes['photo'])) {
            MediaUrl::deleteIfStored($attributes['photo']);
        }
        if (! empty($attributes['avatar'])) {
            MediaUrl::deleteIfStored($attributes['avatar']);
        }

        $this->delete();
    }
}
