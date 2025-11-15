# Valksor Functions: Local - Features

This document lists all the functions available in the Valksor Functions: Local package.

## Class and File Existence

### exists()

```php
public function exists(
    string|object $class,
): bool
```

Checks if a PHP class, interface, or trait exists.

Parameters:

- `$class`: The class name or object to check

Returns a boolean indicating whether the class, interface, or trait exists.

Example:

```php
use Valksor\Functions\Local;

// Check if a standard PHP class exists
$dateTimeExists = Local::exists('DateTime');
var_dump($dateTimeExists); // Output: bool(true)

// Check if a non-existent class exists
$nonExistentClass = Local::exists('NonExistentClass');
var_dump($nonExistentClass); // Output: bool(false)

// Check if an interface exists
$iteratorExists = Local::exists('Iterator');
var_dump($iteratorExists); // Output: bool(true)

// Check if a trait exists
$traitExists = Local::exists('Throwable');
var_dump($traitExists); // Output: bool(true)

// Check using an object instance
$dateTime = new DateTime();
$objectExists = Local::exists($dateTime);
var_dump($objectExists); // Output: bool(true)

// Practical use case: Conditional class loading
if (!Local::exists('MyCustomClass')) {
    // Class doesn't exist, so define it
    class MyCustomClass {
        public function doSomething() {
            return 'Something done!';
        }
    }
}

// Now the class exists
$customClassExists = Local::exists('MyCustomClass');
var_dump($customClassExists); // Output: bool(true)

// Practical use case: Safe class instantiation
function safelyCreateObject($className, $fallbackClassName) {
    if (Local::exists($className)) {
        return new $className();
    } else {
        return new $fallbackClassName();
    }
}

// Using the function
$object = safelyCreateObject('NonExistentClass', 'DateTime');
echo get_class($object); // Output: DateTime
```

### fileExistsCwd()

```php
public function fileExistsCwd(
    string $filename,
): bool
```

Checks if a file exists in the current working directory.

Parameters:

- `$filename`: The name of the file to check

Returns a boolean indicating whether the file exists in the current working directory.

Example:

```php
use Valksor\Functions\Local;

// Check if a file exists in the current working directory
$composerExists = Local::fileExistsCwd('composer.json');
echo "composer.json exists in current directory: " . ($composerExists ? 'Yes' : 'No') . "\n";

// Check if a non-existent file exists
$nonExistentFile = Local::fileExistsCwd('non-existent-file.txt');
echo "non-existent-file.txt exists in current directory: " . ($nonExistentFile ? 'Yes' : 'No') . "\n";

// Practical use case: Conditional file processing
function processConfigFile($filename) {
    if (Local::fileExistsCwd($filename)) {
        echo "Processing {$filename}...\n";
        // Read and process the file
        $content = file_get_contents(getcwd() . '/' . $filename);
        return "File processed successfully. Size: " . strlen($content) . " bytes";
    } else {
        echo "File {$filename} not found in current directory.\n";
        return "Error: File not found";
    }
}

// Process an existing file
$result1 = processConfigFile('composer.json');
echo $result1 . "\n";
// Output might be:
// Processing composer.json...
// File processed successfully. Size: 1234 bytes

// Try to process a non-existent file
$result2 = processConfigFile('config.xyz');
echo $result2 . "\n";
// Output:
// File config.xyz not found in current directory.
// Error: File not found

// Practical use case: Check multiple files
$requiredFiles = ['composer.json', '.env', 'README.md'];
$missingFiles = [];

foreach ($requiredFiles as $file) {
    if (!Local::fileExistsCwd($file)) {
        $missingFiles[] = $file;
    }
}

if (empty($missingFiles)) {
    echo "All required files exist in the current directory.\n";
} else {
    echo "Missing files: " . implode(', ', $missingFiles) . "\n";
}
```

## Directory Operations

### mkdir()

```php
public function mkdir(
    string $dir,
): bool
```

Creates a directory if it doesn't exist.

Parameters:

- `$dir`: The path of the directory to create

Returns a boolean indicating whether the directory exists after the operation. Throws an exception if the directory couldn't be created.

Example:

