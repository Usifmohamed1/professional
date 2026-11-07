<?php

namespace ArchiElite\Career\Tables;

use ArchiElite\Career\Models\CareerApplication;
use Botble\Base\Facades\Html;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EmailColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\PhoneColumn;
use Illuminate\Database\Eloquent\Builder;

class CareerApplicationTable extends TableAbstract
{
    public function setup(): void
    {
        $this->defaultSortColumnName = 'created_at';

        $this
            ->model(CareerApplication::class)
            ->addAction(DeleteAction::make()->route('careers.applications.destroy')->permission('careers.destroy'))
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->alignLeft(),
                PhoneColumn::make()->linkable()->withEmptyState(),
                EmailColumn::make()->linkable()->withEmptyState(),
                FormattedColumn::make('career_id')
                    ->title(trans('plugins/career::career.application.career'))
                    ->alignLeft()
                    ->orderable(false)
                    ->searchable(false)
                    ->renderUsing(fn (FormattedColumn $column) => $column->getItem()->career?->name ?: '&mdash;'),
                FormattedColumn::make('cv_original_name')
                    ->title(trans('plugins/career::career.application.cv'))
                    ->alignLeft()
                    ->orderable(false)
                    ->searchable(false)
                    ->renderUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        if (! $item->cv_path) {
                            return '&mdash;';
                        }

                        return Html::link(
                            route('careers.applications.download', $item->getKey()),
                            sprintf(
                                '<i class="ti ti-download"></i> %s',
                                e($item->cv_original_name ?: trans('plugins/career::career.application.download_cv'))
                            ),
                            ['class' => 'btn btn-sm btn-primary'],
                            escape: false
                        )->toHtml();
                    }),
                FormattedColumn::make('cv_size')
                    ->title(trans('plugins/career::career.application.cv_size'))
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        $size = (int) $column->getItem()->cv_size;

                        return $size > 0 ? number_format($size / 1024, 1) . ' KB' : null;
                    }),
                CreatedAtColumn::make(),
            ])
            ->addBulkAction(DeleteBulkAction::make()->permission('careers.destroy'))
            ->queryUsing(function (Builder $query) {
                return $query
                    ->with('career')
                    ->select([
                        'career_applications.id',
                        'career_applications.career_id',
                        'career_applications.name',
                        'career_applications.phone',
                        'career_applications.email',
                        'career_applications.cv_path',
                        'career_applications.cv_original_name',
                        'career_applications.cv_size',
                        'career_applications.created_at',
                    ]);
            });
    }
}
