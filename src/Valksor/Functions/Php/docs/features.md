# Valksor Functions: PHP - Features

This document lists all the functions available in the Valksor Functions: PHP package.

## Reflection and Introspection

### classMethods()

```php
public function classMethods(
    string $class,
    ?string $parent = null,
): array
```

Gets the methods of a class.

Parameters:

- `$class`: The class name or object
- `$parent`: Optional parent class name. If provided, only methods not inherited from the parent will be returned.

Returns an array of method names.

Example:

```php
use Valksor\Functions\Php;

// Define some example classes for demonstration
class BaseClass
{
    public function baseMethod1() {}
    public function baseMethod2() {}
    protected function baseProtectedMethod() {}
    private function basePrivateMethod() {}
}

class ChildClass extends BaseClass
{
    public function childMethod1() {}
    public function childMethod2() {}
    private function childPrivateMethod() {}

    // Override a method from the parent
    public function baseMethod2() {}
}

// Basic usage - get all methods of a class
$baseMethods = Php::classMethods(BaseClass::class);
echo "Methods of BaseClass:\n";
print_r($baseMethods);
// Output:
// Methods of BaseClass:
// Array
// (
//     [0] => baseMethod1
//     [1] => baseMethod2
//     [2] => baseProtectedMethod
//     [3] => basePrivateMethod
// )

// Get all methods of a child class (including inherited methods)
$childMethods = Php::classMethods(ChildClass::class);
echo "\nAll methods of ChildClass (including inherited):\n";
print_r($childMethods);
// Output:
// All methods of ChildClass (including inherited):
// Array
// (
//     [0] => childMethod1
//     [1] => childMethod2
//     [2] => childPrivateMethod
//     [3] => baseMethod1
//     [4] => baseMethod2
//     [5] => baseProtectedMethod
// )

// Get only methods defined in the child class (not inherited from parent)
$childOnlyMethods = Php::classMethods(ChildClass::class, BaseClass::class);
echo "\nMethods defined only in ChildClass (not inherited):\n";
print_r($childOnlyMethods);
// Output:
// Methods defined only in ChildClass (not inherited):
// Array
// (
//     [0] => childMethod1
//     [1] => childMethod2
//     [2] => childPrivateMethod
//     [3] => baseMethod2  // This is included because it's overridden
// )

// Using an object instance instead of a class name
$childObj = new ChildClass();
$methodsFromObj = Php::classMethods($childObj);
echo "\nMethods from ChildClass object instance:\n";
print_r($methodsFromObj);
// Output should be the same as $childMethods

// Practical use case: Introspection for debugging
function debugClassMethods($object) {
    $className = get_class($object);
    $parentClass = get_parent_class($object);

    echo "Class: {$className}\n";

    if ($parentClass) {
        echo "Parent class: {$parentClass}\n";

        echo "Inherited methods:\n";
        $inheritedMethods = array_diff(
            Php::classMethods($className),
            Php::classMethods($className, $parentClass)
        );
        foreach ($inheritedMethods as $method) {
            echo "- {$method}\n";
        }

        echo "Own methods:\n";
        $ownMethods = Php::classMethods($className, $parentClass);
        foreach ($ownMethods as $method) {
            echo "- {$method}\n";
        }
    } else {
        echo "No parent class\n";

        echo "Methods:\n";
        $methods = Php::classMethods($className);
        foreach ($methods as $method) {
            echo "- {$method}\n";
        }
    }
}

// Using the debug function
echo "\nDebugging ChildClass:\n";
debugClassMethods(new ChildClass());
// Output:
// Debugging ChildClass:
// Class: ChildClass
// Parent class: BaseClass
// Inherited methods:
// - baseMethod1
// - baseProtectedMethod
// Own methods:
// - childMethod1
// - childMethod2
// - childPrivateMethod
// - baseMethod2

// Practical use case: Finding methods that match a pattern
function findMethodsByPattern($className, $pattern) {
    $methods = Php::classMethods($className);
    $matchingMethods = [];

    foreach ($methods as $method) {
        if (preg_match($pattern, $method)) {
            $matchingMethods[] = $method;
        }
    }

    return $matchingMethods;
}

// Find all methods that start with "base"
$baseMethodsPattern = findMethodsByPattern(ChildClass::class, '/^base/');
echo "\nMethods starting with 'base' in ChildClass:\n";
print_r($baseMethodsPattern);
// Output:
// Methods starting with 'base' in ChildClass:
// Array
// (
//     [0] => baseMethod1
//     [1] => baseMethod2
//     [2] => baseProtectedMethod
// )
```

### classConstants()

```php
public function classConstants(
    string $class,
): array
```

Gets the constants of a class.

Parameters:

- `$class`: The class name

Returns an array of constant names and values.

Example:

```php
use Valksor\Functions\Php;

// Define a class with constants for demonstration
class ColorConstants
{
    public const RED = '#FF0000';
    public const GREEN = '#00FF00';
    public const BLUE = '#0000FF';

    // PHP 8.1+ allows for private and protected constants
    private const INTERNAL_VERSION = '1.0.0';
    protected const INTERNAL_CODE = 'COLOR';

    // Class with nested arrays as constants
    public const COLOR_RGB = [
        'RED' => [255, 0, 0],
        'GREEN' => [0, 255, 0],
        'BLUE' => [0, 0, 255]
    ];
}

// Basic usage - get all constants of a class
$constants = Php::classConstants(ColorConstants::class);
echo "Constants of ColorConstants class:\n";
print_r($constants);
// Output:
// Constants of ColorConstants class:
// Array
// (
//     [RED] => #FF0000
//     [GREEN] => #00FF00
//     [BLUE] => #0000FF
//     [INTERNAL_VERSION] => 1.0.0
//     [INTERNAL_CODE] => COLOR
//     [COLOR_RGB] => Array
//         (
//             [RED] => Array
//                 (
//                     [0] => 255
//                     [1] => 0
//                     [2] => 0
//                 )
//             [GREEN] => Array
//                 (
//                     [0] => 0
//                     [1] => 255
//                     [2] => 0
//                 )
//             [BLUE] => Array
//                 (
//                     [0] => 0
//                     [1] => 0
//                     [2] => 255
//                 )
//         )
// )

// Define a class with inheritance for demonstration
class ExtendedColorConstants extends ColorConstants
{
    public const YELLOW = '#FFFF00';
    public const CYAN = '#00FFFF';
    public const MAGENTA = '#FF00FF';

    // Override a constant from the parent
    public const BLUE = '#0000CC'; // Slightly darker blue
}

// Get constants from a child class (including inherited constants)
$extendedConstants = Php::classConstants(ExtendedColorConstants::class);
echo "\nConstants of ExtendedColorConstants class (including inherited):\n";
print_r($extendedConstants);
// Output will include all constants from ColorConstants plus the new ones defined in ExtendedColorConstants
// Note that BLUE will have the overridden value '#0000CC'

// Practical use case: Using constants for configuration
class AppConfig
{
    public const DB_HOST = 'localhost';
    public const DB_PORT = 3306;
    public const DB_USER = 'app_user';
    public const DB_PASS = 'secret';
    public const DB_NAME = 'app_db';

    public const CACHE_ENABLED = true;
    public const CACHE_TTL = 3600;

    public const LOG_LEVEL = 'info';
    public const LOG_FILE = '/var/log/app.log';
}

function getDatabaseConfig() {
    $constants = Php::classConstants(AppConfig::class);
    $dbConfig = [];

    // Extract only DB-related constants
    foreach ($constants as $name => $value) {
        if (strpos($name, 'DB_') === 0) {
            // Remove the 'DB_' prefix and convert to lowercase
            $key = strtolower(substr($name, 3));
            $dbConfig[$key] = $value;
        }
    }

    return $dbConfig;
}

// Get database configuration from constants
$dbConfig = getDatabaseConfig();
echo "\nDatabase configuration from constants:\n";
print_r($dbConfig);
// Output:
// Database configuration from constants:
// Array
// (
//     [host] => localhost
//     [port] => 3306
//     [user] => app_user
//     [pass] => secret
//     [name] => app_db
// )

// Practical use case: Generating an enum-like dropdown
class StatusCodes
{
    public const PENDING = 'pending';
    public const ACTIVE = 'active';
    public const SUSPENDED = 'suspended';
    public const CANCELLED = 'cancelled';

    // Human-readable labels
    public const LABELS = [
        self::PENDING => 'Pending Approval',
        self::ACTIVE => 'Active',
        self::SUSPENDED => 'Temporarily Suspended',
        self::CANCELLED => 'Cancelled'
    ];
}

function generateStatusDropdown($selectedStatus = null) {
    $constants = Php::classConstants(StatusCodes::class);
    $labels = $constants['LABELS'] ?? [];

    // Remove the LABELS constant itself from the list of statuses
    unset($constants['LABELS']);

    $html = '<select name="status">';

    foreach ($constants as $name => $value) {
        $label = $labels[$value] ?? $value;
        $selected = ($value === $selectedStatus) ? ' selected' : '';

        $html .= '<option value="' . htmlspecialchars($value) . '"' . $selected . '>' .
                 htmlspecialchars($label) . '</option>';
    }

    $html .= '</select>';

    return $html;
}

// Generate a status dropdown with 'active' selected
$dropdown = generateStatusDropdown(StatusCodes::ACTIVE);
echo "\nGenerated status dropdown HTML:\n";
echo $dropdown . "\n";
// Output:
// <select name="status">
//   <option value="pending">Pending Approval</option>
//   <option value="active" selected>Active</option>
//   <option value="suspended">Temporarily Suspended</option>
//   <option value="cancelled">Cancelled</option>
// </select>
```

### classConstantsValues()

```php
public function classConstantsValues(
    string $class,
): array
```

Gets the values of the constants of a class.

Parameters:

- `$class`: The class name

Returns an array of constant values.

Example:

```php
use Valksor\Functions\Php;

// Define a class with constants for demonstration
class HttpStatusCodes
{
    public const OK = 200;
    public const CREATED = 201;
    public const ACCEPTED = 202;

    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;

    public const SERVER_ERROR = 500;
    public const SERVICE_UNAVAILABLE = 503;
}

// Basic usage - get all constant values of a class
$statusValues = Php::classConstantsValues(HttpStatusCodes::class);
echo "HTTP Status Code values:\n";
print_r($statusValues);
// Output:
// HTTP Status Code values:
// Array
// (
//     [0] => 200
//     [1] => 201
//     [2] => 202
//     [3] => 400
//     [4] => 401
//     [5] => 403
//     [6] => 404
//     [7] => 500
//     [8] => 503
// )

// Comparing with classConstants() to show the difference
$statusConstants = Php::classConstants(HttpStatusCodes::class);
echo "\nHTTP Status Code constants (with names):\n";
print_r($statusConstants);
// Output:
// HTTP Status Code constants (with names):
// Array
// (
//     [OK] => 200
//     [CREATED] => 201
//     [ACCEPTED] => 202
//     [BAD_REQUEST] => 400
//     [UNAUTHORIZED] => 401
//     [FORBIDDEN] => 403
//     [NOT_FOUND] => 404
//     [SERVER_ERROR] => 500
//     [SERVICE_UNAVAILABLE] => 503
// )

// Practical use case: Validating a value against allowed constants
function isValidHttpStatusCode($code) {
    $validCodes = Php::classConstantsValues(HttpStatusCodes::class);
    return in_array($code, $validCodes, true);
}

// Check if various values are valid HTTP status codes
$testCodes = [200, 404, 418, 500, '200', null];
foreach ($testCodes as $code) {
    $isValid = isValidHttpStatusCode($code);
    echo "Is " . var_export($code, true) . " a valid HTTP status code? " .
         ($isValid ? 'Yes' : 'No') . "\n";
}
// Output:
// Is 200 a valid HTTP status code? Yes
// Is 404 a valid HTTP status code? Yes
// Is 418 a valid HTTP status code? No
// Is 500 a valid HTTP status code? Yes
// Is '200' a valid HTTP status code? No (strict comparison)
// Is NULL a valid HTTP status code? No

// Define a class with string constants for demonstration
class UserRoles
{
    public const ADMIN = 'admin';
    public const EDITOR = 'editor';
    public const VIEWER = 'viewer';
    public const GUEST = 'guest';
}

// Practical use case: Creating a permission check function
function hasPermission($userRole, $requiredRoles) {
    // Get all valid roles
    $allRoles = Php::classConstantsValues(UserRoles::class);

    // Validate the user role
    if (!in_array($userRole, $allRoles, true)) {
        throw new InvalidArgumentException("Invalid user role: {$userRole}");
    }

    // Check if the user role is in the required roles
    return in_array($userRole, $requiredRoles, true);
}

// Define role requirements for different actions
$actionPermissions = [
    'viewDashboard' => [UserRoles::ADMIN, UserRoles::EDITOR, UserRoles::VIEWER],
    'editContent' => [UserRoles::ADMIN, UserRoles::EDITOR],
    'manageUsers' => [UserRoles::ADMIN]
];

// Check permissions for different roles
$roles = [UserRoles::ADMIN, UserRoles::EDITOR, UserRoles::VIEWER, UserRoles::GUEST];
$action = 'editContent';

echo "\nPermission check for action '{$action}':\n";
foreach ($roles as $role) {
    $canPerform = hasPermission($role, $actionPermissions[$action]);
    echo "Role '{$role}' can {$action}? " . ($canPerform ? 'Yes' : 'No') . "\n";
}
// Output:
// Permission check for action 'editContent':
// Role 'admin' can editContent? Yes
// Role 'editor' can editContent? Yes
// Role 'viewer' can editContent? No
// Role 'guest' can editContent? No

// Practical use case: Working with enum-like classes
class PaymentMethods
{
    public const CREDIT_CARD = 'credit_card';
    public const PAYPAL = 'paypal';
    public const BANK_TRANSFER = 'bank_transfer';
    public const CRYPTO = 'cryptocurrency';

    // Additional metadata as nested arrays
    public const METADATA = [
        self::CREDIT_CARD => ['fee' => 2.9, 'instant' => true],
        self::PAYPAL => ['fee' => 3.5, 'instant' => true],
        self::BANK_TRANSFER => ['fee' => 1.0, 'instant' => false],
        self::CRYPTO => ['fee' => 1.5, 'instant' => false]
    ];
}

// Get available payment methods for a dropdown
function getPaymentMethodOptions() {
    $methods = Php::classConstantsValues(PaymentMethods::class);

    // Remove the METADATA constant from the list
    $metadata = Php::classConstants(PaymentMethods::class)['METADATA'] ?? [];
    $methods = array_filter($methods, function($method) {
        return is_string($method); // Filter out the METADATA array
    });

    $options = [];
    foreach ($methods as $method) {
        $fee = $metadata[$method]['fee'] ?? 0;
        $instant = $metadata[$method]['instant'] ?? false;

        // Format the method name for display
        $name = ucwords(str_replace('_', ' ', $method));

        // Add fee information
        $feeInfo = $fee > 0 ? " (Fee: {$fee}%)" : " (No fee)";

        // Add instant information
        $instantInfo = $instant ? " - Instant" : " - Processing time: 1-3 days";

        $options[$method] = $name . $feeInfo . $instantInfo;
    }

    return $options;
}

// Get formatted payment method options
$paymentOptions = getPaymentMethodOptions();
echo "\nAvailable payment methods:\n";
foreach ($paymentOptions as $value => $label) {
    echo "- [{$value}] {$label}\n";
}
// Output:
// Available payment methods:
// - [credit_card] Credit Card (Fee: 2.9%) - Instant
// - [paypal] Paypal (Fee: 3.5%) - Instant
// - [bank_transfer] Bank Transfer (Fee: 1%) - Processing time: 1-3 days
// - [cryptocurrency] Cryptocurrency (Fee: 1.5%) - Processing time: 1-3 days
```

