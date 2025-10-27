# Valksor Functions: Pagination

A PHP library providing pagination functionality to generate page numbers for UI pagination controls, with smart handling of large page sets.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-pagination
```

## Requirements

PHP 8.4 or higher

## Usage

The Pagination class provides a mechanism to generate pagination data for displaying page numbers in a UI, with intelligent handling of omitted pages.

### Basic Usage

```php
use Valksor\Functions\Pagination\Pagination;

// Create a new Pagination instance
$pagination = new Pagination();

// Generate pagination data
$pages = $pagination->paginate(
    7,       // Number of visible pages
    20,      // Total number of pages
    5,       // Current page
    -1       // Indicator for omitted pages (optional, defaults to -1)
);

// $pages will contain an array like: [1, 2, 3, 4, 5, 6, 7, -1, 20]
// where -1 represents omitted pages
```

### Different Pagination Scenarios

The pagination algorithm intelligently handles different scenarios:

#### All Pages Visible

When the total number of pages is less than or equal to the number of visible pages:

```php
$pages = $pagination->paginate(10, 8, 4); // [1, 2, 3, 4, 5, 6, 7, 8]
```

#### Single Omitted Section

When the current page is near the beginning or end:

```php
// Current page near beginning
$pages = $pagination->paginate(7, 20, 3); // [1, 2, 3, 4, 5, -1, 20]

// Current page near end
$pages = $pagination->paginate(7, 20, 18); // [1, -1, 16, 17, 18, 19, 20]
```

#### Two Omitted Sections

When the current page is in the middle of a large set:

```php
$pages = $pagination->paginate(7, 20, 10); // [1, -1, 8, 9, 10, 11, 12, -1, 20]
```

## Features

For a complete list of all functions available in this package, see [Features](docs/features.md).

## Validation

The class performs several validations:
- The number of visible pages must be at least 5
- The total number of pages must be at least 1
- The current page must be between 1 and the total number of pages
- The indicator value must not be a valid page number (between 1 and total)

## License

This package is licensed under the [BSD-3-Clause License](LICENSE).

## About Valksor

This package is part of the [valksor/php-valksor](https://github.com/valksor/php-valksor) project - a comprehensive PHP library and Symfony bundle that provides a collection of utilities, components, and integrations for Symfony applications.

The main project includes:
- Various utility functions and components
- Doctrine ORM tools and extensions
- Symfony bundle for easy configuration
- And much more

If you find this Pagination component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
