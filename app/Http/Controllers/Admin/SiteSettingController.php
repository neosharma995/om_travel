<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteSettingsRequest;
use App\Models\SiteSetting;
use App\Support\MediaUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => SiteSetting::allKeyed(),
        ]);
    }

    public function update(UpdateSiteSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $existing = SiteSetting::allKeyed();

        $textFields = [
            'site_name',
            'site_tagline',
            'topbar_address',
            'topbar_hours',
            'contact_phone',
            'contact_phone_alt',
            'contact_email',
            'contact_address',
            'footer_about',
            'hero_subtitle',
            'hero_title',
            'hero_primary_cta',
            'hero_secondary_cta',
            'about_subtitle',
            'about_title',
            'about_description',
            'about_feature_1_title',
            'about_feature_1_text',
            'about_feature_2_title',
            'about_feature_2_text',
            'stat_1_value',
            'stat_1_label',
            'stat_2_value',
            'stat_2_label',
            'stat_3_value',
            'stat_3_label',
            'stat_4_value',
            'stat_4_label',
        ];

        $pairs = [];
        foreach ($textFields as $field) {
            $pairs[$field] = $validated[$field] ?? '';
        }

        $imageMap = [
            'hero_image' => 'hero_image_file',
            'about_image_1' => 'about_image_1_file',
            'about_image_2' => 'about_image_2_file',
            'about_image_3' => 'about_image_3_file',
        ];

        foreach ($imageMap as $settingKey => $fileInput) {
            $pairs[$settingKey] = MediaUrl::storeUpload(
                $request->file($fileInput),
                'settings',
                $existing[$settingKey] ?? null,
                $validated[$settingKey] ?? null,
            );
        }

        SiteSetting::setMany($pairs, 'general');

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Site settings updated successfully.');
    }
}
