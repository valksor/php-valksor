# Valksor Functions: Sort

A PHP library providing various sorting algorithms and utilities for arrays and objects.

## Installation

Install the package via Composer:

```bash
composer require valksor/functions-sort
```

## Requirements

PHP 8.4 or higher

## Usage

There are two ways to use this package: via the Functions class or by directly using the traits.

### Using the Functions Class

The Functions class provides various sorting algorithms and utilities for arrays and objects.

```php
use Valksor\Functions\Sort\Functions;

// Create a new Functions instance
$sort = new Functions();

// Use bubble sort
$array = [3, 1, 4, 1, 5, 9, 2, 6, 5];
$sort->bubbleSort($array);
// $array is now sorted: [1, 1, 2, 3, 4, 5, 5, 6, 9]

// Use merge sort
$array = [3, 1, 4, 1, 5, 9, 2, 6, 5];
$sortedArray = $sort->mergeSort($array);
// $sortedArray is now [1, 1, 2, 3, 4, 5, 5, 6, 9]

// Sort an array of objects by a specific parameter
$objects = [
    ['id' => 3, 'name' => 'Charlie'],
    ['id' => 1, 'name' => 'Alice'],
    ['id' => 2, 'name' => 'Bob'],
];
$sortedObjects = $sort->sortByParameter($objects, 'name');
// $sortedObjects is now sorted by name: Alice, Bob, Charlie
```

### Using Traits Directly

Alternatively, you can use the traits directly in your own classes:

```php
use Valksor\Functions\Sort\Traits\_BubbleSort;
use Valksor\Functions\Sort\Traits\_MergeSort;
use Valksor\Functions\Sort\Traits\_SortByParameter;

class MyClass
{
    // Import the traits you need
    use _BubbleSort;
    use _MergeSort;
    use _SortByParameter;

    public function doSomething(): void
    {
        // Use bubble sort
        $array = [3, 1, 4, 1, 5, 9, 2, 6, 5];
        $this->bubbleSort($array);

        // Use merge sort
        $array = [3, 1, 4, 1, 5, 9, 2, 6, 5];
        $sortedArray = $this->mergeSort($array);

        // Sort by parameter
        $objects = [
            ['id' => 3, 'name' => 'Charlie'],
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ];
        $sortedObjects = $this->sortByParameter($objects, 'name');
    }
}
```

Note that some traits may depend on other helper traits. For example, the `_SortByParameter` trait uses `_Usort` and `_IsSortable` internally. The Functions class handles these dependencies for you, but if you use the traits directly, you may need to include these helper traits as well.

## Features

For a complete list of all functions available in this package, see [Features](docs/features.md).

## License

This package is licensed under the [BSD-3-Clause License](LICENSE).

## About Valksor

This package is part of the [valksor/valksor](https://github.com/valksor/valksor) project - a comprehensive PHP library and Symfony bundle that provides a collection of utilities, components, and integrations for Symfony applications.

The main project includes:
- Various utility functions and components
- Doctrine ORM tools and extensions
- Symfony bundle for easy configuration
- And much more

If you find this Sort component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/valksor
```
