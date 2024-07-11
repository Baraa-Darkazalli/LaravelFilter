<?php

namespace BaraaDark\LaravelFilter\Factories;

use BaraaDark\LaravelFilter\Exceptions\InvalidFilterClassException;
use BaraaDark\LaravelFilter\Exceptions\InvalidFilterKeyException;
use BaraaDark\LaravelFilter\Exceptions\MissingFiltersKeysMethodException;
use BaraaDark\LaravelFilter\Filter;

class FilterFactory
{
    /**
     *  Create method for filter facotry
     *  @param string $filterKey
     *  @param array $fitlerData
     *  @param \App\Models\Model|\App\Models\AuthModel $model
     *  @return Filter
     *  @throws InvalidFilterKeyException
     *  @throws InvalidFilterClassException
     *  @throws MissingFiltersKeysMethodException
     */
    public static function create($filterKey, $filterData, $model): Filter
    {
        if (method_exists($model, 'filtersKeys'))
        {
            $paths = $model->filtersKeys();

            if(!key_exists($filterKey, $paths))
            {
                throw new InvalidFilterKeyException($filterKey);
            }

            $filterClassPath = $paths[$filterKey];
            if(class_exists($filterClassPath))
            {
                return new $filterClassPath($filterData);
            }

            throw new InvalidFilterClassException($filterClassPath);
        }

        throw new MissingFiltersKeysMethodException($model);
    }
}