```php
use Valksor\Functions\Local;

// Basic usage - create a directory
try {
    $dirPath = sys_get_temp_dir() . '/valksor_test';
    $success = Local::mkdir($dirPath);
    echo "Directory creation " . ($success ? "successful" : "failed") . "\n";
    // Output: Directory creation successful

    // The directory now exists
    echo "Directory exists: " . (is_dir($dirPath) ? "Yes" : "No") . "\n";
    // Output: Directory exists: Yes

    // Trying to create the same directory again also returns true
    $success = Local::mkdir($dirPath);
    echo "Second attempt: " . ($success ? "successful" : "failed") . "\n";
    // Output: Second attempt: successful
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Creating nested directories
try {
    $nestedPath = sys_get_temp_dir() . '/valksor_test/nested/subdirectory';
    $success = Local::mkdir($nestedPath);
    echo "Nested directory creation " . ($success ? "successful" : "failed") . "\n";
    // Output: Nested directory creation successful
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Practical use case: Ensuring upload directory exists
function ensureUploadDirectoryExists($userId) {
    $uploadDir = sys_get_temp_dir() . '/uploads/' . $userId;

    try {
        if (Local::mkdir($uploadDir)) {
            echo "Upload directory for user {$userId} is ready.\n";
            return $uploadDir;
        } else {
            echo "Failed to create upload directory for user {$userId}.\n";
            return false;
        }
    } catch (\Exception $e) {
        echo "Error creating upload directory: " . $e->getMessage() . "\n";
        return false;
    }
}

// Using the function
$userUploadDir = ensureUploadDirectoryExists('user123');
if ($userUploadDir) {
    echo "Files can be uploaded to: {$userUploadDir}\n";
    // Output: Files can be uploaded to: /tmp/uploads/user123
}

// Error handling example - trying to create a directory in a location without permissions
try {
    // This would typically fail due to permissions (assuming /root is protected)
    $restrictedPath = '/root/test_directory';
    $success = Local::mkdir($restrictedPath);
    echo "Restricted directory creation " . ($success ? "successful" : "failed") . "\n";
} catch (\Exception $e) {
    echo "Error with restricted directory: " . $e->getMessage() . "\n";
    // Output: Error with restricted directory: [error message about permissions]
}
```

### rmdir()

```php
public function rmdir(
    string $dir,
): bool
```

Removes a directory if it exists.

Parameters:

- `$dir`: The path of the directory to remove

Returns a boolean indicating whether the operation was successful.

Example:

```php
use Valksor\Functions\Local;

// First, create a test directory to remove
$testDir = sys_get_temp_dir() . '/valksor_rmdir_test';
if (!is_dir($testDir)) {
    mkdir($testDir);
}

// Basic usage - remove a directory
try {
    $success = Local::rmdir($testDir);
    echo "Directory removal " . ($success ? "successful" : "failed") . "\n";
    // Output: Directory removal successful

    // The directory should no longer exist
    echo "Directory exists: " . (is_dir($testDir) ? "Yes" : "No") . "\n";
    // Output: Directory exists: No
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Trying to remove a non-existent directory
try {
    $nonExistentDir = sys_get_temp_dir() . '/non_existent_directory';
    $success = Local::rmdir($nonExistentDir);
    echo "Non-existent directory removal " . ($success ? "successful" : "failed") . "\n";
    // Output: Non-existent directory removal successful (or failed, depending on implementation)
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Error handling - trying to remove a non-empty directory
try {
    // Create a directory with a file in it
    $nonEmptyDir = sys_get_temp_dir() . '/non_empty_dir';
    if (!is_dir($nonEmptyDir)) {
        mkdir($nonEmptyDir);
    }
    file_put_contents($nonEmptyDir . '/test_file.txt', 'Test content');

    // Try to remove the non-empty directory
    $success = Local::rmdir($nonEmptyDir);
    echo "Non-empty directory removal " . ($success ? "successful" : "failed") . "\n";
    // This would typically fail because the directory is not empty
} catch (\Exception $e) {
    echo "Error removing non-empty directory: " . $e->getMessage() . "\n";
    // Output: Error removing non-empty directory: [error message]

    // Clean up by removing the file first, then the directory
    unlink($nonEmptyDir . '/test_file.txt');
    Local::rmdir($nonEmptyDir);
}

// Practical use case: Cleaning up temporary directories
function cleanupTempDirectory($dirPath) {
    if (!is_dir($dirPath)) {
        echo "Directory {$dirPath} does not exist.\n";
        return true;
    }

    // Remove all files in the directory first
    $files = glob($dirPath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    // Now try to remove the directory
    try {
        $success = Local::rmdir($dirPath);
        if ($success) {
            echo "Successfully cleaned up and removed {$dirPath}.\n";
        } else {
            echo "Failed to remove {$dirPath}.\n";
        }
        return $success;
    } catch (\Exception $e) {
        echo "Error during cleanup: " . $e->getMessage() . "\n";
        return false;
    }
}

// Using the cleanup function
$tempDir = sys_get_temp_dir() . '/valksor_temp';
if (!is_dir($tempDir)) {
    mkdir($tempDir);
    file_put_contents($tempDir . '/temp_data.txt', 'Temporary data');
}
$cleanupResult = cleanupTempDirectory($tempDir);
// Output: Successfully cleaned up and removed /tmp/valksor_temp.
```

