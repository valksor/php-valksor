# Valksor Functions: Handler - Features

This document lists all the classes and methods available in the Valksor Functions: Handler package.

## Chain Class

The Chain class implements a simple pipeline pattern for function composition.

### \_\_construct()

```php
public function __construct(mixed $value)
```

Creates a new Chain instance with the given initial value.

Parameters:

- `$value`: The initial value to be stored in the chain

Example:

```php
use Valksor\Functions\Handler\Chain;

// Create a new Chain instance with an initial string value
$chain = new Chain('Hello, World!');

// Create a new Chain instance with an initial array value
$arrayChain = new Chain(['apple', 'banana', 'cherry']);

// Create a new Chain instance with an initial numeric value
$numberChain = new Chain(42);
```

### get()

```php
public function get(): mixed
```

Returns the current value stored in the chain.

Example:

```php
use Valksor\Functions\Handler\Chain;

// Create a Chain with an initial value
$chain = new Chain('Hello, World!');

// Get the current value from the chain
$value = $chain->get();
echo $value; // Output: Hello, World!

// Create a Chain with an array and get the value
$arrayChain = new Chain(['apple', 'banana', 'cherry']);
$array = $arrayChain->get();
print_r($array); // Output: Array ( [0] => apple [1] => banana [2] => cherry )
```

### pipe()

```php
public function pipe(callable $callback): self
```

Applies a transformation function to the current value and returns a new Chain instance with the result.

Parameters:

- `$callback`: A function that takes the current value and returns a new value

Example:

```php
use Valksor\Functions\Handler\Chain;

// Create a Chain with an initial string value
$chain = new Chain('hello world');

// Apply a series of transformations using pipe
$result = $chain
    ->pipe(fn($str) => ucwords($str)) // Capitalize first letter of each word
    ->pipe(fn($str) => $str . '!') // Add exclamation mark
    ->pipe(fn($str) => str_replace(' ', '-', $str)) // Replace spaces with hyphens
    ->get();

echo $result; // Output: Hello-World!

// Example with array transformation
$arrayChain = new Chain([1, 2, 3, 4, 5]);
$processedArray = $arrayChain
    ->pipe(fn($arr) => array_map(fn($num) => $num * 2, $arr)) // Multiply each element by 2
    ->pipe(fn($arr) => array_filter($arr, fn($num) => $num > 5)) // Keep only elements greater than 5
    ->pipe(fn($arr) => array_values($arr)) // Re-index array
    ->get();

print_r($processedArray); // Output: Array ( [0] => 6 [1] => 8 [2] => 10 )
```

### of()

```php
public static function of(mixed $value): self
```

Static factory method to create a new Chain instance.

Parameters:

- `$value`: The initial value to be stored in the chain

Example:

```php
use Valksor\Functions\Handler\Chain;

// Create a Chain using the static factory method
$chain = Chain::of('Hello, World!');
echo $chain->get(); // Output: Hello, World!

// Chain transformations directly after creation
$result = Chain::of([1, 2, 3, 4, 5])
    ->pipe(fn($arr) => array_map(fn($num) => $num * $num, $arr)) // Square each number
    ->pipe(fn($arr) => array_sum($arr)) // Sum all squared numbers
    ->get();

echo $result; // Output: 55 (1² + 2² + 3² + 4² + 5² = 1 + 4 + 9 + 16 + 25 = 55)

// Process a string in a single chain
$processed = Chain::of('  hello world  ')
    ->pipe('trim') // Using a named function
    ->pipe('ucwords')
    ->pipe(fn($str) => "Greeting: {$str}")
    ->get();

echo $processed; // Output: Greeting: Hello World
```

## Handler Interface

The Handler interface defines the contract for implementing the Chain of Responsibility pattern.

### handle()

```php
public function handle(...$arguments): mixed
```

Processes a request with variable arguments.

Parameters:

- `$arguments`: Variable arguments to be processed

Example:

```php
use Valksor\Functions\Handler\Handler;

// Example implementation of the Handler interface
class LogHandler implements Handler
{
    private ?Handler $nextHandler = null;

    public function handle(...$arguments): mixed
    {
        // Process the request
        $message = $arguments[0] ?? 'No message provided';
        echo "LogHandler: Logging message: {$message}\n";

        // Pass to the next handler if it exists
        if ($this->nextHandler) {
            return $this->nextHandler->handle(...$arguments);
        }

        return null;
    }

    public function next(Handler $handler): Handler
    {
        $this->nextHandler = $handler;
        return $handler;
    }
}

// Example implementation of another handler
class EmailHandler implements Handler
{
    private ?Handler $nextHandler = null;

    public function handle(...$arguments): mixed
    {
        // Process the request
        $message = $arguments[0] ?? 'No message provided';
        echo "EmailHandler: Sending email with message: {$message}\n";

        // Pass to the next handler if it exists
        if ($this->nextHandler) {
            return $this->nextHandler->handle(...$arguments);
        }

        return null;
    }

    public function next(Handler $handler): Handler
    {
        $this->nextHandler = $handler;
        return $handler;
    }
}

// Usage
$logHandler = new LogHandler();
$emailHandler = new EmailHandler();

// Chain the handlers
$logHandler->next($emailHandler);

// Process a request through the chain
$logHandler->handle("Important notification");
// Output:
// LogHandler: Logging message: Important notification
// EmailHandler: Sending email with message: Important notification
```

### next()

```php
public function next(self $handler): self
```

Sets the next handler in the chain.

Parameters:

- `$handler`: The next handler in the chain

Example:

```php
use Valksor\Functions\Handler\Handler;

// Example of creating a chain of responsibility with multiple handlers
class ValidationHandler implements Handler
{
    private ?Handler $nextHandler = null;

    public function handle(...$arguments): mixed
    {
        $data = $arguments[0] ?? [];

        // Validate the data
        if (empty($data)) {
            echo "ValidationHandler: Data is empty, stopping chain.\n";
            return false;
        }

        echo "ValidationHandler: Data is valid, continuing chain.\n";

        // Pass to next handler if it exists
        if ($this->nextHandler) {
            return $this->nextHandler->handle(...$arguments);
        }

        return true;
    }

    public function next(Handler $handler): Handler
    {
        $this->nextHandler = $handler;
        return $handler; // Return the handler to allow chaining
    }
}

class ProcessingHandler implements Handler
{
    private ?Handler $nextHandler = null;

    public function handle(...$arguments): mixed
    {
        echo "ProcessingHandler: Processing data.\n";

        // Pass to next handler if it exists
        if ($this->nextHandler) {
            return $this->nextHandler->handle(...$arguments);
        }

        return true;
    }

    public function next(Handler $handler): Handler
    {
        $this->nextHandler = $handler;
        return $handler;
    }
}

class NotificationHandler implements Handler
{
    private ?Handler $nextHandler = null;

    public function handle(...$arguments): mixed
    {
        echo "NotificationHandler: Sending notification.\n";

        // Pass to next handler if it exists
        if ($this->nextHandler) {
            return $this->nextHandler->handle(...$arguments);
        }

        return true;
    }

    public function next(Handler $handler): Handler
    {
        $this->nextHandler = $handler;
        return $handler;
    }
}

// Create handlers
$validationHandler = new ValidationHandler();
$processingHandler = new ProcessingHandler();
$notificationHandler = new NotificationHandler();

// Chain handlers using next() method
$validationHandler
    ->next($processingHandler)
    ->next($notificationHandler);

// Process valid data through the chain
$validationHandler->handle(['user' => 'john']);
// Output:
// ValidationHandler: Data is valid, continuing chain.
// ProcessingHandler: Processing data.
// NotificationHandler: Sending notification.

// Process invalid data
$validationHandler->handle([]);
// Output:
// ValidationHandler: Data is empty, stopping chain.
```

