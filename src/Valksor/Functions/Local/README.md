# Valksor Functions: Local

[![BSD-3-Clause](https://img.shields.io/badge/BSD--3--Clause-green?style=flat)](https://github.com/valksor/php-functions-local/blob/master/LICENSE)
[![Coverage Status](https://coveralls.io/repos/github/valksor/php-functions-local/badge.svg?branch=master)](https://coveralls.io/github/valksor/php-functions-local?branch=master)

A PHP library providing utility functions for working with the local filesystem, environment variables, system resources, and more.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-local
```

## Requirements

PHP 8.4 or higher

## Usage

There are two ways to use this package: via the Functions class or by directly using the traits.

### Using the Functions Class

The Functions class provides utility functions for working with the local filesystem, environment variables, system resources, and more.

```php
use Valksor\Functions\Local\Functions;

// Create a new Functions instance
$local = new Functions();

// Check if a file exists in the current working directory
$exists = $local->fileExistsCwd('filename.txt');

// Create a directory
$created = $local->mkdir('/path/to/directory');

// Get an environment variable
$value = $local->getenv('VARIABLE_NAME');

// Format a file size in a human-readable way
$size = $local->humanFileSize(1024 * 1024); // "1.00M"

// Check if PHP extensions or Composer packages are installed
$installed = $local->isInstalled(['package1', 'package2']);

// Check if a class from a package will be available at runtime
$available = $local->willBeAvailable('package-name', 'Namespace\\ClassName', ['parent-package']);

// Get the cURL user agent string
$userAgent = $local->getCurlUserAgent();
```

### Using Traits Directly

Alternatively, you can use the traits directly in your own classes:

```php
use Valksor\Functions\Local\Traits\_FileExistsCwd;
use Valksor\Functions\Local\Traits\_MkDir;
use Valksor\Functions\Local\Traits\_GetEnv;

class MyClass
{
    // Import the traits you need
    use _FileExistsCwd;
    use _MkDir;
    use _GetEnv;

    public function doSomething(): void
    {
        // Use the methods directly
        $exists = $this->fileExistsCwd('filename.txt');
        $created = $this->mkdir('/path/to/directory');
        $value = $this->getenv('VARIABLE_NAME');
    }
}
```

## Features

- **Filesystem operations**: Check file existence, create directories
- **Environment variables**: Secure access to environment configuration
- **System resources**: Get system information and resource details
- **File size formatting**: Human-readable file size representations
- **Package detection**: Check for installed PHP extensions and Composer packages
- **Runtime availability**: Predict if classes will be available at runtime
- **cURL utilities**: Get cURL user agent information
- **Trait-based architecture**: Use individual traits for specific functionality

For a complete list of all functions available in this package, see [Features](docs/features.md).

## Contributing

Contributions are welcome!

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to Local functions:

1. Fork repository
2. Create a feature branch (`git checkout -b feature/new-local-function`)
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
- **Stack Overflow**: Use tag `valksor-php-functions-local`

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

If you find this Local component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
