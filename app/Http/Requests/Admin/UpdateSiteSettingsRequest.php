<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:80'],
            'site_tagline' => ['required', 'string', 'max:80'],
            'topbar_address' => ['required', 'string', 'max:180'],
            'topbar_hours' => ['required', 'string', 'max:120'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'contact_phone_alt' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['required', 'email', 'max:150'],
            'contact_address' => ['required', 'string', 'max:255'],
            'footer_about' => ['required', 'string', 'max:500'],
            'hero_subtitle' => ['required', 'string', 'max:120'],
            'hero_title' => ['required', 'string', 'max:160'],
            'hero_primary_cta' => ['required', 'string', 'max:60'],
            'hero_secondary_cta' => ['required', 'string', 'max:60'],
            'hero_image' => ['nullable', 'string', 'max:500'],
            'hero_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'about_subtitle' => ['required', 'string', 'max:120'],
            'about_title' => ['required', 'string', 'max:160'],
            'about_description' => ['required', 'string', 'max:1000'],
            'about_feature_1_title' => ['required', 'string', 'max:120'],
            'about_feature_1_text' => ['required', 'string', 'max:350'],
            'about_feature_2_title' => ['required', 'string', 'max:120'],
            'about_feature_2_text' => ['required', 'string', 'max:350'],
            'about_image_1' => ['nullable', 'string', 'max:500'],
            'about_image_1_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'about_image_2' => ['nullable', 'string', 'max:500'],
            'about_image_2_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'about_image_3' => ['nullable', 'string', 'max:500'],
            'about_image_3_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'stat_1_value' => ['required', 'string', 'max:20'],
            'stat_1_label' => ['required', 'string', 'max:60'],
            'stat_2_value' => ['required', 'string', 'max:20'],
            'stat_2_label' => ['required', 'string', 'max:60'],
            'stat_3_value' => ['required', 'string', 'max:20'],
            'stat_3_label' => ['required', 'string', 'max:60'],
            'stat_4_value' => ['required', 'string', 'max:20'],
            'stat_4_label' => ['required', 'string', 'max:60'],
        ];
    }
}