## AbstractHandler Class

The AbstractHandler class provides a base implementation of the Handler interface.

### handle()

```php
public function handle(...$arguments): mixed
```

Processes a request or passes it to the next handler in the chain.

Parameters:

- `$arguments`: Variable arguments to be processed

Example:

```php
use Valksor\Functions\Handler\AbstractHandler;
use Valksor\Functions\Handler\Handler;

// Create concrete handlers by extending AbstractHandler
class AuthenticationHandler extends AbstractHandler
{
    protected function processing(...$arguments): mixed
    {
        $user = $arguments[0] ?? null;
        $password = $arguments[1] ?? null;

        if (!$user || !$password) {
            echo "AuthenticationHandler: Missing credentials.\n";
            return false;
        }

        // Simulate authentication
        if ($user === 'admin' && $password === 'password') {
            echo "AuthenticationHandler: User authenticated successfully.\n";
            return true; // Continue to next handler
        }

        echo "AuthenticationHandler: Invalid credentials.\n";
        return false; // Stop the chain
    }
}

class AuthorizationHandler extends AbstractHandler
{
    protected function processing(...$arguments): mixed
    {
        $user = $arguments[0] ?? null;
        $resource = $arguments[2] ?? null;

        if (!$resource) {
            echo "AuthorizationHandler: No resource specified.\n";
            return false;
        }

        // Simulate authorization check
        if ($user === 'admin') {
            echo "AuthorizationHandler: User authorized to access {$resource}.\n";
            return true;
        }

        echo "AuthorizationHandler: User not authorized to access {$resource}.\n";
        return false;
    }
}

class LoggingHandler extends AbstractHandler
{
    protected function processing(...$arguments): mixed
    {
        $user = $arguments[0] ?? 'unknown';
        $resource = $arguments[2] ?? 'unknown';

        echo "LoggingHandler: Logging access attempt by {$user} to {$resource}.\n";
        return true;
    }
}

// Create and chain handlers
$authHandler = new AuthenticationHandler();
$authzHandler = new AuthorizationHandler();
$logHandler = new LoggingHandler();

$authHandler->next($authzHandler)->next($logHandler);

// Successful authentication and authorization
$authHandler->handle('admin', 'password', 'sensitive-data');
// Output:
// AuthenticationHandler: User authenticated successfully.
// AuthorizationHandler: User authorized to access sensitive-data.
// LoggingHandler: Logging access attempt by admin to sensitive-data.

// Failed authentication
$authHandler->handle('user', 'wrong-password', 'sensitive-data');
// Output:
// AuthenticationHandler: Invalid credentials.
// LoggingHandler: Logging access attempt by user to sensitive-data.
```

### next()

```php
public function next(Handler $handler): Handler
```

Sets the next handler in the chain.

Parameters:

- `$handler`: The next handler in the chain

Example:

```php
use Valksor\Functions\Handler\AbstractHandler;

// Create concrete handlers by extending AbstractHandler
class RequestValidationHandler extends AbstractHandler
{
    protected function processing(...$arguments): mixed
    {
        $request = $arguments[0] ?? null;

        echo "RequestValidationHandler: Validating request format.\n";

        // Continue to next handler
        return true;
    }
}

class DataSanitizationHandler extends AbstractHandler
{
    protected function processing(...$arguments): mixed
    {
        echo "DataSanitizationHandler: Sanitizing input data.\n";

        // Continue to next handler
        return true;
    }
}

class BusinessLogicHandler extends AbstractHandler
{
    protected function processing(...$arguments): mixed
    {
        echo "BusinessLogicHandler: Applying business rules.\n";

        // Continue to next handler
        return true;
    }
}

class ResponseFormattingHandler extends AbstractHandler
{
    protected function processing(...$arguments): mixed
    {
        echo "ResponseFormattingHandler: Formatting response.\n";

        // End of chain
        return true;
    }
}

// Create handlers
$validationHandler = new RequestValidationHandler();
$sanitizationHandler = new DataSanitizationHandler();
$businessLogicHandler = new BusinessLogicHandler();
$responseHandler = new ResponseFormattingHandler();

// Method 1: Chain handlers one by one
$validationHandler->next($sanitizationHandler);
$sanitizationHandler->next($businessLogicHandler);
$businessLogicHandler->next($responseHandler);

// Process request through the chain
$validationHandler->handle(['data' => 'example']);
// Output:
// RequestValidationHandler: Validating request format.
// DataSanitizationHandler: Sanitizing input data.
// BusinessLogicHandler: Applying business rules.
// ResponseFormattingHandler: Formatting response.

// Method 2: Chain handlers using fluent interface
$newValidationHandler = new RequestValidationHandler();
$newSanitizationHandler = new DataSanitizationHandler();
$newBusinessLogicHandler = new BusinessLogicHandler();
$newResponseHandler = new ResponseFormattingHandler();

// Chain using fluent interface (method chaining)
$newValidationHandler
    ->next($newSanitizationHandler)
    ->next($newBusinessLogicHandler)
    ->next($newResponseHandler);

// Process request through the chain
$newValidationHandler->handle(['data' => 'example']);
// Output:
// RequestValidationHandler: Validating request format.
// DataSanitizationHandler: Sanitizing input data.
// BusinessLogicHandler: Applying business rules.
// ResponseFormattingHandler: Formatting response.
```

## FunctionHandler Class

The FunctionHandler executes a specified function as part of the chain.

### \_\_construct()

```php
public function __construct(string $function, ?object $instance = null)
```

Creates a new FunctionHandler instance.

Parameters:

- `$function`: The name of the function to execute
- `$instance`: Optional object instance on which to call the function

Example:

```php
use Valksor\Functions\Handler\FunctionHandler;
use Valksor\Functions\Handler\AbstractHandler;

// Example class with methods that can be used with FunctionHandler
class UserService
{
    public function validateUsername(string $username): bool
    {
        $isValid = strlen($username) >= 3;
        echo "UserService::validateUsername: Username " . ($isValid ? "is valid" : "is too short") . ".\n";
        return $isValid;
    }

    public function createUser(string $username, string $email): array
    {
        echo "UserService::createUser: Creating user {$username} with email {$email}.\n";
        return [
            'id' => rand(1000, 9999),
            'username' => $username,
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function notifyUser(array $user): void
    {
        echo "UserService::notifyUser: Sending welcome email to {$user['email']}.\n";
    }
}

// Create a service instance
$userService = new UserService();

// Create FunctionHandler instances for different methods
$validateHandler = new FunctionHandler('validateUsername', $userService);
$createUserHandler = new FunctionHandler('createUser', $userService);
$notifyHandler = new FunctionHandler('notifyUser', $userService);

// Create a regular handler to process the result
class ResultHandler extends AbstractHandler
{
    protected function processing(...$arguments): mixed
    {
        $user = $arguments[0] ?? null;

        if (is_array($user) && isset($user['id'])) {
            echo "ResultHandler: User created successfully with ID: {$user['id']}.\n";
        }

        return true;
    }
}

$resultHandler = new ResultHandler();

// Chain the handlers
$validateHandler->next($createUserHandler)->next($notifyHandler)->next($resultHandler);

// Process a request through the chain
$validateHandler->handle('john_doe', 'john@example.com');
// Output:
// UserService::validateUsername: Username is valid.
// UserService::createUser: Creating user john_doe with email john@example.com.
// UserService::notifyUser: Sending welcome email to john@example.com.
// ResultHandler: User created successfully with ID: [random ID].

// Process an invalid request
$validateHandler->handle('jo', 'invalid@example.com');
// Output:
// UserService::validateUsername: Username is too short.
```

### handle()

```php
public function handle(...$arguments): mixed
```

