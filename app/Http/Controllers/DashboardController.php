<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $approvedEnrollments = $user->courseEnrollments()
            ->with([
                'course.category',
                'course.episodes' => fn ($query) => $query->where('is_published', true)->orderBy('sort_order'),
            ])
            ->where('status', CourseEnrollment::STATUS_APPROVED)
            ->latest('approved_at')
            ->get();

        $pendingEnrollments = $user->courseEnrollments()
            ->with('course.category')
            ->where('status', CourseEnrollment::STATUS_PENDING)
            ->latest()
            ->get();

        $rejectedEnrollments = $user->courseEnrollments()
            ->with('course.category')
            ->where('status', CourseEnrollment::STATUS_REJECTED)
            ->latest('reviewed_at')
            ->get();

        return Inertia::render('Dashboard', [
            'approvedEnrollments' => $approvedEnrollments,
            'pendingEnrollments' => $pendingEnrollments,
            'rejectedEnrollments' => $rejectedEnrollments,
            'dashboardStats' => [
                'approvedCourses' => $approvedEnrollments->count(),
                'pendingRequests' => $pendingEnrollments->count(),
                'availableEpisodes' => $approvedEnrollments->sum(fn ($enrollment) => $enrollment->course?->episodes?->count() ?? 0),
            ],
        ]);
    }
}
