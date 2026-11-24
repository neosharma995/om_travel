<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\BlogPost;
use App\Models\TrashItem;
use App\Support\MediaUrl;
use App\Support\SlugGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->latest('published_at')
            ->latest('id')
            ->paginate(10);

        return view('admin.blog-posts.index', [
            'posts' => $posts,
        ]);
    }

    public function create(): View
    {
        return view('admin.blog-posts.create', [
            'post' => new BlogPost([
                'category' => 'Travel Tips',
                'read_time' => '5 Min Read',
                'published_at' => now(),
                'is_published' => true,
            ]),
        ]);
    }

    public function store(BlogPostRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $isPublished = $request->boolean('is_published');

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'blog',
            null,
            $validated['image'] ?? '/images/seed/cat-beach.jpg',
        );

        BlogPost::query()->create([
            'title' => $validated['title'],
            'slug' => SlugGenerator::unique('blog_posts', $validated['slug'] ?? $validated['title']),
            'category' => $validated['category'],
            'read_time' => $validated['read_time'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'image' => $imagePath,
            'published_at' => $validated['published_at'] ?? ($isPublished ? now() : null),
            'is_published' => $isPublished,
        ]);

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Blog article created successfully.');
    }

    public function show(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.show', [
            'post' => $blogPost,
        ]);
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.edit', [
            'post' => $blogPost,
        ]);
    }

    public function update(BlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $validated = $request->validated();
        $isPublished = $request->boolean('is_published');

        $imagePath = MediaUrl::storeUpload(
            $request->file('image_file'),
            'blog',
            $blogPost->image,
            $validated['image'] ?? null,
        );

        $blogPost->update([
            'title' => $validated['title'],
            'slug' => SlugGenerator::unique('blog_posts', $validated['slug'] ?? $validated['title'], $blogPost->id),
            'category' => $validated['category'],
            'read_time' => $validated['read_time'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'image' => $imagePath,
            'published_at' => $validated['published_at'] ?? ($isPublished ? ($blogPost->published_at ?? now()) : null),
            'is_published' => $isPublished,
        ]);

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Blog article updated successfully.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        MediaUrl::deleteIfStored($blogPost->image);
        $blogPost->delete();

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Blog article deleted successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.blog-posts.index');
        }

        $action = $request->string('action')->trim()->toString();
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one article.');
        }

        $posts = BlogPost::query()->whereIn('id', $ids)->get();

        if ($action === 'publish') {
            BlogPost::query()->whereIn('id', $ids)->update(['is_published' => true]);

            return back()->with('success', count($posts).' article(s) published.');
        }

        if ($action === 'unpublish') {
            BlogPost::query()->whereIn('id', $ids)->update(['is_published' => false]);

            return back()->with('success', count($posts).' article(s) unpublished.');
        }

        if ($action === 'trash') {
            foreach ($posts as $post) {
                TrashItem::trash($post);
            }

            return back()->with('success', count($posts).' article(s) moved to trash bin.');
        }

        if ($action === 'delete') {
            foreach ($posts as $post) {
                MediaUrl::deleteIfStored($post->image);
                $post->delete();
            }

            return back()->with('success', count($posts).' article(s) deleted permanently.');
        }

        return back()->with('error', 'Invalid action selected.');
    }
}
