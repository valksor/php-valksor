# Valksor Functions: Web

[![Latest Version on Packagist](https://img.shields.io/packagist/v/valksor/php-functions-web.svg)](https://packagist.org/packages/valksor/php-functions-web)
[![Total Downloads](https://img.shields.io/packagist/dt/valksor/php-functions-web.svg)](https://packagist.org/packages/valksor/php-functions-web)
[![License](https://img.shields.io/packagist/l/valksor/php-functions-web.svg)](LICENSE)
[![PHP Version Require](https://img.shields.io/packagist/require-v/valksor/php-functions-web/php)](https://packagist.org/packages/valksor/php-functions-web)

A PHP library providing a collection of web-related utility functions for handling HTTP requests, validating web data, and working with URLs.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-web
```

## Requirements

PHP 8.4 or higher
Symfony HttpFoundation component

## Usage

There are two ways to use this package: via the Functions class or by directly using the traits.

### Using the Functions Class

The Functions class provides a comprehensive set of web-related utility functions.

```php
use Valksor\Functions\Web\Functions;

// Create a new Functions instance
$web = new Functions();

// Validate an IP address
$isValid = $web->validateIPAddress('192.168.1.1');

// Validate an email address
$isValid = $web->validateEmail('user@example.com');

// Check if a URL is absolute
$isAbsolute = $web->isAbsolute('https://example.com');

// Get remote IP address from a request
$ip = $web->remoteIp($request);

// Check if a request is using HTTPS
$isHttps = $web->isHttps($request);

// Encode a URL
$encoded = $web->urlEncode('https://example.com?query=value&special=value with spaces');
```

### Using Traits Directly

Alternatively, you can use the traits directly in your own classes:

```php
use Valksor\Functions\Web\Traits\_ValidateIPAddress;
use Valksor\Functions\Web\Traits\_ValidateEmail;
use Valksor\Functions\Web\Traits\_IsAbsolute;

class MyClass
{
    // Import the traits you need
    use _ValidateIPAddress;
    use _ValidateEmail;
    use _IsAbsolute;

    public function doSomething(): void
    {
        // Use the methods directly
        $isValidIp = $this->validateIPAddress('192.168.1.1');
        $isValidEmail = $this->validateEmail('user@example.com');
        $isAbsoluteUrl = $this->isAbsolute('https://example.com');
    }
}
```

Note that some traits may depend on other helper traits. The Functions class handles these dependencies for you, but if you use the traits directly, you may need to include these helper traits as well.

## Features

For a complete list of all functions available in this package, see [Features](docs/features.md).


## Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) for details on:

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to Web functions:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-web-function`)
3. Implement your function following existing patterns
4. Add comprehensive tests
5. Ensure all tests pass and code style is correct
6. Submit a pull request

## Security

If you discover any security-related issues, please email us at security@valksor.dev instead of using the issue tracker.

For security policy and vulnerability reporting guidelines, please see our [Security Policy](SECURITY.md).

## Support

- **Documentation**: [Full documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues) for bug reports and feature requests
- **Discussions**: [GitHub Discussions](https://github.com/valksor/php-valksor/discussions) for questions and community support
- **Stack Overflow**: Use tag `valksor-php-functions-web`

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[Symfony HTTP Foundation](https://symfony.com/doc/current/components/http_foundation.html)** - Request handling inspiration
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

If you find this Web component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
