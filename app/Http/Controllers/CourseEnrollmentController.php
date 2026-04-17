<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseEnrollmentController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        abort_unless($course->is_published, 404);

        $validated = $request->validate([
            'amount' => ['nullable', 'integer', 'min:0'],
            'receipt_reference' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
            'user_note' => ['nullable', 'string', 'max:2000'],
            'receipt_image' => ['required', 'image', 'max:4096'],
        ], [], [
            'amount' => 'مبلغ پرداختی',
            'receipt_reference' => 'شماره پیگیری',
            'paid_at' => 'تاریخ پرداخت',
            'user_note' => 'توضیحات',
            'receipt_image' => 'تصویر فیش',
        ]);

        $existing = CourseEnrollment::query()
            ->where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->first();

        $receiptPath = $validated['receipt_image']->store('payment-receipts', 'public');

        if ($existing?->receipt_image) {
            \Storage::disk('public')->delete($existing->receipt_image);
        }

        CourseEnrollment::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
            ],
            [
                'status' => CourseEnrollment::STATUS_PENDING,
                'amount' => $validated['amount'] ?? null,
                'receipt_reference' => $validated['receipt_reference'] ?? null,
                'paid_at' => $validated['paid_at'] ?? null,
                'user_note' => $validated['user_note'] ?? null,
                'receipt_image' => $receiptPath,
                'admin_note' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'approved_at' => null,
            ],
        );

        return back()->with('success', 'فیش پرداخت شما ثبت شد و پس از بررسی، دسترسی دوره فعال می‌شود.');
    }
}
