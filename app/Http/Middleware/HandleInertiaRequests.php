<?php

namespace App\Http\Middleware;

use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'csrf_token' => csrf_token(),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'status' => $request->session()->get('status'),
            ],
            'courseCategories' => fn () => CourseCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'title', 'slug']),
            'translations' => [
                'ui' => require base_path('lang/fa/ui.php'),
                'category' => ['resource' => (require base_path('lang/fa/category.php'))['resource']],
                'post' => ['resource' => (require base_path('lang/fa/post.php'))['resource']],
            ],
        ];
    }
}
