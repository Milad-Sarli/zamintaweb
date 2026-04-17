<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\CourseEpisode;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $featuredCourses = Course::query()
            ->with('category')
            ->withCount(['episodes' => fn ($query) => $query->where('is_published', true)])
            ->published()
            ->where('is_featured', true)
            ->take(6)
            ->get();

        $latestCourses = Course::query()
            ->with('category')
            ->withCount(['episodes' => fn ($query) => $query->where('is_published', true)])
            ->published()
            ->take(9)
            ->get();

        $categories = CourseCategory::query()
            ->where('is_active', true)
            ->withCount('courses')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Welcome', [
            'featuredCourses' => $featuredCourses,
            'latestCourses' => $latestCourses,
            'categories' => $categories,
            'stats' => [
                'courses' => Course::query()->where('is_published', true)->count(),
                'students' => CourseEnrollment::query()->where('status', CourseEnrollment::STATUS_APPROVED)->count(),
                'episodes' => CourseEpisode::query()->where('is_published', true)->count(),
            ],
        ]);
    }
}
