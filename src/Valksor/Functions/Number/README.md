# Valksor Functions: Number

[![BSD-3-Clause](https://img.shields.io/badge/BSD--3--Clause-green?style=flat)](https://github.com/valksor/php-functions-number/blob/master/LICENSE)
[![Coverage Status](https://coveralls.io/repos/github/valksor/php-functions-number/badge.svg?branch=master)](https://coveralls.io/github/valksor/php-functions-number?branch=master)

A PHP library providing various number-related functions including prime number checking, distance calculations, and mathematical operations.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-number
```

## Requirements

PHP 8.4 or higher

### Optional Extensions

- **ext-gmp**: The GNU Multiple Precision (GMP) extension is recommended for efficient prime number checking, especially for large numbers. The package will use alternative algorithms if GMP is not available, but GMP provides better performance for prime number operations.

## Usage

There are two ways to use this package: via the Functions class or by directly using the traits.

### Using the Functions Class

The Functions class provides various number-related functions for mathematical operations and checks.

```php
use Valksor\Functions\Number\Functions;

// Create a new Functions instance
$number = new Functions();

// Check if a number is prime
$isPrime = $number->isPrime(17);

// Calculate distance between two geographical points
$distance = $number->distanceBetweenPoints(
    40.7128, -74.0060,  // New York (latitude, longitude)
    34.0522, -118.2437, // Los Angeles (latitude, longitude)
    true,               // Return distance in kilometers
    2                   // Precision (decimal places)
);

// Calculate greatest common divisor
$gcd = $number->greatestCommonDivisor(48, 18);

// Calculate least common multiple
$lcm = $number->leastCommonMultiple(12, 15);

// Check if a value is an integer
$isInt = $number->isInt('123');

// Check if a value is a float
$isFloat = $number->isFloat('123.45');

// Swap two numbers
[$a, $b] = $number->swap(5, 10);
```

### Using Traits Directly

Alternatively, you can use the traits directly in your own classes:

```php
use Valksor\Functions\Number\Traits\_IsPrime;
use Valksor\Functions\Number\Traits\_DistanceBetweenPoints;
use Valksor\Functions\Number\Traits\_GreatestCommonDiviser;

class MyClass
{
    // Import the traits you need
    use _IsPrime;
    use _DistanceBetweenPoints;
    use _GreatestCommonDiviser;

    public function doSomething(): void
    {
        // Use the methods directly
        $isPrime = $this->isPrime(17);
        $distance = $this->distanceBetweenPoints(40.7128, -74.0060, 34.0522, -118.2437);
        $gcd = $this->greatestCommonDivisor(48, 18);
    }
}
```

Note that some traits may depend on other helper traits. For example, the `_IsPrime` trait uses `_IsPrimal`, `_IsPrimeBelow1000`, and `_IsPrimeGmp` internally. The Functions class handles these dependencies for you, but if you use the traits directly, you may need to include these helper traits as well.

## Features

For a complete list of all functions available in this package, see [Features](docs/features.md).

## Contributing

Contributions are welcome!

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to Number functions:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-number-function`)
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
- **Stack Overflow**: Use tag `valksor-php-functions-number`

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[PHP Math Functions](https://www.php.net/manual/en/book.math.php)** - Core mathematics functionality inspiration
- **[GMP Extension](https://www.php.net/manual/en/book.gmp.php)** - High-precision arithmetic inspiration
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

If you find this Number component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
