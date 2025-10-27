# Valksor Functions: Local

A PHP library providing utility functions for working with the local filesystem, environment variables, system resources, and more.

## Installation

Install the package via Composer:

```bash
composer require valksor/functions-local
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

If you find this Local component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/valksor
```
