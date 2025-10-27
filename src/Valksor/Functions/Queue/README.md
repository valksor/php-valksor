# Valksor Functions: Queue

A PHP library providing a simple and efficient queue implementation for managing collections of items in a FIFO (First-In-First-Out) manner.

## Installation

Install the package via Composer:

```bash
composer require valksor/functions-queue
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

For a complete list of all methods available in this package, see [Features](docs/features.md).

## License

This package is licensed under the [BSD-3-Clause License](LICENSE).

## About Valksor

This package is part of the [valksor/valksor](https://github.com/valksor/valksor) project - a comprehensive PHP library and Symfony bundle that provides a collection of utilities, components, and integrations for Symfony applications.

The main project includes:
- Various utility functions and components
- Doctrine ORM tools and extensions
- Symfony bundle for easy configuration
- And much more

If you find this Queue component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/valksor
```
