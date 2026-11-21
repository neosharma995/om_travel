@extends('layouts.admin')

@section('title', 'Site Settings & Hero')
@section('page_title', 'Site Settings & Homepage Sections')

@section('content')
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">Brand, Top Bar &amp; Contact Information</h2>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="site_name" class="form-label">Site Name *</label>
                    <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="site_tagline" class="form-label">Brand Tagline *</label>
                    <input type="text" id="site_tagline" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="topbar_address" class="form-label">Top Bar Address *</label>
                    <input type="text" id="topbar_address" name="topbar_address" value="{{ old('topbar_address', $settings['topbar_address']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="topbar_hours" class="form-label">Top Bar Hours *</label>
                    <input type="text" id="topbar_hours" name="topbar_hours" value="{{ old('topbar_hours', $settings['topbar_hours']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="contact_phone" class="form-label">Primary Phone *</label>
                    <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="contact_phone_alt" class="form-label">Secondary Phone</label>
                    <input type="text" id="contact_phone_alt" name="contact_phone_alt" value="{{ old('contact_phone_alt', $settings['contact_phone_alt']) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="contact_email" class="form-label">Contact Email *</label>
                    <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="contact_address" class="form-label">Office Address *</label>
                    <input type="text" id="contact_address" name="contact_address" value="{{ old('contact_address', $settings['contact_address']) }}" class="form-control" required>
                </div>
                <div class="form-group form-span-2">
                    <label for="footer_about" class="form-label">Footer About Text *</label>
                    <textarea id="footer_about" name="footer_about" class="form-control" required>{{ old('footer_about', $settings['footer_about']) }}</textarea>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">Hero Banner</h2>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="hero_subtitle" class="form-label">Hero Script Subtitle *</label>
                    <input type="text" id="hero_subtitle" name="hero_subtitle" value="{{ old('hero_subtitle', $settings['hero_subtitle']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="hero_title" class="form-label">Hero Main Heading *</label>
                    <input type="text" id="hero_title" name="hero_title" value="{{ old('hero_title', $settings['hero_title']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="hero_primary_cta" class="form-label">Primary Button Text *</label>
                    <input type="text" id="hero_primary_cta" name="hero_primary_cta" value="{{ old('hero_primary_cta', $settings['hero_primary_cta']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="hero_secondary_cta" class="form-label">Secondary Button Text *</label>
                    <input type="text" id="hero_secondary_cta" name="hero_secondary_cta" value="{{ old('hero_secondary_cta', $settings['hero_secondary_cta']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="hero_image" class="form-label">Hero Background Image Path/URL</label>
                    <input type="text" id="hero_image" name="hero_image" value="{{ old('hero_image', $settings['hero_image']) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="hero_image_file" class="form-label">Or Upload Hero Background</label>
                    <input type="file" id="hero_image_file" name="hero_image_file" accept="image/*" class="form-control" data-preview-input="preview-hero-image">
                    <div class="image-preview-box">
                        <img id="preview-hero-image" src="{{ \App\Support\MediaUrl::resolve($settings['hero_image']) }}" alt="Hero preview">
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">Plan Your Trip (About Section)</h2>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="about_subtitle" class="form-label">About Script Subtitle *</label>
                    <input type="text" id="about_subtitle" name="about_subtitle" value="{{ old('about_subtitle', $settings['about_subtitle']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="about_title" class="form-label">About Title *</label>
                    <input type="text" id="about_title" name="about_title" value="{{ old('about_title', $settings['about_title']) }}" class="form-control" required>
                </div>
                <div class="form-group form-span-2">
                    <label for="about_description" class="form-label">About Description *</label>
                    <textarea id="about_description" name="about_description" class="form-control" required>{{ old('about_description', $settings['about_description']) }}</textarea>
                </div>
                <div class="form-group">
                    <label for="about_feature_1_title" class="form-label">Feature 1 Title *</label>
                    <input type="text" id="about_feature_1_title" name="about_feature_1_title" value="{{ old('about_feature_1_title', $settings['about_feature_1_title']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="about_feature_1_text" class="form-label">Feature 1 Description *</label>
                    <input type="text" id="about_feature_1_text" name="about_feature_1_text" value="{{ old('about_feature_1_text', $settings['about_feature_1_text']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="about_feature_2_title" class="form-label">Feature 2 Title *</label>
                    <input type="text" id="about_feature_2_title" name="about_feature_2_title" value="{{ old('about_feature_2_title', $settings['about_feature_2_title']) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="about_feature_2_text" class="form-label">Feature 2 Description *</label>
                    <input type="text" id="about_feature_2_text" name="about_feature_2_text" value="{{ old('about_feature_2_text', $settings['about_feature_2_text']) }}" class="form-control" required>
                </div>

                @for ($imgNum = 1; $imgNum <= 3; $imgNum++)
                    <div class="form-group">
                        <label for="about_image_{{ $imgNum }}" class="form-label">Collage Image {{ $imgNum }} Path/URL</label>
                        <input type="text" id="about_image_{{ $imgNum }}" name="about_image_{{ $imgNum }}" value="{{ old('about_image_'.$imgNum, $settings['about_image_'.$imgNum]) }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="about_image_{{ $imgNum }}_file" class="form-label">Or Upload Collage Image {{ $imgNum }}</label>
                        <input type="file" id="about_image_{{ $imgNum }}_file" name="about_image_{{ $imgNum }}_file" accept="image/*" class="form-control" data-preview-input="preview-about-{{ $imgNum }}">
                        <div class="image-preview-box">
                            <img id="preview-about-{{ $imgNum }}" src="{{ \App\Support\MediaUrl::resolve($settings['about_image_'.$imgNum]) }}" alt="About preview {{ $imgNum }}">
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">Stat Counter Bubbles</h2>
            </div>
            <div class="form-grid">
                @for ($s = 1; $s <= 4; $s++)
                    <div class="form-group">
                        <label for="stat_{{ $s }}_value" class="form-label">Stat {{ $s }} Value *</label>
                        <input type="text" id="stat_{{ $s }}_value" name="stat_{{ $s }}_value" value="{{ old('stat_'.$s.'_value', $settings['stat_'.$s.'_value']) }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="stat_{{ $s }}_label" class="form-label">Stat {{ $s }} Label *</label>
                        <input type="text" id="stat_{{ $s }}_label" name="stat_{{ $s }}_label" value="{{ old('stat_'.$s.'_label', $settings['stat_'.$s.'_label']) }}" class="form-control" required>
                    </div>
                @endfor
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save All Settings</button>
            </div>
        </div>
    </form>
@endsection
