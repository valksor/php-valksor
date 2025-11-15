# Valksor Functions: Memoize - Features

This document lists all the functions available in the Valksor Functions: Memoize package.

## Caching Functions

### memoize()

```php
public function memoize(
    BackedEnum $context,
    int|string $key,
    callable $callback,
    bool $refresh = false,
    string ...$subKeys,
): mixed
```

Caches the result of a callback function based on a context and keys. If the result is already cached, it returns the cached value unless a refresh is requested.

Parameters:

- `$context`: A BackedEnum instance used as the primary context for caching
- `$key`: The main key for the cached value
- `$callback`: The function whose result will be cached
- `$refresh`: Whether to force recalculation of the value
- `$subKeys`: Optional additional keys for nested caching

Returns the result of the callback function, either freshly calculated or from the cache.

Example:

```php
use Valksor\Functions\Memoize;

// First, define a backed enum to use as a context
enum CacheContext: string
{
    case DATABASE = 'db';
    case API = 'api';
    case FILESYSTEM = 'fs';
}

// Basic usage - caching an expensive database query
function getUserData(int $userId) {
    // Simulate an expensive database query
    echo "Executing expensive database query for user {$userId}...\n";
    sleep(1); // Simulate delay

    return [
        'id' => $userId,
        'name' => 'User ' . $userId,
        'email' => 'user' . $userId . '@example.com',
        'created_at' => date('Y-m-d H:i:s')
    ];
}

// First call - will execute the callback and cache the result
$user1 = Memoize::memoize(
    CacheContext::DATABASE,
    'user_data',
    fn() => getUserData(123)
);
echo "User name: {$user1['name']}\n";
// Output:
// Executing expensive database query for user 123...
// User name: User 123

// Second call - will return the cached result without executing the callback again
$user2 = Memoize::memoize(
    CacheContext::DATABASE,
    'user_data',
    fn() => getUserData(123)
);
echo "User name (from cache): {$user2['name']}\n";
// Output:
// User name (from cache): User 123

// Using the refresh parameter to force recalculation
$refreshedUser = Memoize::memoize(
    CacheContext::DATABASE,
    'user_data',
    fn() => getUserData(123),
    true // Force refresh
);
echo "Refreshed user name: {$refreshedUser['name']}\n";
// Output:
// Executing expensive database query for user 123...
// Refreshed user name: User 123

// Using subkeys for more granular caching
function getProductDetails(int $productId, string $locale) {
    echo "Fetching product {$productId} details in {$locale}...\n";
    sleep(1); // Simulate delay

    return [
        'id' => $productId,
        'name' => 'Product ' . $productId . ' (' . $locale . ')',
        'price' => 99.99,
        'currency' => ($locale === 'en_US') ? 'USD' : 'EUR'
    ];
}

// Cache product details with different locales
$productEN = Memoize::memoize(
    CacheContext::API,
    'product',
    fn() => getProductDetails(456, 'en_US'),
    false,
    'en_US' // Subkey for English locale
);
echo "Product name (EN): {$productEN['name']}, Price: {$productEN['price']} {$productEN['currency']}\n";
// Output:
// Fetching product 456 details in en_US...
// Product name (EN): Product 456 (en_US), Price: 99.99 USD

// Different subkey will trigger a new calculation
$productFR = Memoize::memoize(
    CacheContext::API,
    'product',
    fn() => getProductDetails(456, 'fr_FR'),
    false,
    'fr_FR' // Subkey for French locale
);
echo "Product name (FR): {$productFR['name']}, Price: {$productFR['price']} {$productFR['currency']}\n";
// Output:
// Fetching product 456 details in fr_FR...
// Product name (FR): Product 456 (fr_FR), Price: 99.99 EUR

// Using the same subkey will return cached result
$productENAgain = Memoize::memoize(
    CacheContext::API,
    'product',
    fn() => getProductDetails(456, 'en_US'),
    false,
    'en_US' // Same subkey as before
);
echo "Product name (EN again): {$productENAgain['name']}\n";
// Output:
// Product name (EN again): Product 456 (en_US)

// Practical use case: Caching API responses
class ApiClient
{
    public function fetchData(string $endpoint, array $params = []) {
        // In a real application, this would make an HTTP request
        echo "Making API request to {$endpoint}...\n";
        sleep(2); // Simulate network delay

        // Simulate different responses based on the endpoint
        if ($endpoint === '/users') {
            return [
                ['id' => 1, 'name' => 'John'],
                ['id' => 2, 'name' => 'Jane'],
                ['id' => 3, 'name' => 'Bob']
            ];
        } elseif ($endpoint === '/products') {
            return [
                ['id' => 101, 'name' => 'Laptop', 'price' => 999],
                ['id' => 102, 'name' => 'Phone', 'price' => 699],
                ['id' => 103, 'name' => 'Tablet', 'price' => 499]
            ];
        }

        return ['error' => 'Unknown endpoint'];
    }

    public function getUsers() {
        // Use memoize to cache the API response
        return Memoize::memoize(
            CacheContext::API,
            'api_response',
            fn() => $this->fetchData('/users'),
            false,
            'users' // Subkey for the users endpoint
        );
    }

    public function getProducts() {
        // Use memoize to cache the API response
        return Memoize::memoize(
            CacheContext::API,
            'api_response',
            fn() => $this->fetchData('/products'),
            false,
            'products' // Subkey for the products endpoint
        );
    }

    public function refreshCache() {
        // Force refresh of all cached data
        Memoize::memoize(
            CacheContext::API,
            'api_response',
            fn() => $this->fetchData('/users'),
            true, // Force refresh
            'users'
        );

        Memoize::memoize(
            CacheContext::API,
            'api_response',
            fn() => $this->fetchData('/products'),
            true, // Force refresh
            'products'
        );

        echo "API cache refreshed.\n";
    }
}

// Using the API client with memoization
$apiClient = new ApiClient();

// First call - will make the API request
$users = $apiClient->getUsers();
echo "Found " . count($users) . " users.\n";
// Output:
// Making API request to /users...
// Found 3 users.

// Second call - will use cached data
$usersAgain = $apiClient->getUsers();
echo "Found " . count($usersAgain) . " users (from cache).\n";
// Output:
// Found 3 users (from cache).

// Different endpoint - will make a new API request
$products = $apiClient->getProducts();
echo "Found " . count($products) . " products.\n";
// Output:
// Making API request to /products...
// Found 3 products.

// Refresh the cache
$apiClient->refreshCache();
// Output:
// Making API request to /users...
// Making API request to /products...
// API cache refreshed.

// After refresh - will use the new cached data
$refreshedUsers = $apiClient->getUsers();
echo "Found " . count($refreshedUsers) . " users after refresh.\n";
// Output:
// Found 3 users after refresh.
```

