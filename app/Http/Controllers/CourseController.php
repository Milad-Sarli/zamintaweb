<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(Request $request): Response
    {
        $categorySlug = $request->string('category')->toString();

        $courses = Course::query()
            ->with('category')
            ->withCount(['episodes' => fn ($query) => $query->where('is_published', true)])
            ->published()
            ->when($categorySlug, function ($query, $categorySlug) {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
            })
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
            'selectedCategory' => $categorySlug,
            'categories' => CourseCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function show(Request $request, Course $course): Response
    {
        abort_unless($course->is_published, 404);

        $course->load([
            'category',
            'episodes' => fn ($query) => $query->where('is_published', true)->orderBy('sort_order'),
        ]);

        $enrollment = null;

        if ($request->user()) {
            $enrollment = CourseEnrollment::query()
                ->where('user_id', $request->user()->id)
                ->where('course_id', $course->id)
                ->first();
        }

        return Inertia::render('Courses/Show', [
            'course' => $course,
            'enrollment' => $enrollment,
            'relatedCourses' => Course::query()
                ->with('category')
                ->published()
                ->where('id', '!=', $course->id)
                ->when($course->course_category_id, fn ($query) => $query->where('course_category_id', $course->course_category_id))
                ->take(3)
                ->get(),
        ]);
    }
}
