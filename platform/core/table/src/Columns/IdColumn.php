<?php

namespace Botble\Table\Columns;

use Botble\Base\Models\BaseModel;
use Botble\Table\Contracts\FormattedColumn as FormattedColumnContract;

class IdColumn extends FormattedColumn implements FormattedColumnContract
{
    public static function make(array|string $data = [], string $name = ''): static
    {
        return parent::make($data ?: 'id', $name)
            ->title(trans('core/base::tables.id'))
            ->alignCenter()
            ->width(20)
            ->columnVisibility()
            ->orderable(false)
            ->searchable(false)
            ->getValueUsing(function (IdColumn $column): int {
                $item = $column->getItem();
                $query = $column->getTable()->query();

                if (! method_exists($item, 'getKeyName')) {
                    return (int) $item->id;
                }

                $keyColumn = $item->qualifyColumn($item->getKeyName());
                $keyValue = $item->getKey();

                if (is_numeric($keyValue)) {
                    return $query->where($keyColumn, '<=', $keyValue)->count();
                }

                if (! $createdAt = $item->getAttribute($item->getCreatedAtColumn())) {
                    return $query->where($keyColumn, '<=', $keyValue)->count();
                }

                $createdAtColumn = $item->qualifyColumn($item->getCreatedAtColumn());

                return $query
                    ->where(function ($query) use ($createdAtColumn, $createdAt, $keyColumn, $keyValue): void {
                        $query
                            ->where($createdAtColumn, '<', $createdAt)
                            ->orWhere(function ($query) use ($createdAtColumn, $createdAt, $keyColumn, $keyValue): void {
                                $query
                                    ->where($createdAtColumn, '=', $createdAt)
                                    ->where($keyColumn, '<=', $keyValue);
                            });
                    })
                    ->count();
            });
    }

    public function formattedValue($value): ?string
    {
        return $this
            ->when(BaseModel::isUsingStringId(), function (IdColumn $column) {
                return $column->limit();
            })
            ->applyLimitIfAvailable($value);
    }
}
