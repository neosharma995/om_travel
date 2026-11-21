<div class="form-grid">
    <div class="form-group">
        <label for="client_name" class="form-label">Client Name *</label>
        <input type="text" id="client_name" name="client_name" value="{{ old('client_name', $testimonial->client_name) }}" class="form-control" required>
        @error('client_name') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="client_role" class="form-label">Client Role *</label>
        <input type="text" id="client_role" name="client_role" value="{{ old('client_role', $testimonial->client_role) }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="avatar" class="form-label">Avatar Path / URL</label>
        <input type="text" id="avatar" name="avatar" value="{{ old('avatar', $testimonial->avatar) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="avatar_file" class="form-label">Or Upload Avatar</label>
        <input type="file" id="avatar_file" name="avatar_file" accept="image/*" class="form-control" data-preview-input="preview-testimonial-img">
        <div class="image-preview-box">
            <img id="preview-testimonial-img" src="{{ $testimonial->avatarUrl() }}" alt="Preview">
        </div>
    </div>

    <div class="form-group">
        <label for="rating" class="form-label">Rating (1 - 5) *</label>
        <input type="number" id="rating" name="rating" value="{{ old('rating', $testimonial->rating ?? 5) }}" min="1" max="5" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="sort_order" class="form-label">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}" min="0" class="form-control">
    </div>

    <div class="form-group form-span-2">
        <label for="quote" class="form-label">Client Quote *</label>
        <textarea id="quote" name="quote" class="form-control" required>{{ old('quote', $testimonial->quote) }}</textarea>
    </div>

    <div class="form-group form-span-2" style="flex-direction: row; gap: 24px;">
        <label class="checkbox-row">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $testimonial->is_featured ?? true))>
            <span>Featured</span>
        </label>
        <label class="checkbox-row">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $testimonial->is_active ?? true))>
            <span>Active</span>
        </label>
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Save Testimonial</button>
    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline">Cancel</a>
</div>