### value()

```php
public function value(
    BackedEnum $context,
    int|string $key,
    mixed $default = null,
    string ...$subKeys,
): mixed
```

Retrieves a cached value based on a context and keys, with an option to provide a default value if the requested value isn't cached.

Parameters:

- `$context`: A BackedEnum instance used as the primary context for caching
- `$key`: The main key for the cached value
- `$default`: The default value to return if the requested value isn't cached
- `$subKeys`: Optional additional keys for nested caching

Returns the cached value if found, or the default value if not found.

Example:

```php
use Valksor\Functions\Memoize;

// Using the same enum context from the previous example
enum CacheContext: string
{
    case DATABASE = 'db';
    case API = 'api';
    case FILESYSTEM = 'fs';
    case CONFIG = 'config';
}

// First, let's cache some values using memoize()
Memoize::memoize(
    CacheContext::CONFIG,
    'app_settings',
    fn() => [
        'debug' => true,
        'timezone' => 'UTC',
        'max_upload_size' => 10485760 // 10MB
    ]
);

// Basic usage - retrieving a cached value
$appSettings = Memoize::value(CacheContext::CONFIG, 'app_settings');
echo "Debug mode: " . ($appSettings['debug'] ? 'enabled' : 'disabled') . "\n";
echo "Timezone: {$appSettings['timezone']}\n";
// Output:
// Debug mode: enabled
// Timezone: UTC

// Retrieving a value that doesn't exist (using default)
$nonExistentValue = Memoize::value(CacheContext::CONFIG, 'non_existent', 'Default Value');
echo "Non-existent value: {$nonExistentValue}\n";
// Output:
// Non-existent value: Default Value

// Using subkeys for nested values
// First, cache some nested data
Memoize::memoize(
    CacheContext::DATABASE,
    'connection',
    fn() => [
        'mysql' => [
            'host' => 'localhost',
            'port' => 3306,
            'username' => 'root',
            'password' => 'secret'
        ],
        'postgres' => [
            'host' => 'pg.example.com',
            'port' => 5432,
            'username' => 'postgres',
            'password' => 'pg_secret'
        ]
    ]
);

// Retrieve a specific nested value using subkeys
$mysqlConfig = Memoize::value(CacheContext::DATABASE, 'connection', null, 'mysql');
if ($mysqlConfig) {
    echo "MySQL Connection: {$mysqlConfig['username']}@{$mysqlConfig['host']}:{$mysqlConfig['port']}\n";
}
// Output:
// MySQL Connection: root@localhost:3306

// Retrieve a non-existent nested value with a default
$redisConfig = Memoize::value(CacheContext::DATABASE, 'connection', [
    'host' => '127.0.0.1',
    'port' => 6379
], 'redis');
echo "Redis Connection: {$redisConfig['host']}:{$redisConfig['port']}\n";
// Output:
// Redis Connection: 127.0.0.1:6379

// Practical use case: Configuration manager
class ConfigManager
{
    private array $defaults = [
        'app' => [
            'name' => 'My Application',
            'version' => '1.0.0',
            'debug' => false
        ],
        'database' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'name' => 'app_db',
            'user' => 'app_user',
            'password' => ''
        ],
        'mail' => [
            'driver' => 'smtp',
            'host' => 'smtp.example.com',
            'port' => 587,
            'encryption' => 'tls',
            'username' => '',
            'password' => ''
        ]
    ];

    public function __construct()
    {
        // Initialize the cache with default values
        Memoize::memoize(
            CacheContext::CONFIG,
            'settings',
            fn() => $this->defaults
        );
    }

    public function get(string $section, string $key = null, mixed $default = null)
    {
        if ($key === null) {
            // Return the entire section
            return Memoize::value(CacheContext::CONFIG, 'settings', $this->defaults[$section] ?? $default, $section);
        }

        // Get the section first
        $sectionData = Memoize::value(CacheContext::CONFIG, 'settings', $this->defaults[$section] ?? [], $section);

        // Return the specific key from the section
        return $sectionData[$key] ?? $default;
    }

    public function set(string $section, string $key, mixed $value)
    {
        // Get the current section data
        $sectionData = Memoize::value(CacheContext::CONFIG, 'settings', $this->defaults[$section] ?? [], $section);

        // Update the value
        $sectionData[$key] = $value;

        // Update the cache with the new section data
        $settings = Memoize::value(CacheContext::CONFIG, 'settings', $this->defaults);
        $settings[$section] = $sectionData;

        // Store the updated settings
        Memoize::memoize(
            CacheContext::CONFIG,
            'settings',
            fn() => $settings,
            true // Force refresh
        );

        return $value;
    }
}

// Using the configuration manager
$config = new ConfigManager();

// Get configuration values
$appName = $config->get('app', 'name');
echo "Application name: {$appName}\n";
// Output:
// Application name: My Application

$dbConfig = $config->get('database');
echo "Database connection: {$dbConfig['user']}@{$dbConfig['host']}:{$dbConfig['port']}/{$dbConfig['name']}\n";
// Output:
// Database connection: app_user@localhost:3306/app_db

// Get a value with a default
$cacheDriver = $config->get('cache', 'driver', 'file');
echo "Cache driver: {$cacheDriver}\n";
// Output:
// Cache driver: file

// Update a configuration value
$config->set('app', 'debug', true);
$debugMode = $config->get('app', 'debug');
echo "Debug mode: " . ($debugMode ? 'enabled' : 'disabled') . "\n";
// Output:
// Debug mode: enabled

// Another practical use case: User preferences
class UserPreferences
{
    public function loadPreferences(int $userId)
    {
        // In a real application, this would load from a database
        echo "Loading preferences for user {$userId} from database...\n";
        sleep(1); // Simulate database query

        return [
            'theme' => 'dark',
            'language' => 'en',
            'notifications' => true,
            'timezone' => 'America/New_York'
        ];
    }

    public function getPreference(int $userId, string $key, mixed $default = null)
    {
        // First, ensure the user's preferences are cached
        Memoize::memoize(
            CacheContext::DATABASE,
            'user_preferences',
            fn() => $this->loadPreferences($userId),
            false,
            (string)$userId
        );

        // Get the specific preference
        $preferences = Memoize::value(CacheContext::DATABASE, 'user_preferences', [], (string)$userId);
        return $preferences[$key] ?? $default;
    }

    public function getAllPreferences(int $userId)
    {
        // Ensure the user's preferences are cached
        Memoize::memoize(
            CacheContext::DATABASE,
            'user_preferences',
            fn() => $this->loadPreferences($userId),
            false,
            (string)$userId
        );

        // Return all preferences
        return Memoize::value(CacheContext::DATABASE, 'user_preferences', [], (string)$userId);
    }
}

// Using the user preferences
$preferences = new UserPreferences();

// Get a specific preference
$theme = $preferences->getPreference(789, 'theme', 'light');
echo "User theme: {$theme}\n";
// Output:
// Loading preferences for user 789 from database...
// User theme: dark

// Get another preference (should use cached data)
$language = $preferences->getPreference(789, 'language', 'en');
echo "User language: {$language}\n";
// Output:
// User language: en

// Get all preferences (should use cached data)
$allPrefs = $preferences->getAllPreferences(789);
echo "User timezone: {$allPrefs['timezone']}\n";
// Output:
// User timezone: America/New_York

// Get a preference for a different user (should load from database)
$otherTheme = $preferences->getPreference(456, 'theme', 'light');
echo "Other user theme: {$otherTheme}\n";
// Output:
// Loading preferences for user 456 from database...
// Other user theme: dark
```