## Environment and System

### getenv()

```php
public function getenv(
    string $name,
    bool $localOnly = true,
): mixed
```

Gets the value of an environment variable.

Parameters:

- `$name`: The name of the environment variable
- `$localOnly`: Whether to get the variable from the local environment only

Returns the value of the environment variable, or the name of the variable if it doesn't exist.

Example:

```php
use Valksor\Functions\Local;

// Basic usage - get a common environment variable
$path = Local::getenv('PATH');
echo "PATH environment variable: " . (is_string($path) ? $path : 'Not found') . "\n";
// Output: PATH environment variable: /usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin (or similar)

// Get a variable that might not exist
$nonExistentVar = Local::getenv('NON_EXISTENT_VARIABLE');
echo "Result for non-existent variable: " . $nonExistentVar . "\n";
// Output: Result for non-existent variable: NON_EXISTENT_VARIABLE (returns the variable name)

// Using the localOnly parameter
// When localOnly is true (default), it only checks the local environment
$homeLocal = Local::getenv('HOME', true);
echo "HOME (local only): " . (is_string($homeLocal) ? $homeLocal : 'Not found') . "\n";

// When localOnly is false, it might also check Apache environment variables in a web context
$homeAll = Local::getenv('HOME', false);
echo "HOME (all environments): " . (is_string($homeAll) ? $homeAll : 'Not found') . "\n";

// Practical use case: Configuring an application based on environment variables
function getDatabaseConfig() {
    $config = [
        'host' => Local::getenv('DB_HOST') !== 'DB_HOST' ? Local::getenv('DB_HOST') : 'localhost',
        'port' => Local::getenv('DB_PORT') !== 'DB_PORT' ? Local::getenv('DB_PORT') : '3306',
        'name' => Local::getenv('DB_NAME') !== 'DB_NAME' ? Local::getenv('DB_NAME') : 'default_db',
        'user' => Local::getenv('DB_USER') !== 'DB_USER' ? Local::getenv('DB_USER') : 'root',
        'pass' => Local::getenv('DB_PASS') !== 'DB_PASS' ? Local::getenv('DB_PASS') : '',
    ];

    return $config;
}

// Using the function to get database configuration
$dbConfig = getDatabaseConfig();
echo "Database configuration:\n";
echo "Host: " . $dbConfig['host'] . "\n";
echo "Port: " . $dbConfig['port'] . "\n";
echo "Database: " . $dbConfig['name'] . "\n";
echo "User: " . $dbConfig['user'] . "\n";
echo "Password: " . (empty($dbConfig['pass']) ? '[empty]' : '[set]') . "\n";

// Practical use case: Determining application environment
function getAppEnvironment() {
    $env = Local::getenv('APP_ENV');
    if ($env === 'APP_ENV') {
        // Environment variable not set, default to development
        return 'development';
    }

    return $env;
}

$environment = getAppEnvironment();
echo "Application is running in {$environment} environment.\n";

// Conditionally enable features based on environment
if ($environment === 'development') {
    echo "Debug mode is enabled.\n";
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    echo "Debug mode is disabled.\n";
    error_reporting(0);
    ini_set('display_errors', '0');
}
```

### humanFileSize()

```php
public function humanFileSize(
    int $bytes,
    int $decimals = 2,
): string
```

Formats a file size in a human-readable way.

