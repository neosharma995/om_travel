<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->published()
            ->latest('published_at')
            ->latest('id')
            ->paginate(9);

        return view('website.blog.index', [
            'posts' => $posts,
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
            ->limit(4)
            ->get();

        return view('website.blog.show', [
            'post' => $blogPost,
            'recentPosts' => $recentPosts,
        ]);
    }
}
