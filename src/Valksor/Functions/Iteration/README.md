# Valksor Functions: Iteration

[![valksor](https://badgen.net/static/org/valksor/green)](https://github.com/valksor)
[![BSD-3-Clause](https://img.shields.io/badge/BSD--3--Clause-green?style=flat)](https://github.com/valksor/php-functions-iteration/blob/master/LICENSE)
[![Coverage Status](https://coveralls.io/repos/github/valksor/php-functions-iteration/badge.svg?branch=master)](https://coveralls.io/github/valksor/php-functions-iteration?branch=master)
[![php](https://badgen.net/static/php/>=8.4/purple)](https://www.php.net/releases/8.4/en.php)

A PHP library providing enhanced array and iteration utility functions for array manipulation, transformation, and validation.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-iteration
```

## Requirements

PHP 8.4 or higher

## Usage

There are two ways to use this package: via the Functions class or by directly using the traits.

### Using the Functions Class

The Functions class provides a wide range of array and iteration utility functions.

```php
use Valksor\Functions\Iteration\Functions;

// Create a new Functions instance
$iteration = new Functions();

// Check if an array is associative
$isAssoc = $iteration->isAssociative(['key' => 'value']); // Returns true
$isAssoc = $iteration->isAssociative([1, 2, 3]); // Returns false

// Make a one-dimensional array multi-dimensional
$multiDim = $iteration->makeMultiDimensional([1, 2, 3]); // Returns [[1], [2], [3]]

// Get unique values from an array
$unique = $iteration->unique([1, 2, 2, 3, 3, 3]); // Returns [1, 2, 3]

// Check if an array is multi-dimensional
$isMultiDim = $iteration->isMultiDimensional([[1], [2]]); // Returns true

// Convert an array to a string
$string = $iteration->arrayToString(['a', 'b', 'c']); // Returns "a,b,c"

// JSON encode with error handling
$json = $iteration->jsonEncode(['key' => 'value']); // Returns '{"key":"value"}'
```

### Using Traits Directly

Alternatively, you can use the traits directly in your own classes:

```php
use Valksor\Functions\Iteration\Traits\_IsAssociative;
use Valksor\Functions\Iteration\Traits\_MakeMultiDimensional;
use Valksor\Functions\Iteration\Traits\_Unique;

class MyClass
{
    // Import the traits you need
    use _IsAssociative;
    use _MakeMultiDimensional;
    use _Unique;

    public function doSomething(): void
    {
        // Use the methods directly
        $isAssoc = $this->isAssociative(['key' => 'value']);
        $multiDim = $this->makeMultiDimensional([1, 2, 3]);
        $unique = $this->unique([1, 2, 2, 3, 3, 3]);
    }
}
```

Note that some traits may depend on other helper traits. The Functions class handles these dependencies for you, but if you use the traits directly, you may need to include these helper traits as well.

## Features

For a complete list of all functions available in this package, see [Features](docs/features.md).

## Contributing

Contributions are welcome!

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to Iteration functions:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-iteration-function`)
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
- **Stack Overflow**: Use tag `valksor-php-functions-iteration`

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[PHP SPL Documentation](https://www.php.net/manual/en/book.spl.php)** - Standard PHP Library inspiration
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

If you find this Iteration component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