Parameters:

- `$bytes`: The size in bytes
- `$decimals`: The number of decimal places to include in the formatted size

Returns a string with the formatted file size (e.g., "1.00M").

Example:

```php
use Valksor\Functions\Local;

// Basic usage with different file sizes
$size1 = Local::humanFileSize(1024); // 1 KB
echo "1024 bytes = " . $size1 . "\n";
// Output: 1024 bytes = 1.00K

$size2 = Local::humanFileSize(1048576); // 1 MB
echo "1048576 bytes = " . $size2 . "\n";
// Output: 1048576 bytes = 1.00M

$size3 = Local::humanFileSize(1073741824); // 1 GB
echo "1073741824 bytes = " . $size3 . "\n";
// Output: 1073741824 bytes = 1.00G

$size4 = Local::humanFileSize(1099511627776); // 1 TB
echo "1099511627776 bytes = " . $size4 . "\n";
// Output: 1099511627776 bytes = 1.00T

// Using different decimal precision
$size5 = Local::humanFileSize(1500000, 0); // No decimals
echo "1500000 bytes (0 decimals) = " . $size5 . "\n";
// Output: 1500000 bytes (0 decimals) = 1M

$size6 = Local::humanFileSize(1500000, 1); // 1 decimal
echo "1500000 bytes (1 decimal) = " . $size6 . "\n";
// Output: 1500000 bytes (1 decimal) = 1.4M

$size7 = Local::humanFileSize(1500000, 3); // 3 decimals
echo "1500000 bytes (3 decimals) = " . $size7 . "\n";
// Output: 1500000 bytes (3 decimals) = 1.430M

// Practical use case: Displaying file sizes in a file listing
function getFilesList($directory) {
    $files = [];

    if (is_dir($directory)) {
        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . '/' . $item;

            if (is_file($path)) {
                $size = filesize($path);
                $humanSize = Local::humanFileSize($size);

                $files[] = [
                    'name' => $item,
                    'size_bytes' => $size,
                    'size_human' => $humanSize,
                    'modified' => date('Y-m-d H:i:s', filemtime($path))
                ];
            }
        }
    }

    return $files;
}

// Using the function to list files in a directory
$filesList = getFilesList(sys_get_temp_dir());
echo "Files in temporary directory:\n";
foreach ($filesList as $index => $file) {
    if ($index >= 5) {
        // Limit to 5 files for the example
        break;
    }
    echo "{$file['name']} - {$file['size_human']} (modified: {$file['modified']})\n";
}

// Practical use case: Showing storage usage
function getStorageUsage($directories) {
    $usage = [];
    $totalBytes = 0;

    foreach ($directories as $name => $path) {
        $bytes = 0;

        if (is_dir($path)) {
            // This is a simplified calculation and doesn't handle subdirectories
            $files = glob($path . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $bytes += filesize($file);
                }
            }
        }

        $usage[$name] = [
            'bytes' => $bytes,
            'human' => Local::humanFileSize($bytes)
        ];

        $totalBytes += $bytes;
    }

    $usage['total'] = [
        'bytes' => $totalBytes,
        'human' => Local::humanFileSize($totalBytes)
    ];

    return $usage;
}

// Using the function to show storage usage
$storageUsage = getStorageUsage([
    'temp' => sys_get_temp_dir(),
    'home' => getenv('HOME') ?: '/home/user'
]);

echo "\nStorage usage:\n";
foreach ($storageUsage as $name => $data) {
    echo ucfirst($name) . ": " . $data['human'] . "\n";
}
```

### getCurlUserAgent()

```php
public function getCurlUserAgent(): string
```

Gets the cURL user agent string.

Returns a string with the cURL user agent (e.g., "curl/7.68.0").

Example:

