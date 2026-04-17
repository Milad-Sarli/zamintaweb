<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::published()
            ->with(['category', 'user'])
            ->latest('published_at');

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(12)->withQueryString();

        $posts->getCollection()->transform(function ($post) {
            $post->image = $post->image ? asset('storage/' . $post->image) : null;

            return $post;
        });

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'postCategories' => PostCategory::where('is_visible', true)->get(),
            'filters' => $request->only(['category', 'search']),
        ]);
    }

    public function show(Post $post)
    {
        if (!$post->is_published || $post->published_at > now()) {
            abort(404);
        }

        // Increment view count
        $post->increment('view_count');

        $post->load(['category', 'user']);
        $post->loadCount('likes');
        $post->image = $post->image ? asset('storage/' . $post->image) : null;

        $isLiked = $post->isLikedBy(auth()->user(), request()->ip());

        $relatedPosts = Post::published()
            ->where('post_category_id', $post->post_category_id)
            ->where('id', '!=', $post->id)
            ->limit(4)
            ->get()
            ->map(function ($p) {
                $p->image = $p->image ? asset('storage/' . $p->image) : null;

                return $p;
            });

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'isLiked' => $isLiked,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    public function like(Post $post)
    {
        $user = auth()->user();
        $ip = request()->ip();

        $existingLike = $post->likes()
            ->where(function ($query) use ($user, $ip) {
                if ($user) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->where('ip_address', $ip);
                }
            })
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            $post->likes()->create([
                'user_id' => $user?->id,
                'ip_address' => $user ? null : $ip,
            ]);
            $liked = true;
        }

        return back();
    }
}