### classImplements()

```php
public function classImplements(
    string $class,
    string $interface,
): bool
```

Checks if a class implements an interface.

Parameters:

- `$class`: The class name
- `$interface`: The interface name

Returns a boolean indicating whether the class implements the interface.

Example:

```php
use Valksor\Functions\Php;

// Define some interfaces for demonstration
interface LoggerInterface
{
    public function log(string $message): void;
}

interface FormatterInterface
{
    public function format(mixed $data): string;
}

interface CacheInterface
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value): void;
}

// Define classes that implement these interfaces
class SimpleLogger implements LoggerInterface
{
    public function log(string $message): void
    {
        echo "LOG: {$message}\n";
    }
}

class JsonFormatter implements FormatterInterface
{
    public function format(mixed $data): string
    {
        return json_encode($data);
    }
}

class FileCache implements CacheInterface
{
    private array $cache = [];

    public function get(string $key): mixed
    {
        return $this->cache[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->cache[$key] = $value;
    }
}

// Class implementing multiple interfaces
class AdvancedLogger implements LoggerInterface, FormatterInterface
{
    public function log(string $message): void
    {
        echo "ADVANCED LOG: {$message}\n";
    }

    public function format(mixed $data): string
    {
        return is_array($data) || is_object($data)
            ? json_encode($data)
            : (string)$data;
    }
}

// Basic usage - check if a class implements an interface
$simpleLoggerImplementsLogger = Php::classImplements(SimpleLogger::class, LoggerInterface::class);
echo "SimpleLogger implements LoggerInterface: " . ($simpleLoggerImplementsLogger ? 'Yes' : 'No') . "\n";
// Output: SimpleLogger implements LoggerInterface: Yes

// Check if a class implements an interface it doesn't implement
$simpleLoggerImplementsFormatter = Php::classImplements(SimpleLogger::class, FormatterInterface::class);
echo "SimpleLogger implements FormatterInterface: " . ($simpleLoggerImplementsFormatter ? 'Yes' : 'No') . "\n";
// Output: SimpleLogger implements FormatterInterface: No

// Check if a class implements multiple interfaces
$advancedLoggerImplementsLogger = Php::classImplements(AdvancedLogger::class, LoggerInterface::class);
$advancedLoggerImplementsFormatter = Php::classImplements(AdvancedLogger::class, FormatterInterface::class);

echo "AdvancedLogger implements LoggerInterface: " . ($advancedLoggerImplementsLogger ? 'Yes' : 'No') . "\n";
echo "AdvancedLogger implements FormatterInterface: " . ($advancedLoggerImplementsFormatter ? 'Yes' : 'No') . "\n";
// Output:
// AdvancedLogger implements LoggerInterface: Yes
// AdvancedLogger implements FormatterInterface: Yes

// Practical use case: Type checking in a dependency injection container
class Container
{
    private array $services = [];

    public function register(string $id, object $service): void
    {
        $this->services[$id] = $service;
    }

    public function get(string $id): ?object
    {
        return $this->services[$id] ?? null;
    }

    public function getByInterface(string $interface): array
    {
        $matchingServices = [];

        foreach ($this->services as $id => $service) {
            if (Php::classImplements(get_class($service), $interface)) {
                $matchingServices[$id] = $service;
            }
        }

        return $matchingServices;
    }
}

// Using the container
$container = new Container();
$container->register('logger', new SimpleLogger());
$container->register('formatter', new JsonFormatter());
$container->register('cache', new FileCache());
$container->register('advanced_logger', new AdvancedLogger());

// Get all services implementing LoggerInterface
$loggers = $container->getByInterface(LoggerInterface::class);
echo "\nServices implementing LoggerInterface:\n";
foreach ($loggers as $id => $logger) {
    echo "- {$id} (" . get_class($logger) . ")\n";
}
// Output:
// Services implementing LoggerInterface:
// - logger (SimpleLogger)
// - advanced_logger (AdvancedLogger)

// Practical use case: Creating a factory that supports multiple implementations
function createLogger(string $type): LoggerInterface
{
    $loggerClasses = [
        'simple' => SimpleLogger::class,
        'advanced' => AdvancedLogger::class,
        // Add more logger types here
    ];

    if (!isset($loggerClasses[$type])) {
        throw new InvalidArgumentException("Unknown logger type: {$type}");
    }

    $loggerClass = $loggerClasses[$type];

    // Verify that the class implements the required interface
    if (!Php::classImplements($loggerClass, LoggerInterface::class)) {
        throw new RuntimeException("Class {$loggerClass} does not implement LoggerInterface");
    }

    return new $loggerClass();
}

// Create loggers using the factory
try {
    $simpleLogger = createLogger('simple');
    $advancedLogger = createLogger('advanced');

    echo "\nCreated loggers:\n";
    $simpleLogger->log("This is a simple log message");
    $advancedLogger->log("This is an advanced log message");

    // Try to create an invalid logger
    // $invalidLogger = createLogger('invalid'); // This would throw an exception
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Practical use case: Plugin system with interface requirements
class PluginManager
{
    private array $plugins = [];

    public function registerPlugin(string $name, string $className): bool
    {
        // Check if the class exists
        if (!class_exists($className)) {
            echo "Plugin class {$className} does not exist.\n";
            return false;
        }

        // Check if the class implements the required interface
        if (!Php::classImplements($className, 'PluginInterface')) {
            echo "Plugin class {$className} does not implement PluginInterface.\n";
            return false;
        }

        // Register the plugin
        $this->plugins[$name] = $className;
        echo "Plugin {$name} registered successfully.\n";
        return true;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }
}

// Define a plugin interface and some plugin classes
interface PluginInterface
{
    public function execute(): void;
}

class ValidPlugin implements PluginInterface
{
    public function execute(): void
    {
        echo "Valid plugin executed.\n";
    }
}

class InvalidPlugin
{
    public function run(): void
    {
        echo "Invalid plugin run.\n";
    }
}

// Using the plugin manager
$pluginManager = new PluginManager();
$pluginManager->registerPlugin('valid', ValidPlugin::class);
$pluginManager->registerPlugin('invalid', InvalidPlugin::class);

echo "\nRegistered plugins: " . implode(', ', array_keys($pluginManager->getPlugins())) . "\n";
// Output:
// Plugin valid registered successfully.
// Plugin class InvalidPlugin does not implement PluginInterface.
// Registered plugins: valid
```

