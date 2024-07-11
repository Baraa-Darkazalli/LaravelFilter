# LaravelFilter
LaravelFilter is a package designed to simplify the process of filtering table fields in a Laravel project. It provides a straightforward way to implement custom query filters for your models.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/baraaDark/laravel-filter.svg?style=flat-square)](https://packagist.org/packages/baraadark/laravel-filter)
[![Total Downloads](https://img.shields.io/packagist/dt/baraadark/laravel-filter.svg?style=flat-square)](https://packagist.org/packages/baraadark/laravel-filter)

----------

# Getting started

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/10.x/installation)

You can install the package via composer:
``` bash
composer require baraadark/laravel-filter:dev-main
```

Next, publish the configuration file:
``` bash
php artisan vendor:publish --tag=config
```

## Usage

### Applying the Filterable Trait
Use the Filterable trait in your models to enable filtering.
``` php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BaraaDark\LaravelFilter\Traits\Filterable;

class YourModel extends Model
{
    use Filterable;
}
```

### Override filterKeys function
You should override the filtersKeys method to return an array of filter keys and their corresponding filter classes.

**Example:** 
``` php
use BaraaDark\LaravelFilter\Traits\Filterable;

class YourModel extends Model
{
    use Filterable;

    public function filtersKeys(): array
    {
        return [
            // 'filter-key' => FilterKeyFilter::class
        ];
    }
}
```
The filtersKeys method returns an associative array where the keys are the names of the filter keys expected from the request, and the values are the filter classes that contain the query logic.

### Creating a Filter Class
To create a Filter class, run the command:
``` bash
php artisan make:filter
```
You will be prompted to enter the class name and the related model name. The generated file will be located at App\Http\Filters\ModelName.

### Filter Class Structure

``` php
namespace App\Http\Filters\ModelName;

use BaraaDark\LaravelFilter\Filter;

class FilterClass extends Filter
{
    public function __construct(array $filterData)
    {
        parent::__construct($filterData);
    }

    /**
     * Get the validation rules that apply to the filter request.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [];
    }

    /**
     * Apply filter query on the related model.
     *
     * @param \Illuminate\Database\Eloquent\Builder &$query
     */
    public function apply(&$query)
    {
        return $query;
    }
}
```

**Example Filter Class:**
``` php
use BaraaDark\LaravelFilter\Filter;

class ProductPriceRangeFilter extends Filter
{
    public function __construct(array $filterData)
    {
        parent::__construct($filterData);
    }

    /**
     * Get the validation rules that apply to the filter request.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'min'   => ['required', 'numeric', 'min:0'],
            'max'   => ['required', 'numeric']
        ];
    }

    /**
     * Apply filter query on related model.
     * @param  \Illuminate\Database\Eloquent\Builder &$query
     */
    public function apply(&$query)
    {
        return $query->where('price', '>=', $this->min)
            ->where('price', '<=', $this->max);
    }
}
```
**Product model:**
``` php
use BaraaDark\LaravelFilter\Traits\Filterable;
use App\Http\Filters\Product\ProductPriceRangeFilter;

class Product extends Model
{
    use HasFactory, Filterable;

    protected $guarded = [];

    public function filtersKeys(): array
    {
        return [
            'price-range'   => ProductPriceRangeFilter::class
        ];
    }
}
```

## Applying Filters
Filters can be applied by sending a request with the following structure in the body:
``` json
{
    "filters": {
        "filter_key": {
            "filter_class_key": "value",
            "filter_class_key": "value"
        }
    }
}
```
**Example:**
``` json
{
    "filters": {
        "price-range": {
            "min": 500000,
            "max": 1000000
        }
    }
}
```

## Global vs. Local Scope
If apply_global_scope is set to true in the configuration file, filters will be applied globally to all models when the filters are included in the request. This is not recommended as a general setting since multiple models might be used in the same function, and you might want to apply the filter only to the main model manually.

### Configuration
To configure global scope:

``` php
// config/laravel-filter.php

return [
    'apply_global_scope' => false, // Set to true to enable global scope
];
```

### Using Local Scope
If apply_global_scope is set to false, you can manually apply the filter in your controller:

``` php
use App\Models\SubjectCategory;

public function index(): LengthAwarePaginator
{
    return SubjectCategory::applyFilter()->paginate();
}
```

## Note
Remember to make routes that use filtering either POST or match(['post', 'get']) since the request contains data.