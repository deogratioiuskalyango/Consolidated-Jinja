<?php

namespace Botble\RealEstate\Repositories\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasRoomFilter
{
    /**
     * Apply a room/block filter that supports:
     * - single int: ?bathroom=2
     * - comma-separated: ?bathroom=2,3,4
     * - array: ?bathroom[]=2&bathroom[]=3
     */
    protected function applyRoomFilter(string $column, array|int|float|string $value): void
    {
        $raw = is_string($value) ? explode(',', $value) : (array) $value;
        $values = array_filter(array_map('floatval', $raw));

        if (! $values) {
            return;
        }

        $hasGte5 = in_array(5.0, $values, true);
        $exactValues = array_filter($values, fn ($v) => $v < 5);

        $this->model = $this->model->where(function (Builder $query) use ($column, $hasGte5, $exactValues) {
            if ($exactValues) {
                $query->whereIn($column, $exactValues);
            }

            if ($hasGte5) {
                $query->{$exactValues ? 'orWhere' : 'where'}($column, '>=', 5);
            }
        });
    }
}
