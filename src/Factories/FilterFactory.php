<?php

namespace BaraaDark\LaravelFilter\Factories;

use BaraaDark\LaravelFilter\Filter;

class FilterFactory
{
    /**
     *  Create method for filter facotry
     *  @param string $filterKey
     *  @param array $fitlerData
     *  @param \App\Models\Model|\App\Models\AuthModel $model
     *  @return Filter
     *  @throws \App\Exceptions\ErrorMsgException
     */
    public static function create($filterKey, $filterData, $model): Filter
    {
        if (method_exists($model, 'filtersKeys'))
        {
            $paths = $model->filtersKeys();

            if(!key_exists($filterKey, $paths))
            {
                throwError('Invalid filter key');
            }

            $filterClassPath = $paths[$filterKey];
            if(class_exists($filterClassPath))
            {
                return new $filterClassPath($filterData);
            }

            throwError('Trying to declare invalid filter class');
        }

        throwError(get_class($model) . ' Model doesnt have filtersKeys method');
    }
}
