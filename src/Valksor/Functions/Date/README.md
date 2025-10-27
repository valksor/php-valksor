# Valksor Functions: Date

A PHP library providing enhanced date and time manipulation functions with improved validation and formatting capabilities.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-date
```

## Requirements

PHP 8.4 or higher

## Usage

There are two ways to use this package: via the Functions class or by directly using the traits.

### Using the Functions Class

The Functions class provides enhanced date and time manipulation functions with better validation and formatting capabilities.

```php
use Valksor\Functions\Date\Functions;

// Create a new Functions instance
$date = new Functions();

// Format a date
$formatted = $date->formatDate('2023-01-15', 'Y-m-d');

// Validate a date
$isValid = $date->validateDate('15022023');

// Convert Excel date to regular date
$excelDate = $date->excelDate(44941, 'd-m-Y');

// Convert Unix timestamp to formatted date
$fromTimestamp = $date->fromUnixTimestamp(1673740800, 'd-m-Y H:i:s');

// Format time duration
$formattedTime = $date->format(3665); // "1 hour 1 minute 5 seconds"
```

### Using Traits Directly

Alternatively, you can use the traits directly in your own classes:

```php
use Valksor\Functions\Date\Traits\_FormatDate;
use Valksor\Functions\Date\Traits\_ValidateDate;
use Valksor\Functions\Date\Traits\_ExcelDate;

class MyClass
{
    // Import the traits you need
    use _FormatDate;
    use _ValidateDate;
    use _ExcelDate;

    public function doSomething(): void
    {
        // Use the methods directly
        $formatted = $this->formatDate('2023-01-15', 'Y-m-d');
        $isValid = $this->validateDate('15022023');
        $excelDate = $this->excelDate(44941, 'd-m-Y');
    }
}
```

Note that some traits may depend on other helper traits or components. The Functions class handles these dependencies for you, but if you use the traits directly, you may need to include these helper traits as well.

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

If you find this Date component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
