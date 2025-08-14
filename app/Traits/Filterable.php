<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait Filterable
{
    public function scopeSearch($query, Request $request, array $columns)
    {
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }
        return $query;
    }

    public function scopeFilter($query, Request $request, array $filters)
    {
        foreach ($filters as $field => $type) {
            if ($value = $request->input($field)) {
                if ($type === 'exact') {
                    $query->where($field, $value);
                } elseif ($type === 'like') {
                    $query->where($field, 'LIKE', "%{$value}%");
                }
            }
        }
        return $query;
    }
}
