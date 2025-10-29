# Valksor Functions: Queue

A PHP library providing a simple and efficient queue implementation for managing collections of items in a FIFO (First-In-First-Out) manner.

## Installation

Install the package via Composer:

```bash
composer require valksor/php-functions-queue
```

## Requirements

PHP 8.4 or higher

## Usage

The Queue class provides a simple implementation of a FIFO (First-In-First-Out) queue data structure.

```php
use Valksor\Functions\Queue\Queue;

// Create a new Queue instance
$queue = new Queue();

// Add items to the queue
$queue->push(1);
$queue->push(2);
$queue->push(3);

// Check if the queue contains an item
$containsTwo = $queue->contains(2); // true

// Get the number of items in the queue
$count = count($queue); // 3

// Check if the queue is empty
$isEmpty = $queue->isEmpty(); // false

// Get the first item without removing it
$firstItem = $queue->peek(); // 1

// Remove and return the first item
$firstItem = $queue->pop(); // 1

// Clear all items from the queue
$queue->clear();
```

You can also initialize a queue with an array of items:

```php
// Create a queue with initial items
$queue = new Queue([1, 2, 3]);

// Get the number of items
echo count($queue); // 3
```

## Features

- **FIFO implementation**: Standard First-In-First-Out queue behavior
- **Array initialization**: Create queues from existing arrays
- **Item management**: Add, remove, and check for items
- **Queue inspection**: Peek at items without removing them
- **Count support**: Use count() to get queue length
- **Clear functionality**: Remove all items at once
- **Simple API**: Intuitive and easy-to-use interface

For a complete list of all methods available in this package, see [Features](docs/features.md).


## Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) for details on:

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to Queue functions:

1. Fork repository
2. Create a feature branch (`git checkout -b feature/new-queue-function`)
3. Implement your function following existing patterns
4. Add comprehensive tests including edge cases
5. Ensure all tests pass and code style is correct
6. Submit a pull request

## Security

If you discover any security-related issues, please email us at security@valksor.dev instead of using the issue tracker.

For security policy and vulnerability reporting guidelines, please see our [Security Policy](SECURITY.md).

## Support

- **Documentation**: [Full documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues) for bug reports and feature requests
- **Discussions**: [GitHub Discussions](https://github.com/valksor/php-valksor/discussions) for questions and community support
- **Stack Overflow**: Use tag `valksor-php-functions-queue`

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

If you find this Queue component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
