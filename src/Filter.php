<?php

namespace BaraaDark\LaravelFilter;

use BaraaDark\LaravelFilter\Exceptions\InvalidFilterKeyException;
use Exception;

abstract class Filter
{
    public function __construct(public array $filterData)
    {
        $this->validate($filterData);
    }

    /**
     * Apply filter query on related model.
     * @param  \Illuminate\Database\Eloquent\Builder &$query
     */
    abstract public function apply(&$query);

    /**
     * Get the validation rules that apply to the filter request.
     *
     * @return array
     */
    abstract public static function rules(): array;

    /**
     * Validate passed @param array $filterData according to filter rules
     */
    protected function validate($filterData): void
    {
        foreach ($filterData as $key => $value)
        {
            $this->validateKey($key, $value);
        }
    }

    /**
     * Get the validation rules that apply to the filter request.
     *
     * @return array
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function validateKey($key, $value): bool
    {
        $validator = validator([$key => $value], [$key => static::rules()[$key]]);
        if ($validator->fails())
        {
            $this->failedValidation($validator);
        }
        return true;
    }

     /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $exception = $validator->getException();

        throw (new $exception($validator))
                    ->errorBag($this->errorBag)
                    ->redirectTo($this->getRedirectUrl());
    }

    public function __get($key)
    {
        if (property_exists($this, $key))
        {
            return $this->$key;
        }

        $classPath = explode('\\',get_class($this));
        $className = $classPath[count($classPath) -1];

        return $this->filterData[$key] ?? throw new InvalidFilterKeyException($key, $className);
    }
}
