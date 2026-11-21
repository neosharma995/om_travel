<div class="form-grid">
    <div class="form-group">
        <label for="title" class="form-label">Article Title *</label>
        <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" class="form-control" required>
        @error('title') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="slug" class="form-label">Slug (optional)</label>
        <input type="text" id="slug" name="slug" value="{{ old('slug', $post->slug) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="category" class="form-label">Category *</label>
        <input type="text" id="category" name="category" value="{{ old('category', $post->category) }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="read_time" class="form-label">Read Time Label *</label>
        <input type="text" id="read_time" name="read_time" value="{{ old('read_time', $post->read_time) }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="image" class="form-label">Cover Image Path / URL</label>
        <input type="text" id="image" name="image" value="{{ old('image', $post->image) }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="image_file" class="form-label">Or Upload Cover Image</label>
        <input type="file" id="image_file" name="image_file" accept="image/*" class="form-control" data-preview-input="preview-blog-img">
        <div class="image-preview-box">
            <img id="preview-blog-img" src="{{ $post->imageUrl() }}" alt="Preview">
        </div>
    </div>

    <div class="form-group form-span-2">
        <label for="excerpt" class="form-label">Excerpt</label>
        <input type="text" id="excerpt" name="excerpt" value="{{ old('excerpt', $post->excerpt) }}" class="form-control">
    </div>

    <div class="form-group form-span-2">
        <label for="content" class="form-label">Article Content *</label>
        <textarea id="content" name="content" class="form-control" style="min-height: 200px;" required>{{ old('content', $post->content) }}</textarea>
    </div>

    <div class="form-group">
        <label for="published_at" class="form-label">Publish Date</label>
        <input type="date" id="published_at" name="published_at" value="{{ old('published_at', optional($post->published_at)->toDateString() ?? now()->toDateString()) }}" class="form-control">
    </div>

    <div class="form-group" style="justify-content: center;">
        <label class="checkbox-row">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->is_published ?? true))>
            <span>Published</span>
        </label>
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Save Article</button>
    <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-outline">Cancel</a>
</div>