### attributeExists()

```php
public function attributeExists(
    string|object $class,
    string $attribute,
    ?string $method = null,
): bool
```

Checks if an attribute exists on a class or method.

Parameters:

- `$class`: The class name or object
- `$attribute`: The attribute class name
- `$method`: Optional method name. If provided, checks if the attribute exists on the method.

Returns a boolean indicating whether the attribute exists.

Example:

```php
use Valksor\Functions\Php;

// Define some attributes for demonstration
#[Attribute(Attribute::TARGET_CLASS)]
class Entity
{
    public function __construct(
        public string $table = '',
    ) {}
}

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public string $path,
        public string $name = '',
        public array $methods = ['GET'],
    ) {}
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public string $name = '',
        public string $type = 'string',
        public bool $nullable = false,
    ) {}
}

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PARAMETER)]
class Validate
{
    public function __construct(
        public string $rule,
        public string $message = '',
    ) {}
}

// Define a class with attributes for demonstration
#[Entity(table: 'users')]
#[Route(path: '/users')]
class User
{
    #[Column(name: 'id', type: 'integer')]
    private int $id;

    #[Column(name: 'name', type: 'string')]
    private string $name;

    #[Column(name: 'email', type: 'string')]
    private string $email;

    #[Route(path: '/users/{id}', methods: ['GET'])]
    #[Validate(rule: 'numeric', message: 'User ID must be numeric')]
    public function show(int $id): array
    {
        return ['id' => $id, 'name' => 'John Doe', 'email' => 'john@example.com'];
    }

    #[Route(path: '/users', methods: ['POST'])]
    public function store(
        #[Validate(rule: 'required|string|max:255')] string $name,
        #[Validate(rule: 'required|email')] string $email
    ): array {
        return ['id' => 1, 'name' => $name, 'email' => $email];
    }
}

// Basic usage - check if an attribute exists on a class
$hasEntityAttribute = Php::attributeExists(User::class, Entity::class);
echo "User class has Entity attribute: " . ($hasEntityAttribute ? 'Yes' : 'No') . "\n";
// Output: User class has Entity attribute: Yes

$hasRouteAttribute = Php::attributeExists(User::class, Route::class);
echo "User class has Route attribute: " . ($hasRouteAttribute ? 'Yes' : 'No') . "\n";
// Output: User class has Route attribute: Yes

$hasColumnAttribute = Php::attributeExists(User::class, Column::class);
echo "User class has Column attribute: " . ($hasColumnAttribute ? 'Yes' : 'No') . "\n";
// Output: User class has Column attribute: No (Column is only on properties)

// Check if an attribute exists on a method
$hasRouteAttributeOnShow = Php::attributeExists(User::class, Route::class, 'show');
echo "User::show method has Route attribute: " . ($hasRouteAttributeOnShow ? 'Yes' : 'No') . "\n";
// Output: User::show method has Route attribute: Yes

$hasValidateAttributeOnShow = Php::attributeExists(User::class, Validate::class, 'show');
echo "User::show method has Validate attribute: " . ($hasValidateAttributeOnShow ? 'Yes' : 'No') . "\n";
// Output: User::show method has Validate attribute: Yes

$hasEntityAttributeOnShow = Php::attributeExists(User::class, Entity::class, 'show');
echo "User::show method has Entity attribute: " . ($hasEntityAttributeOnShow ? 'Yes' : 'No') . "\n";
// Output: User::show method has Entity attribute: No

// Using an object instance instead of a class name
$user = new User();
$hasEntityAttributeOnObject = Php::attributeExists($user, Entity::class);
echo "User object has Entity attribute: " . ($hasEntityAttributeOnObject ? 'Yes' : 'No') . "\n";
// Output: User object has Entity attribute: Yes

// Practical use case: Attribute-based routing
class Router
{
    private array $routes = [];

    public function registerControllers(array $controllers): void
    {
        foreach ($controllers as $controller) {
            // Check if the controller class has a Route attribute
            if (Php::attributeExists($controller, Route::class)) {
                $this->registerController($controller);
            }
        }
    }

    private function registerController(string $controller): void
    {
        // Get reflection class
        $reflectionClass = new ReflectionClass($controller);

        // Get controller-level route attribute
        $controllerAttributes = $reflectionClass->getAttributes(Route::class);
        $controllerRoutePrefix = '';

        if (!empty($controllerAttributes)) {
            $controllerRoute = $controllerAttributes[0]->newInstance();
            $controllerRoutePrefix = $controllerRoute->path;
        }

        // Get all public methods
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            // Skip methods that don't have a Route attribute
            if (!Php::attributeExists($controller, Route::class, $method->getName())) {
                continue;
            }

            // Get method-level route attributes
            $methodAttributes = $method->getAttributes(Route::class);

            if (!empty($methodAttributes)) {
                $methodRoute = $methodAttributes[0]->newInstance();
                $fullPath = rtrim($controllerRoutePrefix, '/') . '/' . ltrim($methodRoute->path, '/');
                $routeName = $methodRoute->name ?: $controller . '::' . $method->getName();

                $this->routes[] = [
                    'path' => $fullPath,
                    'name' => $routeName,
                    'methods' => $methodRoute->methods,
                    'controller' => $controller,
                    'action' => $method->getName()
                ];

                echo "Registered route: {$routeName} [{$fullPath}] " .
                     implode(',', $methodRoute->methods) . "\n";
            }
        }
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}

// Using the router
$router = new Router();
$router->registerControllers([User::class]);
// Output:
// Registered route: User::show [/users/users/{id}] GET
// Registered route: User::store [/users/users] POST

// Practical use case: Attribute-based validation
class Validator
{
    public function validate(object $controller, string $method, array $parameters): array
    {
        $errors = [];

        // Check if the method has validation attributes
        if (!Php::attributeExists($controller, Validate::class, $method)) {
            return $errors;
        }

        // Get reflection method
        $reflectionMethod = new ReflectionMethod($controller, $method);

        // Check method-level validation
        $methodAttributes = $reflectionMethod->getAttributes(Validate::class);
        if (!empty($methodAttributes)) {
            $validateAttr = $methodAttributes[0]->newInstance();
            // In a real application, you would validate based on the rule
            echo "Validating method with rule: {$validateAttr->rule}\n";
        }

        // Check parameter-level validation
        $reflectionParams = $reflectionMethod->getParameters();
        foreach ($reflectionParams as $index => $param) {
            $paramAttributes = $param->getAttributes(Validate::class);
            if (!empty($paramAttributes)) {
                $validateAttr = $paramAttributes[0]->newInstance();
                $paramName = $param->getName();
                $paramValue = $parameters[$index] ?? null;

                // In a real application, you would validate based on the rule
                echo "Validating parameter '{$paramName}' with rule: {$validateAttr->rule}\n";

                // Simulate validation error
                if ($paramName === 'email' && !filter_var($paramValue, FILTER_VALIDATE_EMAIL)) {
                    $errors[$paramName] = $validateAttr->message ?: "The {$paramName} must be a valid email address.";
                }
            }
        }

        return $errors;
    }
}

// Using the validator
$validator = new Validator();
$user = new User();

// Validate the show method
$showErrors = $validator->validate($user, 'show', [123]);
echo "Show method validation errors: " . (empty($showErrors) ? "None" : json_encode($showErrors)) . "\n";
// Output:
// Validating method with rule: numeric
// Show method validation errors: None

// Validate the store method with invalid email
$storeErrors = $validator->validate($user, 'store', ['John Doe', 'not-an-email']);
echo "Store method validation errors: " . (empty($storeErrors) ? "None" : json_encode($storeErrors)) . "\n";
// Output:
// Validating parameter 'name' with rule: required|string|max:255
// Validating parameter 'email' with rule: required|email
// Store method validation errors: {"email":"The email must be a valid email address."}
```

