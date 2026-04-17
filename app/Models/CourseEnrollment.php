<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'amount',
        'receipt_image',
        'receipt_reference',
        'paid_at',
        'user_note',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getReceiptImageUrlAttribute(): ?string
    {
        if (! $this->receipt_image) {
            return null;
        }

        return asset('storage/' . $this->receipt_image);
    }
}
