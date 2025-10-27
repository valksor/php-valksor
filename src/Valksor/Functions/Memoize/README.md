# Valksor Functions: Memoize

A PHP library providing memoization functionality to cache the results of expensive function calls and return the cached result when the same inputs occur again.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-memoize
```

## Requirements

PHP 8.4 or higher

## Usage

The package provides two classes for memoization:

1. `Memoize` - The base class that provides a caching mechanism to store and retrieve values based on a context and keys.
2. `MemoizeCache` - A conditional class that extends either `RequestCache` (if available in a Symfony environment) or falls back to `Memoize`. This provides seamless integration with Symfony's dependency injection when used as part of the Valksor bundle.

### Basic Usage

```php
use Valksor\Functions\Memoize\Memoize;
use YourNamespace\YourEnum;

// Create a new Memoize instance
$memoize = new Memoize();

// Cache the result of an expensive function call
$result = $memoize->memoize(
    YourEnum::SOME_VALUE,  // Context (must be a BackedEnum)
    'your-key',            // Key
    function() {           // Callback function whose result will be cached
        // Expensive operation here
        return $expensiveResult;
    }
);

// Later, retrieve the cached value
$cachedResult = $memoize->value(
    YourEnum::SOME_VALUE,  // Same context
    'your-key',            // Same key
    null                   // Default value if not found
);
```

### Using MemoizeCache

```php
use Valksor\Functions\Memoize\MemoizeCache;
use YourNamespace\YourEnum;

// Create a new MemoizeCache instance
$memoizeCache = new MemoizeCache();

// Use it the same way as Memoize
$result = $memoizeCache->memoize(
    YourEnum::SOME_VALUE,  // Context (must be a BackedEnum)
    'your-key',            // Key
    function() {           // Callback function whose result will be cached
        // Expensive operation here
        return $expensiveResult;
    }
);
```

In a Symfony environment with the Valksor bundle installed, you can inject MemoizeCache as a service:

```php
use Valksor\Functions\Memoize\MemoizeCache;

class YourService
{
    public function __construct(
        private readonly MemoizeCache $memoizeCache,
    ) {
    }

    public function someMethod()
    {
        // Use $this->memoizeCache
    }
}
```

### Advanced Usage with Nested Keys

You can use subkeys for more complex caching scenarios (this works with both `Memoize` and `MemoizeCache`):

```php
// Cache with nested keys
$result = $memoize->memoize(
    YourEnum::SOME_VALUE,  // Context
    'parent-key',          // Main key
    function() {           // Callback
        return $expensiveResult;
    },
    false,                 // Don't refresh the cache
    'child-key',           // Subkey
    'grandchild-key'       // Another level of subkey
);

// Retrieve nested value
$cachedResult = $memoize->value(
    YourEnum::SOME_VALUE,  // Same context
    'parent-key',          // Same main key
    null,                  // Default value
    'child-key',           // Same subkey path
    'grandchild-key'       // Same subkey path
);
```

### Refreshing the Cache

You can force a refresh of the cached value (this works with both `Memoize` and `MemoizeCache`):

```php
$result = $memoize->memoize(
    YourEnum::SOME_VALUE,
    'your-key',
    function() {
        return $newExpensiveResult;
    },
    true  // Set refresh to true to force recalculation
);
```

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

If you find this Memoize component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
