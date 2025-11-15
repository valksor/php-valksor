# Valksor Functions: Latvian

[![BSD-3-Clause](https://img.shields.io/badge/BSD--3--Clause-green?style=flat)](https://github.com/valksor/php-functions-latvian/blob/master/LICENSE)
[![Coverage Status](https://coveralls.io/repos/github/valksor/php-functions-latvian/badge.svg?branch=master)](https://coveralls.io/github/valksor/php-functions-latvian?branch=master)

A PHP library providing functions specific to Latvian language and data formats, including Latvian text comparison, sorting according to Latvian alphabet rules, and validation of Latvian personal identification codes.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-latvian
```

## Requirements

PHP 8.4 or higher

## Usage

There are two ways to use this package: via the Functions class or by directly using the traits.

### Using the Functions Class

The Functions class provides utilities for working with Latvian-specific data and text.

```php
use Valksor\Functions\Latvian\Functions;

// Create a new Functions instance
$functions = new Functions();

// Validate a Latvian personal identification code
$isValid = $functions->validatePersonCode('32XXXXXXXXX'); // For new format
$isValid = $functions->validatePersonCode('XXXXXXXXXXX'); // For old format

// Sort an array of names according to Latvian alphabet rules
$names = [
    ['name' => 'Ēriks'],
    ['name' => 'Andris'],
    ['name' => 'Čalis'],
    ['name' => 'Zane'],
];
$functions->sortLatvian($names, 'name');
// Result: Andris, Čalis, Ēriks, Zane

// Compare two strings using Latvian alphabet rules
$result = $functions->compare(['name' => 'Ēriks'], ['name' => 'Andris'], 'name');
// Result: positive integer (Ēriks comes after Andris in Latvian alphabet)
```

### Using Traits Directly

Alternatively, you can use the traits directly in your own classes:

```php
use Valksor\Functions\Latvian\Traits\_Compare;
use Valksor\Functions\Latvian\Traits\_SortLatvian;
use Valksor\Functions\Latvian\Traits\_ValidatePersonCode;

class MyClass
{
    // Import the traits you need
    use _Compare;
    use _SortLatvian;
    use _ValidatePersonCode;

    public function doSomething(): void
    {
        // Validate a Latvian personal identification code
        $isValid = $this->validatePersonCode('32XXXXXXXXX');

        // Sort an array of names according to Latvian alphabet rules
        $names = [
            ['name' => 'Ēriks'],
            ['name' => 'Andris'],
            ['name' => 'Čalis'],
            ['name' => 'Zane'],
        ];
        $this->sortLatvian($names, 'name');

        // Compare two strings using Latvian alphabet rules
        $result = $this->compare(['name' => 'Ēriks'], ['name' => 'Andris'], 'name');
    }
}
```

Note that some traits may depend on other helper traits. For example, the `_ValidatePersonCode` trait uses `_ValidatePersonCodeNew` and `_ValidatePersonCodeOld` internally. The Functions class handles these dependencies for you, but if you use the traits directly, you may need to include these helper traits as well.

## Features

For a complete list of all functions available in this package, see [Features](docs/features.md).

## Contributing

Contributions are welcome!

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to Latvian functions:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-latvian-function`)
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
- **Stack Overflow**: Use tag `valksor-php-functions-latvian`

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[Latvian Language Resources](https://www.lv/)** - Latvian language standards and inspiration
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

If you find this Latvian functions component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
