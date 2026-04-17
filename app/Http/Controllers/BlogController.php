<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::published()->with(['user', 'category']);

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('summary', 'like', '%' . $request->search . '%');
            });
        }

        $posts = $query->latest()->paginate(9)->withQueryString();

        $postCategories = PostCategory::where('is_visible', true)->get(['id', 'name', 'slug']);

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'postCategories' => $postCategories,
            'filters' => [
                'category' => $request->category,
                'search' => $request->search,
            ],
        ]);
    }

    public function show(Post $post)
    {
        if (!$post->is_published) {
            abort(404);
        }

        $post->increment('view_count');
        $post->load(['user', 'category']);

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->when($post->post_category_id, function ($query) use ($post) {
                return $query->where('post_category_id', $post->post_category_id);
            })
            ->limit(4)
            ->get();

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
