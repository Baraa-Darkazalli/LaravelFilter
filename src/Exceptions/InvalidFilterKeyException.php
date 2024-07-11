<?php

namespace BaraaDark\LaravelFilter\Exceptions;

use Exception;

class InvalidFilterKeyException extends Exception
{
    public function __construct($key, $className)
    {
        $message = "Invalid key '$key' in '$className'.";
        parent::__construct($message);
    }
}