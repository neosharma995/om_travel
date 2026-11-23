<div class="form-grid">
    <div class="form-group">
        <label for="title" class="form-label">Tour Title *</label>
        <input type="text" id="title" name="title" value="{{ old('title', $tour->title) }}" class="form-control" required>
        @error('title') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="slug" class="form-label">Slug (optional)</label>
        <input type="text" id="slug" name="slug" value="{{ old('slug', $tour->slug) }}" class="form-control">
        @error('slug') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="destination_id" class="form-label">Destination</label>
        <select id="destination_id" name="destination_id" class="form-control">
            <option value="">None</option>
            @foreach ($destinations as $dest)
                <option value="{{ $dest->id }}" @selected((string) old('destination_id', $tour->destination_id) === (string) $dest->id)>{{ $dest->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="tour_category_id" class="form-label">Tour Category</label>
        <select id="tour_category_id" name="tour_category_id" class="form-control">
            <option value="">None</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected((string) old('tour_category_id', $tour->tour_category_id) === (string) $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="tour_type_id" class="form-label">Tour Type</label>
        <select id="tour_type_id" name="tour_type_id" class="form-control">
            <option value="">None</option>
            @foreach ($tourTypes as $type)
                <option value="{{ $type->id }}" @selected((string) old('tour_type_id', $tour->tour_type_id) === (string) $type->id)>{{ $type->name }}</option>
            @endforeach
        </select>
        @error('tour_type_id') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="location" class="form-label">Location Subtitle</label>
        <input type="text" id="location" name="location" value="{{ old('location', $tour->location) }}" class="form-control" placeholder="e.g. Santorini &amp; Mykonos, Greece">
    </div>

    <div class="form-group">
        <label for="price" class="form-label">Price Per Person ($) *</label>
        <input type="number" id="price" name="price" value="{{ old('price', $tour->price) }}" step="0.01" min="0" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="duration_days" class="form-label">Duration Days *</label>
        <input type="number" id="duration_days" name="duration_days" value="{{ old('duration_days', $tour->duration_days ?? 5) }}" min="1" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="duration_label" class="form-label">Duration Display Label</label>
        <input type="text" id="duration_label" name="duration_label" value="{{ old('duration_label', $tour->duration_label) }}" class="form-control" placeholder="e.g. 7 Days">
    </div>

    <div class="form-group">
        <label for="rating" class="form-label">Rating (1.0 - 5.0) *</label>
        <input type="number" id="rating" name="rating" value="{{ old('rating', $tour->rating ?? 4.8) }}" step="0.1" min="1" max="5" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="reviews_count" class="form-label">Reviews Count</label>
        <input type="number" id="reviews_count" name="reviews_count" value="{{ old('reviews_count', $tour->reviews_count ?? 0) }}" min="0" class="form-control">
    </div>

    <div class="form-group">
        <label for="image" class="form-label">Image Path / URL</label>
        <input type="text" id="image" name="image" value="{{ old('image', $tour->image) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="image_file" class="form-label">Or Upload Cover Photo</label>
        <input type="file" id="image_file" name="image_file" accept="image/*" class="form-control" data-preview-input="preview-tour-img">
        <div class="image-preview-box">
            <img id="preview-tour-img" src="{{ $tour->imageUrl() }}" alt="Preview">
        </div>
    </div>

    <div class="form-group form-span-2">
        <label for="summary" class="form-label">Short Summary</label>
        <input type="text" id="summary" name="summary" value="{{ old('summary', $tour->summary) }}" class="form-control">
    </div>

    <div class="form-group form-span-2">
        <label for="description" class="form-label">Full Description</label>
        <textarea id="description" name="description" class="form-control">{{ old('description', $tour->description) }}</textarea>
    </div>

    <div class="form-group form-span-2">
        <label for="highlights" class="form-label">Tour Highlights (one per line)</label>
        <textarea id="highlights" name="highlights" class="form-control">{{ old('highlights', $tour->highlights) }}</textarea>
    </div>

    <div class="form-group">
        <label for="sort_order" class="form-label">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $tour->sort_order ?? 0) }}" min="0" class="form-control">
    </div>

    <div class="form-group" style="justify-content: center; gap: 10px;">
        <label class="checkbox-row">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $tour->is_featured ?? true))>
            <span>Featured on Homepage</span>
        </label>
        <label class="checkbox-row">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $tour->is_active ?? true))>
            <span>Active (bookable)</span>
        </label>
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Save Tour Package</button>
    <a href="{{ route('admin.tours.index') }}" class="btn btn-outline">Cancel</a>
</div>
