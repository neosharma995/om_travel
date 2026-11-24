<div class="form-grid">
    <div class="form-group">
        <label for="name" class="form-label">Category Name *</label>
        <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" class="form-control" required>
        @error('name') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="slug" class="form-label">Slug (optional)</label>
        <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" class="form-control" placeholder="Auto-generated if empty">
        @error('slug') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="parent_id" class="form-label">Parent Category (optional)</label>
        <select id="parent_id" name="parent_id" class="form-control">
            <option value="">None (Top-Level Category)</option>
            @foreach ($parentCategories as $parentOption)
                <option value="{{ $parentOption->id }}" @selected((string) old('parent_id', $category->parent_id) === (string) $parentOption->id)>{{ $parentOption->displayNameWithParent() }}</option>
            @endforeach
        </select>
        @error('parent_id') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="sort_order" class="form-label">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0" class="form-control">
    </div>

    <div class="form-group">
        <label for="image" class="form-label">Image Path / URL</label>
        <input type="text" id="image" name="image" value="{{ old('image', $category->image) }}" class="form-control" placeholder="/images/seed/cat-cruise.jpg">
        @error('image') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="image_file" class="form-label">Or Upload Image</label>
        <input type="file" id="image_file" name="image_file" accept="image/*" class="form-control" data-preview-input="preview-category-img">
        @error('image_file') <span class="field-error">{{ $message }}</span> @enderror
        @if ($category->image)
            <div class="image-preview-box">
                <img id="preview-category-img" src="{{ $category->imageUrl() }}" alt="{{ $category->name }}">
            </div>
        @else
            <div class="image-preview-box">
                <img id="preview-category-img" src="/images/seed/cat-cruise.jpg" alt="Preview">
            </div>
        @endif
    </div>

    <div class="form-group" style="justify-content: center;">
        <label class="checkbox-row">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))>
            <span>Active (visible on website)</span>
        </label>
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Save Category</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancel</a>
</div>
