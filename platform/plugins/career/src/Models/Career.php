<?php

namespace ArchiElite\Career\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Career extends BaseModel
{
    protected $table = 'careers';

    protected $fillable = [
        'name',
        'location',
        'salary',
        'description',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(CareerApplication::class);
    }
}
