# Valksor Functions: Preg

A PHP library providing enhanced regular expression (preg_*) functions with improved error handling and UTF-8 support.

## Installation

Install the package via Composer:

```bash
composer require valksor/functions-preg
```

## Requirements

PHP 8.4 or higher

## Usage

There are two ways to use this package: via the Functions class or by directly using the traits.

### Using the Functions Class

The Functions class provides enhanced versions of PHP's preg_* functions with better error handling and UTF-8 support.

```php
use Valksor\Functions\Preg\Functions;

// Create a new Functions instance
$preg = new Functions();

// Use enhanced preg_match
$isMatch = $preg->match('/pattern/', 'subject string', $matches);

// Use enhanced preg_replace
$replaced = $preg->replace('/pattern/', 'replacement', 'subject string');

// Use enhanced preg_split
$parts = $preg->split('/delimiter/', 'string to split');
```

### Using Traits Directly

Alternatively, you can use the traits directly in your own classes:

```php
use Valksor\Functions\Preg\Traits\_Match;
use Valksor\Functions\Preg\Traits\_Replace;
use Valksor\Functions\Preg\Traits\_Split;

class MyClass
{
    // Import the traits you need
    use _Match;
    use _Replace;
    use _Split;

    public function doSomething(): void
    {
        // Use the methods directly
        $isMatch = $this->match('/pattern/', 'subject string', $matches);
        $replaced = $this->replace('/pattern/', 'replacement', 'subject string');
        $parts = $this->split('/delimiter/', 'string to split');
    }
}
```

Note that some traits may depend on other helper traits. For example, the `_Match` trait uses `_AddUtf8Modifier`, `_NewPregException`, and `_RemoveUtf8Modifier` internally. The Functions class handles these dependencies for you, but if you use the traits directly, you may need to include these helper traits as well.

### UTF-8 Support

The library automatically handles UTF-8 patterns by trying to add or remove the UTF-8 modifier as needed:

```php
// Will automatically try with and without UTF-8 modifier
$isMatch = $preg->match('/pattern/u', 'subject with UTF-8 characters');
```

### Error Handling

The library provides improved error handling by throwing exceptions with detailed information when regex errors occur:

```php
try {
    $result = $preg->match('/invalid[pattern/', 'subject');
} catch (\Exception $e) {
    // Handle the exception with detailed error information
    echo $e->getMessage();
}
```

### SkipErrorHandler

The SkipErrorHandler executes a callback while capturing PHP errors and converting them to exceptions:

```php
use Valksor\Functions\Preg\SkipErrorHandler;

try {
    $result = SkipErrorHandler::execute(function() {
        // Code that might trigger PHP errors
        $value = @file_get_contents('non-existent-file.txt');
        return $value;
    });
} catch (RuntimeException $e) {
    // Handle the error
    echo "Error: " . $e->getMessage();
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

If you find this Preg component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/valksor
```
