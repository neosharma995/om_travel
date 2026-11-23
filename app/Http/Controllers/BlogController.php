<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCategory = $request->string('category')->trim()->toString();

        $posts = BlogPost::query()
            ->published()
            ->when($selectedCategory !== '' && strtolower($selectedCategory) !== 'all', function (Builder $query) use ($selectedCategory): void {
                $query->where('category', $selectedCategory);
            })
            ->latest('published_at')
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        $categories = BlogPost::query()
            ->published()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->values();

        return view('website.blog.index', [
            'posts' => $posts,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function show(BlogPost $blogPost): View
    {
        abort_unless($blogPost->is_published, 404);

        $recentPosts = BlogPost::query()
            ->published()
            ->where('id', '!=', $blogPost->id)
            ->latest('published_at')
            ->latest('id')
            ->limit(3)
            ->get();

        $prevPost = BlogPost::query()
            ->published()
            ->where('id', '<', $blogPost->id)
            ->latest('id')
            ->first();

        $nextPost = BlogPost::query()
            ->published()
            ->where('id', '>', $blogPost->id)
            ->oldest('id')
            ->first();

        return view('website.blog.show', [
            'post' => $blogPost,
            'recentPosts' => $recentPosts,
            'prevPost' => $prevPost,
            'nextPost' => $nextPost,
        ]);
    }
}
