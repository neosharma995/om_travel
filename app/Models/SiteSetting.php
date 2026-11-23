<?php

namespace App\Models;

use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'site_name' => 'OmTravel',
            'site_tagline' => 'Explore World',
            'topbar_address' => '45 New Eskaton Road, Austria',
            'topbar_hours' => 'Sun to Friday: 8.00 am - 7.00 pm',
            'contact_phone' => '+01 234 567 890',
            'contact_phone_alt' => '+09 876 543 210',
            'contact_email' => 'info@omtravel.com',
            'contact_address' => '789 Inner Lane, Holy Park, California, USA',
            'footer_about' => 'Curating unforgettable journeys across the majestic snow peaks, pine valleys, and sacred heritage of Himachal Pradesh with dedicated local travel specialists and seamless itineraries.',
            'hero_subtitle' => 'Get unforgettable pleasure with us',
            'hero_title' => 'Natural Wonder of the world',
            'hero_primary_cta' => 'Explore Tours',
            'hero_secondary_cta' => 'Our Services',
            'hero_image' => '/images/himachal/manali.jpg',
            'about_subtitle' => "Let's Go Together",
            'about_title' => 'Plan Your Trip With Us',
            'about_description' => 'From the snow-crowned summits of Manali to the high-altitude cold deserts of Spiti and the tranquil deodar groves of Dalhousie, our specialists design every Himachal itinerary with safety, comfort, and deep local insight.',
            'about_feature_1_title' => 'Curated Mountain Stays',
            'about_feature_1_text' => 'Handpicked boutique cedar chalets, heritage colonial lodges, and luxury riverside camps with warm Himachali hospitality.',
            'about_feature_2_title' => 'Certified Mountain Guides',
            'about_feature_2_text' => 'Certified regional mountaineers who unlock hidden Himalayan viewpoints, alpine trails, and worry-free exploration.',
            'about_image_1' => '/images/himachal/about-1.jpg',
            'about_image_2' => '/images/himachal/about-2.jpg',
            'about_image_3' => '/images/himachal/about-3.jpg',
            'stat_1_value' => '12',
            'stat_1_label' => 'Years Experience',
            'stat_2_value' => '97%',
            'stat_2_label' => 'Retention Rate',
            'stat_3_value' => '8k',
            'stat_3_label' => 'Tour Completed',
            'stat_4_value' => '19k',
            'stat_4_label' => 'Happy Travellers',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function allKeyed(): array
    {
        $defaults = static::defaults();
        $stored = static::query()->pluck('value', 'key')->all();

        foreach ($stored as $key => $value) {
            if ($value !== null && $value !== '') {
                $defaults[$key] = $value;
            }
        }

        return $defaults;
    }

    /**
     * @param  array<string, string|null>  $pairs
     */
    public static function setMany(array $pairs, string $group = 'general'): void
    {
        foreach ($pairs as $key => $value) {
            static::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group,
                ]
            );
        }
    }
}
