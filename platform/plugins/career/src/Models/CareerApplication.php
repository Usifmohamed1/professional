<?php

namespace ArchiElite\Career\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CareerApplication extends BaseModel
{
    protected $table = 'career_applications';

    protected $fillable = [
        'career_id',
        'name',
        'phone',
        'email',
        'cv_path',
        'cv_original_name',
        'cv_mime_type',
        'cv_size',
    ];

    protected static function booted(): void
    {
        static::deleted(function (CareerApplication $application): void {
            if ($application->cv_path) {
                Storage::disk('public')->delete($application->cv_path);
            }
        });
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }
}