## System Information

### systemInfo()

```php
public function systemInfo(): array
```

Gets information about the system's operating system and architecture.

Returns an array with the following keys:

- `os`: Normalized OS name ('windows', 'darwin', or 'linux')
- `arch`: Normalized architecture ('amd64', 'arm64', or '386')
- `extension`: File extension for executables (empty string or '.exe' on Windows)

Example:

```php
use Valksor\Functions\Php;

// Basic usage - get system information
$sysInfo = Php::systemInfo();
echo "System Information:\n";
print_r($sysInfo);
// Output (example for macOS on Apple Silicon):
// System Information:
// Array
// (
//     [os] => darwin
//     [arch] => arm64
//     [extension] =>
// )

// Accessing individual components
$os = $sysInfo['os'];
$arch = $sysInfo['arch'];
$extension = $sysInfo['extension'];

echo "Operating System: {$os}\n";
echo "Architecture: {$arch}\n";
echo "Executable Extension: " . ($extension ?: '(none)') . "\n";
// Output (example for macOS on Apple Silicon):
// Operating System: darwin
// Architecture: arm64
// Executable Extension: (none)

// Practical use case: Determining the correct binary to use
function getBinaryPath(string $binaryName): string
{
    $sysInfo = Php::systemInfo();
    $basePath = '/usr/local/bin';

    // Construct binary name with OS-specific extension
    $binaryFileName = $binaryName . $sysInfo['extension'];

    // For some tools, there might be OS or architecture specific versions
    $osSpecificPath = "{$basePath}/{$binaryName}-{$sysInfo['os']}-{$sysInfo['arch']}";
    if (file_exists($osSpecificPath)) {
        return $osSpecificPath;
    }

    // Fall back to the standard binary
    return "{$basePath}/{$binaryFileName}";
}

// Example usage
$ffmpegPath = getBinaryPath('ffmpeg');
echo "FFmpeg binary path: {$ffmpegPath}\n";
// Output (example):
// FFmpeg binary path: /usr/local/bin/ffmpeg

// Practical use case: Displaying system information in a dashboard
function getSystemSummary(): array
{
    $sysInfo = Php::systemInfo();
    $phpVersion = PHP_VERSION;
    $osName = match($sysInfo['os']) {
        'darwin' => 'macOS',
        'windows' => 'Windows',
        'linux' => 'Linux',
        default => 'Unknown OS'
    };

    $archName = match($sysInfo['arch']) {
        'amd64' => 'x86_64',
        'arm64' => 'ARM64',
        '386' => 'x86',
        default => 'Unknown Architecture'
    };

    return [
        'php_version' => $phpVersion,
        'os_name' => $osName,
        'architecture' => $archName,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time') . ' seconds',
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
    ];
}

// Display system summary
$systemSummary = getSystemSummary();
echo "\nSystem Summary:\n";
foreach ($systemSummary as $key => $value) {
    echo ucwords(str_replace('_', ' ', $key)) . ": {$value}\n";
}
// Output (example):
// System Summary:
// Php Version: 8.1.0
// Os Name: macOS
// Architecture: ARM64
// Server Software: Apache/2.4.54
// Memory Limit: 128M
// Max Execution Time: 30 seconds
// Upload Max Filesize: 2M
// Post Max Size: 8M

// Practical use case: Choosing the right download URL for a tool
function getDownloadUrl(string $toolName, string $version): string
{
    $sysInfo = Php::systemInfo();
    $baseUrl = "https://example.com/downloads/{$toolName}/{$version}";

    // Map our normalized OS and arch to the format used by the download server
    $osMap = [
        'windows' => 'win',
        'darwin' => 'mac',
        'linux' => 'linux'
    ];

    $archMap = [
        'amd64' => 'x64',
        'arm64' => 'arm64',
        '386' => 'x86'
    ];

    $os = $osMap[$sysInfo['os']] ?? 'unknown';
    $arch = $archMap[$sysInfo['arch']] ?? 'unknown';

    // Construct the filename with the appropriate extension
    $extension = ($sysInfo['os'] === 'windows') ? '.zip' : '.tar.gz';
    $filename = "{$toolName}-{$version}-{$os}-{$arch}{$extension}";

    return "{$baseUrl}/{$filename}";
}

// Get download URLs for different tools
$dockerUrl = getDownloadUrl('docker', '20.10.12');
$nodeUrl = getDownloadUrl('node', '16.13.1');

echo "\nDownload URLs:\n";
echo "Docker: {$dockerUrl}\n";
echo "Node.js: {$nodeUrl}\n";
// Output (example for macOS on ARM64):
// Download URLs:
// Docker: https://example.com/downloads/docker/20.10.12/docker-20.10.12-mac-arm64.tar.gz
// Node.js: https://example.com/downloads/node/16.13.1/node-16.13.1-mac-arm64.tar.gz
```

