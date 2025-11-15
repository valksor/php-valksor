# Valksor Functions: Queue - Features

This document lists all the methods available in the Valksor Functions: Queue package.

## Queue Class

The Queue class implements a FIFO (First-In-First-Out) queue data structure.

### \_\_construct()

```php
public function __construct(array $items = [])
```

Creates a new Queue instance, optionally with initial items.

Parameters:

- `$items`: An optional array of initial items to add to the queue.

Example:

```php
use Valksor\Functions\Queue\Queue;

// Create an empty queue
$emptyQueue = new Queue();

// Create a queue with initial items
$queue = new Queue([1, 2, 3]);
```

### clear()

```php
public function clear(): void
```

Removes all items from the queue.

Example:

```php
use Valksor\Functions\Queue\Queue;

$queue = new Queue([1, 2, 3]);
echo count($queue); // 3

$queue->clear();
echo count($queue); // 0
```

### contains()

```php
public function contains(mixed $item): bool
```

Checks if the queue contains a specific item.

Parameters:

- `$item`: The item to check for.

Returns a boolean indicating whether the item exists in the queue.

Example:

```php
use Valksor\Functions\Queue\Queue;

$queue = new Queue([1, 2, 3]);

$containsTwo = $queue->contains(2);
echo $containsTwo ? 'Contains 2' : 'Does not contain 2'; // Contains 2

$containsFour = $queue->contains(4);
echo $containsFour ? 'Contains 4' : 'Does not contain 4'; // Does not contain 4
```

### pop()

```php
public function pop(): mixed
```

Removes and returns the first item from the queue.

Returns the first item in the queue, or `false` if the queue is empty.

Example:

```php
use Valksor\Functions\Queue\Queue;

$queue = new Queue([1, 2, 3]);

$firstItem = $queue->pop(); // 1
echo count($queue); // 2

$secondItem = $queue->pop(); // 2
echo count($queue); // 1

// When the queue is empty, pop() returns false
$queue->clear();
$result = $queue->pop(); // false
```

### push()

```php
public function push(mixed $item): void
```

Adds an item to the end of the queue.

Parameters:

- `$item`: The item to add to the queue. If `null`, the item is not added.

Example:

```php
use Valksor\Functions\Queue\Queue;

$queue = new Queue();

$queue->push(1);
$queue->push(2);
$queue->push(3);

echo count($queue); // 3

// Null values are not added
$queue->push(null);
echo count($queue); // 3
```

### peek()

```php
public function peek(): mixed
```

Returns the first item in the queue without removing it.

Returns the first item in the queue, or `null` if the queue is empty.

Example:

```php
use Valksor\Functions\Queue\Queue;

$queue = new Queue([1, 2, 3]);

$firstItem = $queue->peek(); // 1
echo count($queue); // 3 (item not removed)

$queue->pop(); // Remove the first item
$newFirstItem = $queue->peek(); // 2
```

### isEmpty()

```php
public function isEmpty(): bool
```

Checks if the queue is empty.

Returns a boolean indicating whether the queue is empty.

Example:

```php
use Valksor\Functions\Queue\Queue;

$queue = new Queue();
echo $queue->isEmpty() ? 'Empty' : 'Not empty'; // Empty

$queue->push(1);
echo $queue->isEmpty() ? 'Empty' : 'Not empty'; // Not empty

$queue->clear();
echo $queue->isEmpty() ? 'Empty' : 'Not empty'; // Empty
```

### count()

```php
public function count(): int
```

Gets the number of items in the queue.

Returns the number of items in the queue.

Example:

```php
use Valksor\Functions\Queue\Queue;

$queue = new Queue();
echo count($queue); // 0

$queue->push(1);
$queue->push(2);
echo count($queue); // 2

$queue->pop();
echo count($queue); // 1
```

## Practical Examples

### Task Queue

```php
use Valksor\Functions\Queue\Queue;

class TaskQueue
{
    private Queue $tasks;

    public function __construct()
    {
        $this->tasks = new Queue();
    }

    public function addTask(callable $task): void
    {
        $this->tasks->push($task);
    }

    public function processTasks(int $limit = null): void
    {
        $processed = 0;

        while (!$this->tasks->isEmpty() && ($limit === null || $processed < $limit)) {
            $task = $this->tasks->pop();
            if (is_callable($task)) {
                $task();
                $processed++;
            }
        }
    }

    public function getTaskCount(): int
    {
        return count($this->tasks);
    }
}

// Usage
$taskQueue = new TaskQueue();

// Add some tasks
$taskQueue->addTask(function() {
    echo "Processing task 1\n";
});

$taskQueue->addTask(function() {
    echo "Processing task 2\n";
});

$taskQueue->addTask(function() {
    echo "Processing task 3\n";
});

echo "Tasks in queue: " . $taskQueue->getTaskCount() . "\n"; // 3

// Process all tasks
$taskQueue->processTasks();

echo "Tasks in queue: " . $taskQueue->getTaskCount() . "\n"; // 0
```

### Message Buffer

```php
use Valksor\Functions\Queue\Queue;

class MessageBuffer
{
    private Queue $messages;
    private int $capacity;

    public function __construct(int $capacity = 100)
    {
        $this->messages = new Queue();
        $this->capacity = $capacity;
    }

    public function addMessage(string $message): bool
    {
        if (count($this->messages) >= $this->capacity) {
            // Buffer is full, remove oldest message
            $this->messages->pop();
        }

        $this->messages->push($message);
        return true;
    }

    public function getNextMessage(): ?string
    {
        if ($this->messages->isEmpty()) {
            return null;
        }

        return $this->messages->pop();
    }

    public function peekNextMessage(): ?string
    {
        if ($this->messages->isEmpty()) {
            return null;
        }

        return $this->messages->peek();
    }

    public function getMessageCount(): int
    {
        return count($this->messages);
    }

    public function clear(): void
    {
        $this->messages->clear();
    }
}

// Usage
$buffer = new MessageBuffer(5); // Buffer with capacity of 5 messages

// Add messages
$buffer->addMessage("Message 1");
$buffer->addMessage("Message 2");
$buffer->addMessage("Message 3");

echo "Messages in buffer: " . $buffer->getMessageCount() . "\n"; // 3
echo "Next message: " . $buffer->peekNextMessage() . "\n"; // Message 1

// Process a message
$message = $buffer->getNextMessage();
echo "Processed: " . $message . "\n"; // Message 1
echo "Messages in buffer: " . $buffer->getMessageCount() . "\n"; // 2

// Add more messages to test capacity
$buffer->addMessage("Message 4");
$buffer->addMessage("Message 5");
$buffer->addMessage("Message 6"); // This will cause Message 2 to be removed

echo "Messages in buffer: " . $buffer->getMessageCount() . "\n"; // 5
echo "Next message: " . $buffer->peekNextMessage() . "\n"; // Message 3
```
