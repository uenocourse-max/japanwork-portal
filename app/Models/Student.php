<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'full_name',
        'age',
        'birth_place',
        'birth_date',
        'address',
        'height_cm',
        'weight_kg',
        'blood_type',
        'marital_status',
        'phone_number',
        'gender',
        'participant_status',
        'jft_score',
        'jlpt_level',
        'japanese_learning_months',
        'pathway',
        'lpk_name',
        'photo_drive_url',
        'cv_drive_url',
        'matching_status',
        'matched_company_name',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'jft_score' => 'integer',
            'japanese_learning_months' => 'integer',
            'height_cm' => 'integer',
            'weight_kg' => 'integer',
            'age' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function sswCategories(): BelongsToMany
    {
        return $this->belongsToMany(SswCategory::class, 'student_ssw_category');
    }

    public function isProfileComplete(): bool
    {
        return filled($this->full_name)
            && filled($this->phone_number)
            && filled($this->birth_date);
    }

    public function hasSswCategory(SswCategory|int $category): bool
    {
        $categoryId = $category instanceof SswCategory ? $category->id : $category;

        return $this->sswCategories()->where('ssw_categories.id', $categoryId)->exists();
    }
}
