<div class="form-grid">
    <div class="form-group">
        <label for="name" class="form-label">Destination Name *</label>
        <input type="text" id="name" name="name" value="{{ old('name', $destination->name) }}" class="form-control" required>
        @error('name') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="slug" class="form-label">Slug (optional)</label>
        <input type="text" id="slug" name="slug" value="{{ old('slug', $destination->slug) }}" class="form-control">
        @error('slug') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="country" class="form-label">Country</label>
        <input type="text" id="country" name="country" value="{{ old('country', $destination->country) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="listings_label" class="form-label">Listings Badge Label</label>
        <input type="text" id="listings_label" name="listings_label" value="{{ old('listings_label', $destination->listings_label) }}" class="form-control" placeholder="e.g. 25 Listing">
    </div>

    <div class="form-group">
        <label for="image" class="form-label">Image Path / URL</label>
        <input type="text" id="image" name="image" value="{{ old('image', $destination->image) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="image_file" class="form-label">Or Upload Image</label>
        <input type="file" id="image_file" name="image_file" accept="image/*" class="form-control" data-preview-input="preview-dest-img">
        <div class="image-preview-box">
            <img id="preview-dest-img" src="{{ $destination->imageUrl() }}" alt="Preview">
        </div>
    </div>

    <div class="form-group form-span-2">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" class="form-control">{{ old('description', $destination->description) }}</textarea>
    </div>

    <div class="form-group">
        <label for="sort_order" class="form-label">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $destination->sort_order ?? 0) }}" min="0" class="form-control">
    </div>

    <div class="form-group" style="justify-content: center; gap: 10px;">
        <label class="checkbox-row">
            <input type="checkbox" name="is_popular" value="1" @checked(old('is_popular', $destination->is_popular ?? true))>
            <span>Show in Popular Destinations Slider</span>
        </label>
        <label class="checkbox-row">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $destination->is_active ?? true))>
            <span>Active</span>
        </label>
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Save Destination</button>
    <a href="{{ route('admin.destinations.index') }}" class="btn btn-outline">Cancel</a>
</div>