## Type Conversion

### array()

```php
public function array(
    array|object $input,
): array
```

Converts an input to an array.

Parameters:

- `$input`: The input array or object

Returns an array. If the input is already an array, it is returned unchanged. If the input is an object, it is converted to an array.

Example:

```php
use Valksor\Functions\Php;

// Define a simple class for demonstration
class Person
{
    public string $name;
    public int $age;
    protected string $email;
    private string $password;

    public function __construct(string $name, int $age, string $email, string $password)
    {
        $this->name = $name;
        $this->age = $age;
        $this->email = $email;
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}

// Create an object
$person = new Person('John Doe', 30, 'john@example.com', 'secret123');

// Basic usage - convert an object to an array
$personArray = Php::array($person);
echo "Person as array:\n";
print_r($personArray);
// Output:
// Person as array:
// Array
// (
//     [name] => John Doe
//     [age] => 30
//     [email] => john@example.com
//     [password] => secret123
// )

// Note that protected and private properties are also included in the array
// This is different from casting an object to an array directly, which only includes public properties
$castedArray = (array)$person;
echo "\nPerson cast to array directly:\n";
print_r($castedArray);
// Output:
// Person cast to array directly:
// Array
// (
//     [name] => John Doe
//     [age] => 30
//     [*email] => john@example.com  // Note the different key name for protected property
//     [Person*password] => secret123  // Note the different key name for private property
// )

// If the input is already an array, it is returned unchanged
$originalArray = ['name' => 'Jane Doe', 'age' => 25];
$resultArray = Php::array($originalArray);
echo "\nOriginal array unchanged:\n";
print_r($resultArray);
// Output:
// Original array unchanged:
// Array
// (
//     [name] => Jane Doe
//     [age] => 25
// )

// Handling nested objects
class Department
{
    public string $name;
    public Person $manager;
    public array $employees;

    public function __construct(string $name, Person $manager, array $employees)
    {
        $this->name = $name;
        $this->manager = $manager;
        $this->employees = $employees;
    }
}

// Create a department with a manager and employees
$manager = new Person('Jane Smith', 35, 'jane@example.com', 'manager123');
$employees = [
    new Person('Bob Johnson', 28, 'bob@example.com', 'pass123'),
    new Person('Alice Brown', 32, 'alice@example.com', 'pass456')
];
$department = new Department('Engineering', $manager, $employees);

// Convert the nested object structure to an array
$departmentArray = Php::array($department);
echo "\nDepartment with nested objects as array:\n";
print_r($departmentArray);
// Output:
// Department with nested objects as array:
// Array
// (
//     [name] => Engineering
//     [manager] => Array
//         (
//             [name] => Jane Smith
//             [age] => 35
//             [email] => jane@example.com
//             [password] => manager123
//         )
//     [employees] => Array
//         (
//             [0] => Array
//                 (
//                     [name] => Bob Johnson
//                     [age] => 28
//                     [email] => bob@example.com
//                     [password] => pass123
//                 )
//             [1] => Array
//                 (
//                     [name] => Alice Brown
//                     [age] => 32
//                     [email] => alice@example.com
//                     [password] => pass456
//                 )
//         )
// )

// Practical use case: Serializing objects for storage or API responses
class ApiResponse
{
    public static function toJson(object $data): string
    {
        // Convert the object to an array first
        $array = Php::array($data);

        // Remove sensitive data
        self::removeSensitiveData($array);

        // Convert to JSON
        return json_encode($array, JSON_PRETTY_PRINT);
    }

    private static function removeSensitiveData(array &$data): void
    {
        // Remove password fields
        foreach ($data as $key => &$value) {
            if ($key === 'password') {
                unset($data[$key]);
            } elseif (is_array($value)) {
                self::removeSensitiveData($value);
            }
        }
    }
}

// Generate an API response from the department object
$jsonResponse = ApiResponse::toJson($department);
echo "\nJSON API Response (with sensitive data removed):\n";
echo $jsonResponse . "\n";
// Output:
// JSON API Response (with sensitive data removed):
// {
//     "name": "Engineering",
//     "manager": {
//         "name": "Jane Smith",
//         "age": 35,
//         "email": "jane@example.com"
//     },
//     "employees": [
//         {
//             "name": "Bob Johnson",
//             "age": 28,
//             "email": "bob@example.com"
//         },
//         {
//             "name": "Alice Brown",
//             "age": 32,
//             "email": "alice@example.com"
//         }
//     ]
// }

// Practical use case: Converting objects to arrays for data processing
class DataProcessor
{
    public static function calculateAverageAge(array $people): float
    {
        $total = 0;
        $count = count($people);

        foreach ($people as $person) {
            // Ensure we're working with an array
            $personArray = is_object($person) ? Php::array($person) : $person;
            $total += $personArray['age'] ?? 0;
        }

        return $count > 0 ? $total / $count : 0;
    }
}

// Calculate average age of employees
$employeesArray = Php::array($department->employees);
$averageAge = DataProcessor::calculateAverageAge($employeesArray);
echo "\nAverage age of employees: {$averageAge}\n";
// Output:
// Average age of employees: 30
```