```php
use Valksor\Functions\Local;

// Basic usage - get the cURL user agent
$curlUserAgent = Local::getCurlUserAgent();
echo "cURL User Agent: " . $curlUserAgent . "\n";
// Output: cURL User Agent: curl/7.68.0 (or similar, depending on your cURL version)

// Practical use case: Setting the user agent for an HTTP request
function makeHttpRequest($url, $useCustomUserAgent = false) {
    // Initialize cURL session
    $ch = curl_init($url);

    // Set common cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if ($useCustomUserAgent) {
        // Use a custom user agent that includes the cURL version
        $customUserAgent = 'MyApp/1.0 (' . Local::getCurlUserAgent() . ')';
        curl_setopt($ch, CURLOPT_USERAGENT, $customUserAgent);
        echo "Using custom User Agent: {$customUserAgent}\n";
    } else {
        // Use the default cURL user agent
        echo "Using default cURL User Agent\n";
    }

    // Execute the request
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo "cURL Error: " . curl_error($ch) . "\n";
        curl_close($ch);
        return null;
    }

    curl_close($ch);

    return [
        'status_code' => $info['http_code'],
        'content_type' => $info['content_type'],
        'response_size' => strlen($response),
        'response' => substr($response, 0, 100) . (strlen($response) > 100 ? '...' : '') // Truncate for display
    ];
}

// Example usage of the function
try {
    // Make a request with the default user agent
    echo "Making request with default user agent...\n";
    $result1 = makeHttpRequest('https://httpbin.org/user-agent');

    if ($result1) {
        echo "Status Code: {$result1['status_code']}\n";
        echo "Content Type: {$result1['content_type']}\n";
        echo "Response Size: {$result1['response_size']} bytes\n";
        echo "Response: {$result1['response']}\n\n";
    }

    // Make a request with a custom user agent that includes the cURL version
    echo "Making request with custom user agent...\n";
    $result2 = makeHttpRequest('https://httpbin.org/user-agent', true);

    if ($result2) {
        echo "Status Code: {$result2['status_code']}\n";
        echo "Content Type: {$result2['content_type']}\n";
        echo "Response Size: {$result2['response_size']} bytes\n";
        echo "Response: {$result2['response']}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Practical use case: Checking if cURL is available and getting its version
function getCurlInfo() {
    if (!function_exists('curl_version')) {
        return [
            'available' => false,
            'message' => 'cURL extension is not available'
        ];
    }

    $curlVersion = curl_version();
    $userAgent = Local::getCurlUserAgent();

    return [
        'available' => true,
        'version' => $curlVersion['version'],
        'ssl_version' => $curlVersion['ssl_version'],
        'user_agent' => $userAgent
    ];
}

// Display cURL information
$curlInfo = getCurlInfo();
if ($curlInfo['available']) {
    echo "\ncURL Information:\n";
    echo "Version: {$curlInfo['version']}\n";
    echo "SSL Version: {$curlInfo['ssl_version']}\n";
    echo "User Agent: {$curlInfo['user_agent']}\n";
} else {
    echo "\n{$curlInfo['message']}\n";
}
```

## Package Management

### isInstalled()

```php
public function isInstalled(
    array $packages,
    bool $incDevReq = false,
): bool
```

Checks if PHP extensions or Composer packages are installed.

Parameters:

- `$packages`: An array of package names to check
- `$incDevReq`: Whether to include dev requirements when checking Composer packages

Returns a boolean indicating whether all the specified packages are installed.

Example:

