# Valksor Functions

A comprehensive collection of PHP utility functions and helpers for various tasks including date manipulation, web operations, text processing, and more. This is a meta-package that includes all the Valksor function sub-libraries.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions
```

This will install all the function sub-libraries at once.

## Requirements

- PHP 8.4 or higher
- Various PHP extensions (curl, json, random)
- Symfony components (http-foundation, intl, process, property-access, routing, string)

## Included Sub-libraries

This meta-package includes the following function libraries:

- [valksor/php-functions-date](Date) - Date and time manipulation utilities
- [valksor/php-functions-handler](Handler) - Error and exception handling utilities
- [valksor/php-functions-iteration](Iteration) - Array and collection iteration utilities
- [valksor/php-functions-latvian](Latvian) - Latvian language specific utilities
- [valksor/php-functions-local](Local) - Localization and internationalization utilities
- [valksor/php-functions-memoize](Memoize) - Function result caching utilities
- [valksor/php-functions-number](Number) - Number manipulation and formatting utilities
- [valksor/php-functions-pagination](Pagination) - Pagination utilities for arrays and collections
- [valksor/php-functions-php](Php) - PHP language enhancement utilities
- [valksor/php-functions-preg](Preg) - Regular expression utilities
- [valksor/php-functions-queue](Queue) - FIFO queue implementation for managing collections of items
- [valksor/php-functions-sort](Sort) - Sorting algorithms and utilities
- [valksor/php-functions-text](Text) - Text processing and manipulation utilities
- [valksor/php-functions-web](Web) - Web-related utilities for HTTP requests, URLs, etc.

Each sub-library can also be installed individually if you only need specific functionality.

## Usage

Each sub-library has its own usage instructions. Please refer to the README.md file in each sub-library's directory for specific usage examples.

Generally, there are two ways to use these libraries:

1. Via the Functions class provided by each sub-library
2. By directly using the traits in your own classes

## License

This package is licensed under the [BSD-3-Clause License](LICENSE).

## About Valksor

This package is part of the [valksor/php-valksor](https://github.com/valksor/php-valksor) project - a comprehensive PHP library and Symfony bundle that provides a collection of utilities, components, and integrations for Symfony applications.

The main project includes:
- Various utility functions and components
- Doctrine ORM tools and extensions
- Symfony bundle for easy configuration
- And much more

If you find these function components useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
