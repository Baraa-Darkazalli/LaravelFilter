<?php

namespace BaraaDark\LaravelFilter\Exceptions;

use Exception;

class MissingFiltersKeysMethodException extends Exception
{
    public function __construct($model)
    {
        $message = "Model '" . get_class($model) . "' doesn't have filtersKeys method.";
        parent::__construct($message);
    }
}