```php
use Valksor\Functions\Local;

// Check if common PHP extensions are installed
$requiredExtensions = ['json', 'pdo', 'mbstring'];
$extensionsInstalled = Local::isInstalled($requiredExtensions);
echo "Required extensions are " . ($extensionsInstalled ? "installed" : "not installed") . "\n";
// Output: Required extensions are installed (assuming these common extensions are installed)

// Check if specific extensions are installed
$optionalExtensions = ['imagick', 'redis', 'memcached'];
$optionalInstalled = Local::isInstalled($optionalExtensions);
echo "Optional extensions are " . ($optionalInstalled ? "all installed" : "not all installed") . "\n";
// Output will depend on your PHP installation

// Check individual extensions
foreach ($optionalExtensions as $extension) {
    $isInstalled = Local::isInstalled([$extension]);
    echo "Extension {$extension} is " . ($isInstalled ? "installed" : "not installed") . "\n";
}

// Check if Composer packages are installed (production dependencies only)
$requiredPackages = ['symfony/console', 'doctrine/orm'];
$packagesInstalled = Local::isInstalled($requiredPackages);
echo "Required packages are " . ($packagesInstalled ? "installed" : "not installed") . "\n";
// Output will depend on your project's dependencies

// Check if Composer packages are installed (including dev dependencies)
$devPackages = ['phpunit/phpunit', 'symfony/var-dumper'];
$devPackagesInstalled = Local::isInstalled($devPackages, true);
echo "Dev packages are " . ($devPackagesInstalled ? "installed" : "not installed") . " (including dev requirements)\n";
// Output will depend on your project's dev dependencies

// Practical use case: Conditionally load features based on available extensions
function loadImageProcessingFeature() {
    $requiredExtensions = ['gd', 'exif'];

    if (Local::isInstalled($requiredExtensions)) {
        echo "Image processing feature is available.\n";
        // Load image processing code here
        return true;
    } else {
        echo "Image processing feature is not available. Missing required extensions.\n";
        return false;
    }
}

// Try to load the feature
$imageFeatureLoaded = loadImageProcessingFeature();

// Practical use case: Check dependencies before performing an operation
function performDatabaseMigration($dryRun = false) {
    $requiredPackages = ['doctrine/migrations', 'doctrine/orm'];

    if (!Local::isInstalled($requiredPackages)) {
        echo "Cannot perform database migration. Required packages are missing.\n";
        return false;
    }

    echo "Performing database migration" . ($dryRun ? " (dry run)" : "") . "...\n";
    // Migration code would go here
    echo "Migration completed successfully.\n";
    return true;
}

// Try to perform a database migration
$migrationResult = performDatabaseMigration(true);

// Practical use case: Provide helpful error messages for missing dependencies
function checkSystemRequirements() {
    $requirements = [
        'extensions' => [
            'required' => ['json', 'pdo', 'mbstring', 'xml'],
            'optional' => ['intl', 'curl', 'zip']
        ],
        'packages' => [
            'required' => ['symfony/console', 'doctrine/orm'],
            'optional' => ['symfony/mailer', 'symfony/twig-bundle']
        ]
    ];

    $missingRequired = [];
    $missingOptional = [];

    // Check required extensions
    foreach ($requirements['extensions']['required'] as $ext) {
        if (!Local::isInstalled([$ext])) {
            $missingRequired[] = "PHP extension: {$ext}";
        }
    }

    // Check optional extensions
    foreach ($requirements['extensions']['optional'] as $ext) {
        if (!Local::isInstalled([$ext])) {
            $missingOptional[] = "PHP extension: {$ext}";
        }
    }

    // Check required packages
    foreach ($requirements['packages']['required'] as $pkg) {
        if (!Local::isInstalled([$pkg])) {
            $missingRequired[] = "Composer package: {$pkg}";
        }
    }

    // Check optional packages
    foreach ($requirements['packages']['optional'] as $pkg) {
        if (!Local::isInstalled([$pkg])) {
            $missingOptional[] = "Composer package: {$pkg}";
        }
    }

    // Return the results
    return [
        'success' => empty($missingRequired),
        'missing_required' => $missingRequired,
        'missing_optional' => $missingOptional
    ];
}

// Check system requirements
$requirementsCheck = checkSystemRequirements();

if ($requirementsCheck['success']) {
    echo "All required dependencies are installed!\n";
} else {
    echo "Missing required dependencies:\n";
    foreach ($requirementsCheck['missing_required'] as $missing) {
        echo "- {$missing}\n";
    }
}

if (!empty($requirementsCheck['missing_optional'])) {
    echo "\nMissing optional dependencies (some features may be limited):\n";
    foreach ($requirementsCheck['missing_optional'] as $missing) {
        echo "- {$missing}\n";
    }
}
```

### willBeAvailable()

```php
public function willBeAvailable(
    string $package,
    string $class,
    array $parentPackages,
    string $rootPackageCheck = 'valksor/php-valksor',
): bool
```

Checks if a class from a package will be available at runtime.

Parameters:

- `$package`: The name of the package to check
- `$class`: The name of the class to check
- `$parentPackages`: An array of parent package names to check
- `$rootPackageCheck`: The name of the root package to check

Returns a boolean indicating whether the class will be available at runtime.

Example:

