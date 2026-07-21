<?php

namespace App\Models;

use Database\Factories\JobListingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobListing extends Model
{
    /** @use HasFactory<JobListingFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'requirements',
        'salary_min',
        'salary_max',
        'location',
        'company_name',
        'company_description',
        'thumbnail_url',
        'ssw_category_id',
        'jlpt_level_required',
        'participant_status_required',
        'status',
        'job_type',
        'posted_by',
        'deadline',
    ];

    protected function casts(): array
    {
        return [
            'salary_min' => 'integer',
            'salary_max' => 'integer',
            'deadline' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function sswCategory(): BelongsTo
    {
        return $this->belongsTo(SswCategory::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function savedByStudents(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function getThumbnailDisplayUrlAttribute(): ?string
    {
        if (! $this->thumbnail_url) {
            return null;
        }

        if (preg_match('#drive\.google\.com/file/d/([a-zA-Z0-9_-]+)#', $this->thumbnail_url, $matches)) {
            return "https://drive.google.com/uc?export=view&id={$matches[1]}";
        }

        if (preg_match('#drive\.google\.com/open\?id=([a-zA-Z0-9_-]+)#', $this->thumbnail_url, $matches)) {
            return "https://drive.google.com/uc?export=view&id={$matches[1]}";
        }

        return $this->thumbnail_url;
    }

    public function applicants()
    {
        return $this->belongsToMany(Student::class, 'job_applications')
            ->withPivot('status', 'applied_at', 'notes', 'reviewed_at')
            ->withTimestamps();
    }
}
