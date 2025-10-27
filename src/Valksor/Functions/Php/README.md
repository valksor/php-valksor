# Valksor Functions: PHP

A PHP library providing enhanced PHP utility functions for reflection, introspection, and general PHP programming tasks.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-php
```

## Requirements

PHP 8.4 or higher

## Usage

There are two ways to use this package: via the Functions class or by directly using the traits.

### Using the Functions Class

The Functions class provides a wide range of PHP utility functions.

```php
use Valksor\Component\Functions\Functions;

// Create a new Functions instance
$php = new Functions();

// Get methods of a class
$methods = $php->classMethods(SomeClass::class);

// Get methods of a class that are not inherited from a parent
$ownMethods = $php->classMethods(SomeClass::class, ParentClass::class);

// Get system information
$sysInfo = $php->systemInfo(); // Returns OS, architecture, and file extension info

// Convert an object to an array
$array = $php->array($someObject);

// Check if an attribute exists on a class or method
$hasAttribute = $php->attributeExists(SomeClass::class, SomeAttribute::class);

// Get class constants
$constants = $php->classConstants(SomeClass::class);
```

### Using Traits Directly

Alternatively, you can use the traits directly in your own classes:

```php
use Valksor\Functions\Php\Traits\_ClassMethods;
use Valksor\Functions\Php\Traits\_SystemInfo;
use Valksor\Functions\Php\Traits\_Array;

class MyClass
{
    // Import the traits you need
    use _ClassMethods;
    use _SystemInfo;
    use _Array;

    public function doSomething(): void
    {
        // Use the methods directly
        $methods = $this->classMethods(SomeClass::class);
        $sysInfo = $this->systemInfo();
        $array = $this->array($someObject);
    }
}
```

Note that some traits may depend on other helper traits. The Functions class handles these dependencies for you, but if you use the traits directly, you may need to include these helper traits as well.

## Features

For a complete list of all functions available in this package, see [Features](docs/features.md).

## License

This package is licensed under the [BSD-3-Clause License](LICENSE).

## About Valksor

This package is part of the [valksor/php-valksor](https://github.com/valksor/php-valksor) project - a comprehensive PHP library and Symfony bundle that provides a collection of utilities, components, and integrations for Symfony applications.

The main project includes:
- Various utility functions and components
- Doctrine ORM tools and extensions
- Symfony bundle for easy configuration
- And much more

If you find this PHP component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
