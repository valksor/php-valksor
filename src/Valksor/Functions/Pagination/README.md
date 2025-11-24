# Valksor Functions: Pagination

[![valksor](https://badgen.net/static/org/valksor/green)](https://github.com/valksor) 
[![BSD-3-Clause](https://img.shields.io/badge/BSD--3--Clause-green?style=flat)](https://github.com/valksor/php-functions-pagination/blob/master/LICENSE)
[![Coverage Status](https://coveralls.io/repos/github/valksor/php-functions-pagination/badge.svg?branch=master)](https://coveralls.io/github/valksor/php-functions-pagination?branch=master)

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

- **Smart page generation**: Intelligently generates page numbers for UI pagination controls
- **Omitted page handling**: Uses indicators to show where pages are omitted in large sets
- **Configurable visible pages**: Control how many page numbers are shown
- **Flexible indicators**: Customize the indicator value for omitted pages
- **Input validation**: Comprehensive validation of all input parameters
- **Multiple scenarios**: Handles current page at beginning, middle, or end of page set

For a complete list of all functions available in this package, see [Features](docs/features.md).

## Validation

The class performs several validations:

- The number of visible pages must be at least 5
- The total number of pages must be at least 1
- The current page must be between 1 and the total number of pages
- The indicator value must not be a valid page number (between 1 and total)

## Contributing

Contributions are welcome!

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to Pagination functions:

1. Fork repository
2. Create a feature branch (`git checkout -b feature/new-pagination-function`)
3. Implement your function following existing patterns
4. Add comprehensive tests including edge cases
5. Ensure all tests pass and code style is correct
6. Submit a pull request

## Security

If you discover any security-related issues, please email us at packages@valksor.com instead of using the issue tracker.

## Support

- **Documentation**: [Full documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues) for bug reports and feature requests
- **Discussions**: [GitHub Discussions](https://github.com/valksor/php-valksor/discussions) for questions and community support
- **Stack Overflow**: Use tag `valksor-php-functions-pagination`

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[Relevant PHP Documentation](https://www.php.net/manual/en/)** - Core PHP functionality inspiration
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

If you find this Pagination component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
