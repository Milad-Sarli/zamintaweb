<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_category_id',
        'title',
        'slug',
        'instructor_name',
        'level',
        'duration',
        'price',
        'prepayment_amount',
        'installment_months',
        'installment_monthly_amount',
        'cover_image',
        'teaser_video_url',
        'support_channel',
        'short_description',
        'description',
        'sort_order',
        'is_published',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'prepayment_amount' => 'integer',
            'installment_months' => 'integer',
            'installment_monthly_amount' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(CourseEpisode::class)->orderBy('sort_order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order')->latest('id');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        return asset('storage/' . $this->cover_image);
    }
}
