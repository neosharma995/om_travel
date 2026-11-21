<div class="form-grid">
    <div class="form-group">
        <label for="name" class="form-label">Guide Name *</label>
        <input type="text" id="name" name="name" value="{{ old('name', $guide->name) }}" class="form-control" required>
        @error('name') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="role" class="form-label">Role / Title *</label>
        <input type="text" id="role" name="role" value="{{ old('role', $guide->role) }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="photo" class="form-label">Photo Path / URL</label>
        <input type="text" id="photo" name="photo" value="{{ old('photo', $guide->photo) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="photo_file" class="form-label">Or Upload Portrait</label>
        <input type="file" id="photo_file" name="photo_file" accept="image/*" class="form-control" data-preview-input="preview-guide-img">
        <div class="image-preview-box">
            <img id="preview-guide-img" src="{{ $guide->photoUrl() }}" alt="Preview">
        </div>
    </div>

    <div class="form-group form-span-2">
        <label for="bio" class="form-label">Bio</label>
        <textarea id="bio" name="bio" class="form-control">{{ old('bio', $guide->bio) }}</textarea>
    </div>

    <div class="form-group">
        <label for="facebook_url" class="form-label">Facebook URL</label>
        <input type="url" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $guide->facebook_url) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="twitter_url" class="form-label">Twitter / X URL</label>
        <input type="url" id="twitter_url" name="twitter_url" value="{{ old('twitter_url', $guide->twitter_url) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="instagram_url" class="form-label">Instagram URL</label>
        <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $guide->instagram_url) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="linkedin_url" class="form-label">LinkedIn URL</label>
        <input type="url" id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $guide->linkedin_url) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="sort_order" class="form-label">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $guide->sort_order ?? 0) }}" min="0" class="form-control">
    </div>

    <div class="form-group" style="justify-content: center;">
        <label class="checkbox-row">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $guide->is_active ?? true))>
            <span>Active</span>
        </label>
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Save Guide</button>
    <a href="{{ route('admin.guides.index') }}" class="btn btn-outline">Cancel</a>
</div>