```php
use Valksor\Functions\Local;

// Basic usage - check if a class from a package will be available
$package = 'symfony/console';
$class = 'Symfony\\Component\\Console\\Application';
$parentPackages = ['symfony/framework-bundle'];
$willBeAvailable = Local::willBeAvailable($package, $class, $parentPackages);
echo "The class {$class} from package {$package} " . ($willBeAvailable ? "will" : "will not") . " be available at runtime.\n";
// Output will depend on your project's dependencies

// Check with a custom root package
$customRootPackage = 'my/custom-package';
$willBeAvailableCustomRoot = Local::willBeAvailable($package, $class, $parentPackages, $customRootPackage);
echo "With custom root package: " . ($willBeAvailableCustomRoot ? "Available" : "Not available") . "\n";

// Practical use case: Conditionally use features based on package availability
function getMailer() {
    $package = 'symfony/mailer';
    $class = 'Symfony\\Component\\Mailer\\Mailer';
    $parentPackages = ['symfony/framework-bundle'];

    if (Local::willBeAvailable($package, $class, $parentPackages)) {
        echo "Using Symfony Mailer for sending emails.\n";
        // Code to use Symfony Mailer
        return 'symfony_mailer';
    } else {
        echo "Symfony Mailer is not available. Using fallback mail function.\n";
        // Fallback to PHP's mail() function
        return 'php_mail';
    }
}

// Get the appropriate mailer
$mailer = getMailer();

// Practical use case: Feature detection in a library
class FeatureManager
{
    private $availableFeatures = [];

    public function __construct()
    {
        $this->detectAvailableFeatures();
    }

    private function detectAvailableFeatures()
    {
        // Check for cache feature
        $this->availableFeatures['cache'] = Local::willBeAvailable(
            'symfony/cache',
            'Symfony\\Component\\Cache\\Adapter\\FilesystemAdapter',
            ['symfony/framework-bundle']
        );

        // Check for HTTP client feature
        $this->availableFeatures['http_client'] = Local::willBeAvailable(
            'symfony/http-client',
            'Symfony\\Component\\HttpClient\\HttpClient',
            ['symfony/framework-bundle']
        );

        // Check for messenger feature
        $this->availableFeatures['messenger'] = Local::willBeAvailable(
            'symfony/messenger',
            'Symfony\\Component\\Messenger\\MessageBus',
            ['symfony/framework-bundle']
        );
    }

    public function isFeatureAvailable(string $featureName): bool
    {
        return $this->availableFeatures[$featureName] ?? false;
    }

    public function getAvailableFeatures(): array
    {
        return array_keys(array_filter($this->availableFeatures));
    }
}

// Using the feature manager
$featureManager = new FeatureManager();
echo "Available features: " . implode(', ', $featureManager->getAvailableFeatures()) . "\n";

if ($featureManager->isFeatureAvailable('cache')) {
    echo "Cache feature is available. Using cache for better performance.\n";
    // Code to use cache
} else {
    echo "Cache feature is not available. Using alternative storage.\n";
    // Alternative code
}

// Practical use case: Graceful degradation in a package
function setupLogging() {
    $monologAvailable = Local::willBeAvailable(
        'monolog/monolog',
        'Monolog\\Logger',
        []
    );

    if ($monologAvailable) {
        echo "Setting up Monolog for advanced logging capabilities.\n";
        // Code to set up Monolog
        return 'monolog';
    } else {
        echo "Monolog is not available. Using simple file logging.\n";
        // Code for simple file logging
        return 'file_logger';
    }
}

// Set up logging
$logger = setupLogging();

// Practical use case: Checking for optional integrations
function checkIntegrations() {
    $integrations = [
        'redis' => [
            'package' => 'predis/predis',
            'class' => 'Predis\\Client',
            'parents' => []
        ],
        'elasticsearch' => [
            'package' => 'elasticsearch/elasticsearch',
            'class' => 'Elasticsearch\\Client',
            'parents' => []
        ],
        'doctrine' => [
            'package' => 'doctrine/orm',
            'class' => 'Doctrine\\ORM\\EntityManager',
            'parents' => ['doctrine/doctrine-bundle']
        ]
    ];

    $availableIntegrations = [];

    foreach ($integrations as $name => $integration) {
        $available = Local::willBeAvailable(
            $integration['package'],
            $integration['class'],
            $integration['parents']
        );

        $availableIntegrations[$name] = $available;
        echo "Integration '{$name}' is " . ($available ? "available" : "not available") . ".\n";
    }

    return $availableIntegrations;
}

// Check available integrations
$availableIntegrations = checkIntegrations();
```