Executes the specified function with the given arguments.

Parameters:

- `$arguments`: Arguments to pass to the function

Example:

```php
use Valksor\Functions\Handler\FunctionHandler;

// Example class with methods to be called by FunctionHandler
class Calculator
{
    public function add(int $a, int $b): int
    {
        $result = $a + $b;
        echo "Calculator::add: {$a} + {$b} = {$result}\n";
        return $result;
    }

    public function multiply(int $a, int $b): int
    {
        $result = $a * $b;
        echo "Calculator::multiply: {$a} * {$b} = {$result}\n";
        return $result;
    }

    public function square(int $number): int
    {
        $result = $number * $number;
        echo "Calculator::square: {$number}² = {$result}\n";
        return $result;
    }
}

// Create a calculator instance
$calculator = new Calculator();

// Create FunctionHandler instances
$addHandler = new FunctionHandler('add', $calculator);
$multiplyHandler = new FunctionHandler('multiply', $calculator);
$squareHandler = new FunctionHandler('square', $calculator);

// Use handle() method directly without chaining
$sum = $addHandler->handle(5, 3);
echo "Result of addition: {$sum}\n";
// Output:
// Calculator::add: 5 + 3 = 8
// Result of addition: 8

$product = $multiplyHandler->handle(4, 7);
echo "Result of multiplication: {$product}\n";
// Output:
// Calculator::multiply: 4 * 7 = 28
// Result of multiplication: 28

// Chain handlers and pass result from one to another
$addHandler->next($squareHandler);
$result = $addHandler->handle(10, 5);
// Output:
// Calculator::add: 10 + 5 = 15
// Calculator::square: 15² = 225

echo "Final result: {$result}\n";
// Output: Final result: 225

// Using a global function with FunctionHandler
function formatCurrency(float $amount, string $currency = 'USD'): string
{
    return number_format($amount, 2) . ' ' . $currency;
}

$formatHandler = new FunctionHandler('formatCurrency');
$formattedAmount = $formatHandler->handle(1234.56);
echo "Formatted amount: {$formattedAmount}\n";
// Output: Formatted amount: 1,234.56 USD
```

## SkipErrorHandler Class

The SkipErrorHandler executes a callback while capturing PHP errors and converting them to exceptions.

### execute()

```php
public static function execute(callable $callback)
```

Executes a callback while capturing PHP errors and converting them to exceptions.

Parameters:

- `$callback`: The function to execute

Example:

```php
use Valksor\Functions\Handler\SkipErrorHandler;

// Example 1: Handling a function that might trigger a PHP error
try {
    $result = SkipErrorHandler::execute(function() {
        // This would normally trigger a PHP warning
        $value = 10 / 0;
        return $value;
    });

    echo "Result: {$result}\n";
} catch (\Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
    // Output: Caught exception: Division by zero
}

// Example 2: Handling file operations that might fail
try {
    $fileContents = SkipErrorHandler::execute(function() {
        // This would normally trigger a PHP warning if file doesn't exist
        return file_get_contents('non_existent_file.txt');
    });

    echo "File contents: {$fileContents}\n";
} catch (\Exception $e) {
    echo "File error: " . $e->getMessage() . "\n";
    // Output: File error: file_get_contents(non_existent_file.txt): Failed to open stream: No such file or directory
}

// Example 3: Using with array operations
try {
    $value = SkipErrorHandler::execute(function() {
        $array = ['a' => 1, 'b' => 2];
        // This would normally trigger a PHP notice
        return $array['non_existent_key'];
    });

    echo "Value: {$value}\n";
} catch (\Exception $e) {
    echo "Array error: " . $e->getMessage() . "\n";
    // Output: Array error: Undefined array key "non_existent_key"
}

// Example 4: Successful execution
try {
    $sum = SkipErrorHandler::execute(function() {
        return 5 + 10;
    });

    echo "Sum: {$sum}\n";
    // Output: Sum: 15
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```
