<div class="form-grid">
    <div class="form-group">
        <label for="name" class="form-label">Tour Type Name *</label>
        <input type="text" id="name" name="name" value="{{ old('name', $tourType->name) }}" class="form-control" required>
        @error('name') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="slug" class="form-label">Slug (optional)</label>
        <input type="text" id="slug" name="slug" value="{{ old('slug', $tourType->slug) }}" class="form-control" placeholder="Auto-generated if empty">
        @error('slug') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="sort_order" class="form-label">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $tourType->sort_order ?? 0) }}" min="0" class="form-control">
    </div>

    <div class="form-group" style="justify-content: center;">
        <label class="checkbox-row">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $tourType->is_active ?? true))>
            <span>Active (visible on website)</span>
        </label>
    </div>

    <div class="form-group form-span-2">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" class="form-control" placeholder="Brief summary of this tour style...">{{ old('description', $tourType->description) }}</textarea>
        @error('description') <span class="field-error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Save Tour Type</button>
    <a href="{{ route('admin.tour-types.index') }}" class="btn btn-outline">Cancel</a>
</div>
