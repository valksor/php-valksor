# Valksor Functions: Text

A PHP library providing enhanced text manipulation functions for string operations, formatting, and transformations.

## Installation

Install the package via Composer:

```bash
composer require valksor/functions-text
```

## Requirements

PHP 8.4 or higher

## Usage

There are two ways to use this package: via the Functions class or by directly using the traits.

### Using the Functions Class

The Functions class provides a wide range of text manipulation functions.

```php
use Valksor\Functions\Text\Functions;

// Create a new Functions instance
$text = new Functions();

// Convert text to camelCase
$camelCased = $text->camelCase('hello_world'); // Returns "helloWorld"

// Generate a random string
$randomString = $text->randomString(16); // Returns a 16-character random string

// Sanitize text for safe display
$sanitized = $text->sanitize('<script>alert("XSS")</script>'); // Removes HTML tags

// Convert between Cyrillic and Latin
$latin = $text->cyrillicToLatin('Привет мир'); // Converts Cyrillic to Latin
$cyrillic = $text->latinToCyrillic('Privet mir'); // Converts Latin to Cyrillic

// Check if a string starts with any of the given substrings
$startsWithAny = $text->strStartsWithAny('Hello world', ['Hello', 'Hi']); // Returns true

// Limit text by characters or words
$limited = $text->limitChars('This is a long text', 10); // Returns "This is a..."
$limitedWords = $text->limitWords('This is a long text', 2); // Returns "This is..."
```

### Using Traits Directly

Alternatively, you can use the traits directly in your own classes:

```php
use Valksor\Functions\Text\Traits\_CamelCase;
use Valksor\Functions\Text\Traits\_RandomString;
use Valksor\Functions\Text\Traits\_Sanitize;

class MyClass
{
    // Import the traits you need
    use _CamelCase;
    use _RandomString;
    use _Sanitize;

    public function doSomething(): void
    {
        // Use the methods directly
        $camelCased = $this->camelCase('hello_world');
        $randomString = $this->randomString(16);
        $sanitized = $this->sanitize('<script>alert("XSS")</script>');
    }
}
```

Note that some traits may depend on other helper traits. The Functions class handles these dependencies for you, but if you use the traits directly, you may need to include these helper traits as well.

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

If you find this Text component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/valksor
```
