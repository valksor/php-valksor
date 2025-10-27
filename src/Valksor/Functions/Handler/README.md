# Valksor Functions: Handler

[![Latest Version on Packagist](https://img.shields.io/packagist/v/valksor/php-functions-handler.svg)](https://packagist.org/packages/valksor/php-functions-handler)
[![Total Downloads](https://img.shields.io/packagist/dt/valksor/php-functions-handler.svg)](https://packagist.org/packages/valksor/php-functions-handler)
[![License](https://img.shields.io/packagist/l/valksor/php-functions-handler.svg)](LICENSE)
[![PHP Version Require](https://img.shields.io/packagist/require-v/valksor/php-functions-handler/php)](https://packagist.org/packages/valksor/php-functions-handler)

A PHP library providing function handling utilities including a Chain of Responsibility pattern implementation and function execution utilities.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-handler
```

## Requirements

PHP 8.4 or higher

## Usage

The Handler package provides several utilities for handling function execution and implementing the Chain of Responsibility pattern.

### Chain Class

The Chain class implements a simple pipeline pattern for function composition:

```php
use Valksor\Functions\Handler\Chain;

// Create a new chain with an initial value
$chain = new Chain('initial value');

// Or use the static factory method
$chain = Chain::of('initial value');

// Pipe the value through a series of transformations
$result = $chain
    ->pipe(fn($value) => strtoupper($value))
    ->pipe(fn($value) => $value . ' - transformed')
    ->get();

// $result now contains "INITIAL VALUE - transformed"
```

### Handler Interface and AbstractHandler

The Handler interface and AbstractHandler class implement the Chain of Responsibility pattern:

```php
use Valksor\Functions\Handler\AbstractHandler;
use Valksor\Functions\Handler\Handler;

// Create custom handlers by extending AbstractHandler
class CustomHandler extends AbstractHandler
{
    public function handle(...$arguments): mixed
    {
        // Process the request
        $result = $this->processRequest(...$arguments);

        // If this handler can't process the request, pass it to the next handler
        if (null === $result) {
            return parent::handle(...$arguments);
        }

        return $result;
    }

    private function processRequest(...$arguments): mixed
    {
        // Custom processing logic
        // Return null if this handler can't process the request
    }
}

// Chain handlers together
$handler1 = new CustomHandler();
$handler2 = new AnotherCustomHandler();
$handler3 = new FinalHandler();

$handler1->next($handler2);
$handler2->next($handler3);

// Process a request through the chain
$result = $handler1->handle($request);
```

### FunctionHandler

The FunctionHandler executes a specified function as part of the chain:

```php
use Valksor\Functions\Handler\FunctionHandler;

// Create a handler for a global function
$handler = new FunctionHandler('strtoupper');
$result = $handler->handle('hello'); // Returns "HELLO"

// Create a handler for an object method
$object = new YourClass();
$handler = new FunctionHandler('methodName', $object);
$result = $handler->handle($arg1, $arg2); // Calls $object->methodName($arg1, $arg2)

// Chain with other handlers
$nextHandler = new AnotherHandler();
$handler->next($nextHandler);
$result = $handler->handle('hello'); // If the function returns null, passes to next handler
```


## Features

For a complete list of all classes and methods available in this package, see [Features](docs/features.md).


## Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) for details on:

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to Handler functions:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-handler-function`)
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
- **Stack Overflow**: Use tag `valksor-php-functions-handler`

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[Design Patterns](https://refactoring.guru/design-patterns/)** - Chain of Responsibility pattern inspiration
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

If you find this Handler component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
