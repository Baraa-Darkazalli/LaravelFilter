<?php

namespace BaraaDark\LaravelFilter\Traits;

use BaraaDark\LaravelFilter\Factories\FilterFactory;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Apply the filter scope to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  array|null  $just
     * @param  array|null  $except
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApplyFilter($builder, $just = null, $except = null)
    {
        if (request()->has('filters')) {
            return $builder->filter(request()->input('filters'), $just, $except);
        }
        return $builder;
    }

    /**
     * Boot the trait for a model.
     *
     * @return void
     */
    public static function bootFilterable()
    {
        if (config('laravel-filter.apply_global_scope', false)) {
            static::addGlobalScope('applyFilter', function (Builder $builder) {
                $builder->applyFilter();
            });
        }
    }

    /**
     * filter local scope
     */
    public function scopeFilter($query, $filters, $just = null, $except = null)
    {
        foreach ($filters as $key => $value)
        {
            if(isset($just))
            {
                if(!in_array($key, $just)) continue;
            }
            if(isset($except))
            {
                if(in_array($key, $except)) continue;
            }
            $filterClass = FilterFactory::create($key, $value, $this);
            $filterClass->apply($query);
        }
        return $query;
    }

    public function filtersKeys(): array
    {
        return [
            // 'filter-key'      => FilterKeyFilter::class
        ];
    }

}
