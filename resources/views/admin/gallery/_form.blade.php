<div class="form-grid">
    <div class="form-group">
        <label for="title" class="form-label">Photo Title *</label>
        <input type="text" id="title" name="title" value="{{ old('title', $item->title) }}" class="form-control" required>
        @error('title') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="location" class="form-label">Location</label>
        <input type="text" id="location" name="location" value="{{ old('location', $item->location) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="image" class="form-label">Image Path / URL</label>
        <input type="text" id="image" name="image" value="{{ old('image', $item->image) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="image_file" class="form-label">Or Upload Image</label>
        <input type="file" id="image_file" name="image_file" accept="image/*" class="form-control" data-preview-input="preview-gallery-img">
        <div class="image-preview-box">
            <img id="preview-gallery-img" src="{{ $item->imageUrl() }}" alt="Preview">
        </div>
    </div>

    <div class="form-group">
        <label for="sort_order" class="form-label">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 0) }}" min="0" class="form-control">
    </div>

    <div class="form-group" style="justify-content: center;">
        <label class="checkbox-row">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))>
            <span>Active</span>
        </label>
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Save Gallery Photo</button>
    <a href="{{ route('admin.gallery.index') }}" class="btn btn-outline">Cancel</a>
</div>
