# Valksor Functions: Sort

[![BSD-3-Clause](https://img.shields.io/badge/BSD--3--Clause-green?style=flat)](https://github.com/valksor/php-functions-sort/blob/master/LICENSE)
[![Coverage Status](https://coveralls.io/repos/github/valksor/php-functions-sort/badge.svg?branch=master)](https://coveralls.io/github/valksor/php-functions-sort?branch=master)

A PHP library providing various sorting algorithms and utilities for arrays and objects.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-sort
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

## Contributing

Contributions are welcome!

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to Sort functions:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-sort-function`)
3. Implement your function following existing patterns
4. Add comprehensive tests
5. Ensure all tests pass and code style is correct
6. Submit a pull request

## Security

If you discover any security-related issues, please email us at packages@valksor.com instead of using the issue tracker.

## Support

- **Documentation**: [Full documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues) for bug reports and feature requests
- **Discussions**: [GitHub Discussions](https://github.com/valksor/php-valksor/discussions) for questions and community support
- **Stack Overflow**: Use tag `valksor-php-functions-sort`

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[Algorithms Community](https://en.wikipedia.org/wiki/Sorting_algorithm)** - Sorting algorithm inspiration and research
- **[Valksor Project](https://github.com/valksor)** - Part of the larger Valksor PHP ecosystem

## License

This package is licensed under the [BSD-3-Clause License](LICENSE).

## About Valksor

This package is part of the [valksor/php-valksor](https://github.com/valksor/php-valksor) project - a comprehensive PHP library and Symfony bundle that provides a collection of utilities, components, and integrations for Symfony applications.

The main project includes:

- Various utility functions and components
- Doctrine ORM tools and extensions
- Symfony bundle for easy configuration
- And much more

If you find this Sort component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
