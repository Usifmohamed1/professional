<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Illuminate\Database\Eloquent\Builder;

class PendingPropertyTable extends PropertyTable
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->removeHeaderAction('create')
            ->queryUsing(function (Builder $query): void {
                $query
                    ->select([
                        'id',
                        'name',
                        'images',
                        'views',
                        'status',
                        'moderation_status',
                        'never_expired',
                        'expire_date',
                        'created_at',
                        'unique_id',
                        'location',
                        'zip_code',
                        'author_id',
                        'author_type',
                    ])
                    ->where('moderation_status', ModerationStatusEnum::PENDING)
                    ->with(['author']);
            });
    }
}
