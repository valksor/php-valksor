# Valksor Functions: Iteration - Features

This document lists all the functions available in the Valksor Functions: Iteration package.

## Array Manipulation

### addElementIfNotExists()

```php
public function addElementIfNotExists(
    ?array &$array,
    mixed $element,
    mixed $key = null,
): void
```

Adds an element to an array if it doesn't already exist.

Parameters:
- `$array`: The array to modify (passed by reference)
- `$element`: The element to add
- `$key`: Optional key to use for the element

Example:
```php
use Valksor\Functions\Iteration;

// Add element to an empty array
$array = null;
Iteration::addElementIfNotExists($array, 'apple');
print_r($array); // Output: Array ( [0] => apple )

// Add element with a specific key
$fruits = ['a' => 'apple', 'b' => 'banana'];
Iteration::addElementIfNotExists($fruits, 'cherry', 'c');
print_r($fruits); // Output: Array ( [a] => apple [b] => banana [c] => cherry )

// Element already exists (by value), won't be added
$numbers = [1, 2, 3];
Iteration::addElementIfNotExists($numbers, 2);
print_r($numbers); // Output: Array ( [0] => 1 [1] => 2 [2] => 3 )

// Key already exists, won't be added
$data = ['id' => 1, 'name' => 'John'];
Iteration::addElementIfNotExists($data, 'Smith', 'name');
print_r($data); // Output: Array ( [id] => 1 [name] => John )
```

### arrayFlipRecursive()

```php
public function arrayFlipRecursive(
    array $input = [],
): array
```

Recursively flips the keys and values of an array.

Parameters:
- `$input`: The input array

Returns a new array with keys and values flipped.

Example:
```php
use Valksor\Functions\Iteration;

// Simple key-value pairs
$array = ['a' => 1, 'b' => 2, 'c' => 3];
$flipped = Iteration::arrayFlipRecursive($array);
print_r($flipped); // Output: Array ( [1] => a [2] => b [3] => c )

// Array with nested arrays
$nested = [
    'fruits' => ['apple', 'banana'],
    'colors' => ['red', 'blue']
];
$flippedNested = Iteration::arrayFlipRecursive($nested);
print_r($flippedNested);
// Output: Array ( [fruits] => Array ( [0] => apple [1] => banana ) [colors] => Array ( [0] => red [1] => blue ) )

// Empty array
$empty = [];
$flippedEmpty = Iteration::arrayFlipRecursive($empty);
print_r($flippedEmpty); // Output: Array ( )
```

### arrayIntersectKeyRecursive()

```php
public function arrayIntersectKeyRecursive(
    array $first = [],
    array $second = [],
): array
```

Recursively intersects two arrays by their keys.

Parameters:
- `$first`: The first array
- `$second`: The second array

Returns an array containing all the entries from the first array that have keys which are present in the second array, including nested arrays.

Example:
```php
use Valksor\Functions\Iteration;

// Simple arrays
$array1 = ['a' => 1, 'b' => 2, 'c' => 3];
$array2 = ['a' => 10, 'c' => 30, 'd' => 40];
$result = Iteration::arrayIntersectKeyRecursive($array1, $array2);
print_r($result); // Output: Array ( [a] => 1 [c] => 3 )

// Arrays with nested arrays
$nested1 = [
    'user' => [
        'name' => 'John',
        'age' => 30,
        'email' => 'john@example.com'
    ],
    'settings' => [
        'theme' => 'dark',
        'notifications' => true
    ]
];

$nested2 = [
    'user' => [
        'name' => 'Jane',
        'email' => 'jane@example.com',
        'phone' => '123-456-7890'
    ],
    'settings' => [
        'language' => 'en',
        'notifications' => false
    ]
];

$nestedResult = Iteration::arrayIntersectKeyRecursive($nested1, $nested2);
print_r($nestedResult);
// Output:
// Array (
//     [user] => Array (
//         [name] => John
//         [email] => john@example.com
//     )
//     [settings] => Array (
//         [notifications] => 1
//     )
// )

// Empty arrays
$empty1 = [];
$empty2 = [];
$emptyResult = Iteration::arrayIntersectKeyRecursive($empty1, $empty2);
print_r($emptyResult); // Output: Array ( )
```

### arrayValuesFiltered()

```php
public function arrayValuesFiltered(
    array $array,
): array
```

Returns all the values from an array, filtering out null values and re-indexing the array.

Parameters:
- `$array`: The input array

Returns a filtered array with numeric indices.

Example:
```php
use Valksor\Functions\Iteration;

// Array with null values
$array = ['apple', null, 'banana', null, 'cherry'];
$filtered = Iteration::arrayValuesFiltered($array);
print_r($filtered); // Output: Array ( [0] => apple [1] => banana [2] => cherry )

// Associative array with null values
$assoc = ['a' => 'apple', 'b' => null, 'c' => 'cherry', 'd' => null];
$filteredAssoc = Iteration::arrayValuesFiltered($assoc);
print_r($filteredAssoc); // Output: Array ( [0] => apple [1] => cherry )

// Array with false and empty string values (these are preserved)
$mixed = ['apple', false, '', null, 0, 'cherry'];
$filteredMixed = Iteration::arrayValuesFiltered($mixed);
print_r($filteredMixed); // Output: Array ( [0] => apple [1] => [2] => [3] => 0 [4] => cherry )

// Empty array
$empty = [];
$filteredEmpty = Iteration::arrayValuesFiltered($empty);
print_r($filteredEmpty); // Output: Array ( )
```

### filterKeyEndsWith()

```php
public function filterKeyEndsWith(
    array $array,
    string $needle,
): array
```

Filters an array to keep only elements whose keys end with the specified string.

Parameters:
- `$array`: The input array
- `$needle`: The string to check for at the end of keys

Returns a filtered array.

Example:
```php
use Valksor\Functions\Iteration;

// Filter keys ending with 'Name'
$data = [
    'firstName' => 'John',
    'lastName' => 'Doe',
    'age' => 30,
    'userName' => 'johndoe',
    'email' => 'john@example.com'
];
$nameFields = Iteration::filterKeyEndsWith($data, 'Name');
print_r($nameFields);
// Output: Array ( [firstName] => John [lastName] => Doe [userName] => johndoe )

// Filter keys ending with 'Id'
$records = [
    'userId' => 1001,
    'name' => 'Product A',
    'productId' => 5001,
    'categoryId' => 101,
    'price' => 29.99
];
$idFields = Iteration::filterKeyEndsWith($records, 'Id');
print_r($idFields);
// Output: Array ( [userId] => 1001 [productId] => 5001 [categoryId] => 101 )

// No matching keys
$data = ['name' => 'John', 'age' => 30, 'city' => 'New York'];
$result = Iteration::filterKeyEndsWith($data, 'date');
print_r($result); // Output: Array ( )

// Empty array
$empty = [];
$result = Iteration::filterKeyEndsWith($empty, 'test');
print_r($result); // Output: Array ( )
```

### filterKeyStartsWith()

```php
public function filterKeyStartsWith(
    array $array,
    string $needle,
): array
```

Filters an array to keep only elements whose keys start with the specified string.

Parameters:
- `$array`: The input array
- `$needle`: The string to check for at the start of keys

Returns a filtered array.

Example:
```php
use Valksor\Functions\Iteration;

// Filter keys starting with 'user'
$data = [
    'userId' => 1001,
    'userName' => 'johndoe',
    'userEmail' => 'john@example.com',
    'firstName' => 'John',
    'lastName' => 'Doe'
];
$userFields = Iteration::filterKeyStartsWith($data, 'user');
print_r($userFields);
// Output: Array ( [userId] => 1001 [userName] => johndoe [userEmail] => john@example.com )

// Filter keys starting with 'is'
$flags = [
    'isActive' => true,
    'isAdmin' => false,
    'isVerified' => true,
    'name' => 'John',
    'role' => 'editor'
];
$booleanFlags = Iteration::filterKeyStartsWith($flags, 'is');
print_r($booleanFlags);
// Output: Array ( [isActive] => 1 [isAdmin] => [isVerified] => 1 )

// No matching keys
$data = ['name' => 'John', 'age' => 30, 'city' => 'New York'];
$result = Iteration::filterKeyStartsWith($data, 'user');
print_r($result); // Output: Array ( )

// Empty array
$empty = [];
$result = Iteration::filterKeyStartsWith($empty, 'test');
print_r($result); // Output: Array ( )
```

### firstMatchAsString()

```php
public function firstMatchAsString(
    array $array,
    string $pattern,
): string
```

Returns the first element from an array that matches the specified pattern.

Parameters:
- `$array`: The input array
- `$pattern`: The pattern to match against

Returns the first matching element as a string, or an empty string if no match is found.

Example:
```php
use Valksor\Functions\Iteration;

// Find the first element matching a pattern
$files = [
    'document.pdf',
    'image.jpg',
    'report.pdf',
    'logo.png'
];
$pdfFile = Iteration::firstMatchAsString($files, '/\.pdf$/');
echo $pdfFile; // Output: document.pdf

// Find the first element containing a substring
$names = ['John Doe', 'Jane Smith', 'Robert Johnson', 'Sarah Williams'];
$nameWithJohn = Iteration::firstMatchAsString($names, '/John/');
echo $nameWithJohn; // Output: John Doe

// No matching elements
$numbers = ['123', '456', '789'];
$result = Iteration::firstMatchAsString($numbers, '/abc/');
echo $result; // Output: (empty string)

// Empty array
$empty = [];
$result = Iteration::firstMatchAsString($empty, '/test/');
echo $result; // Output: (empty string)
```

### haveCommonElements()

```php
public function haveCommonElements(
    array $first,
    array $second,
): bool
```

Checks if two arrays have at least one common element.

Parameters:
- `$first`: The first array
- `$second`: The second array

Returns a boolean indicating whether the arrays have common elements.

Example:
```php
use Valksor\Functions\Iteration;

// Arrays with common elements
$array1 = [1, 2, 3, 4, 5];
$array2 = [5, 6, 7, 8, 9];
$hasCommon1 = Iteration::haveCommonElements($array1, $array2);
var_dump($hasCommon1); // Output: bool(true) - because 5 is in both arrays

// Arrays with no common elements
$array3 = ['a', 'b', 'c'];
$array4 = ['d', 'e', 'f'];
$hasCommon2 = Iteration::haveCommonElements($array3, $array4);
var_dump($hasCommon2); // Output: bool(false)

// Arrays with multiple common elements
$array5 = ['apple', 'banana', 'cherry'];
$array6 = ['banana', 'cherry', 'date'];
$hasCommon3 = Iteration::haveCommonElements($array5, $array6);
var_dump($hasCommon3); // Output: bool(true)

// Empty arrays
$empty1 = [];
$empty2 = [];
$hasCommon4 = Iteration::haveCommonElements($empty1, $empty2);
var_dump($hasCommon4); // Output: bool(false)

// One empty array
$array7 = [1, 2, 3];
$empty3 = [];
$hasCommon5 = Iteration::haveCommonElements($array7, $empty3);
var_dump($hasCommon5); // Output: bool(false)
```

### isEmpty()

```php
public function isEmpty(
    mixed $value,
): bool
```

Checks if a value is empty (null, empty string, empty array, etc.).

Parameters:
- `$value`: The value to check

Returns a boolean indicating whether the value is empty.

Example:
```php
use Valksor\Functions\Iteration;

// Null value
$isNull =

### isAssociative()

```php
public function isAssociative(
    mixed $array,
    bool $allowList = false,
): bool
```

Checks if an array is associative (has string keys).

Parameters:
- `$array`: The array to check
- `$allowList`: If true, returns true for any non-empty array

Returns a boolean indicating whether the array is associative.

Example:
```php
use Valksor\Functions\Iteration;

// Associative array (has string keys)
$assocArray = ['name' => 'John', 'age' => 30, 'city' => 'New York'];
$isAssoc1 = Iteration::isAssociative($assocArray);
var_dump($isAssoc1); // Output: bool(true)

// Indexed array (has numeric keys)
$indexedArray = ['apple', 'banana', 'cherry'];
$isAssoc2 = Iteration::isAssociative($indexedArray);
var_dump($isAssoc2); // Output: bool(false)

// Mixed array (has both string and numeric keys)
$mixedArray = [0 => 'apple', 'fruit' => 'banana', 2 => 'cherry'];
$isAssoc3 = Iteration::isAssociative($mixedArray);
var_dump($isAssoc3); // Output: bool(true)

// Empty array
$emptyArray = [];
$isAssoc4 = Iteration::isAssociative($emptyArray);
var_dump($isAssoc4); // Output: bool(false)

// Using allowList parameter
$isAssoc5 = Iteration::isAssociative($indexedArray, true);
var_dump($isAssoc5); // Output: bool(true) - returns true for any non-empty array

// Non-array value
$isAssoc6 = Iteration::isAssociative('not an array');
var_dump($isAssoc6); // Output: bool(false)
```

### isMultiDimensional()

```php
public function isMultiDimensional(
    array $keys,
): bool
```

Checks if an array is multi-dimensional.

Parameters:
- `$keys`: The array to check

Returns a boolean indicating whether the array is multi-dimensional.

Example:
```php
use Valksor\Functions\Iteration;

// Single-dimensional array
$singleDim = ['apple', 'banana', 'cherry'];
$isMulti1 = Iteration::isMultiDimensional($singleDim);
var_dump($isMulti1); // Output: bool(false)

// Multi-dimensional array (array of arrays)
$multiDim = [
    ['name' => 'John', 'age' => 30],
    ['name' => 'Jane', 'age' => 25],
    ['name' => 'Bob', 'age' => 40]
];
$isMulti2 = Iteration::isMultiDimensional($multiDim);
var_dump($isMulti2); // Output: bool(true)

// Mixed array (some elements are arrays, some are not)
$mixedDim = [
    'person1' => ['name' => 'John', 'age' => 30],
    'person2' => ['name' => 'Jane', 'age' => 25],
    'fruit' => 'apple'
];
$isMulti3 = Iteration::isMultiDimensional($mixedDim);
var_dump($isMulti3); // Output: bool(true)

// Empty array
$emptyArray = [];
$isMulti4 = Iteration::isMultiDimensional($emptyArray);
var_dump($isMulti4); // Output: bool(false)

// Deeply nested array
$deeplyNested = [
    'level1' => [
        'level2' => [
            'level3' => ['deep' => 'value']
        ]
    ]
];
$isMulti5 = Iteration::isMultiDimensional($deeplyNested);
var_dump($isMulti5); // Output: bool(true)
```

## Array Transformation

### makeMultiDimensional()

```php
public function makeMultiDimensional(
    array $array,
): array
```

Converts a one-dimensional array into a multi-dimensional array.

Parameters:
- `$array`: The array to convert

Returns a multi-dimensional array where each element of the original array is wrapped in its own array.

Example:
```php
use Valksor\Functions\Iteration;

// Simple indexed array
$fruits = ['apple', 'banana', 'cherry'];
$multiDimFruits = Iteration::makeMultiDimensional($fruits);
print_r($multiDimFruits);
// Output:
// Array
// (
//     [0] => Array
//         (
//             [0] => apple
//         )
//     [1] => Array
//         (
//             [0] => banana
//         )
//     [2] => Array
//         (
//             [0] => cherry
//         )
// )

// Associative array
$person = ['name' => 'John', 'age' => 30, 'city' => 'New York'];
$multiDimPerson = Iteration::makeMultiDimensional($person);
print_r($multiDimPerson);
// Output:
// Array
// (
//     [name] => Array
//         (
//             [0] => John
//         )
//     [age] => Array
//         (
//             [0] => 30
//         )
//     [city] => Array
//         (
//             [0] => New York
//         )
// )

// Empty array
$empty = [];
$multiDimEmpty = Iteration::makeMultiDimensional($empty);
print_r($multiDimEmpty);
// Output: Array ( )

// Practical use case: Preparing data for batch insert
$users = [
    ['John', 'john@example.com'],
    ['Jane', 'jane@example.com'],
    ['Bob', 'bob@example.com']
];
$batchData = Iteration::makeMultiDimensional($users);
print_r($batchData);
// Output:
// Array
// (
//     [0] => Array
//         (
//             [0] => Array
//                 (
//                     [0] => John
//                     [1] => john@example.com
//                 )
//         )
//     [1] => Array
//         (
//             [0] => Array
//                 (
//                     [0] => Jane
//                     [1] => jane@example.com
//                 )
//         )
//     [2] => Array
//         (
//             [0] => Array
//                 (
//                     [0] => Bob
//                     [1] => bob@example.com
//                 )
//         )
// )
```

### unique()

```php
public function unique(
    array $input,
    bool $keepKeys = false,
): array
```

Gets unique values from an array.

Parameters:
- `$input`: The input array
- `$keepKeys`: If true, preserves the keys of the original array

Returns an array with unique values.

Example:
```php
use Valksor\Functions\Iteration;

// Array with duplicate values
$numbers = [1, 2, 3, 2, 4, 3, 5, 1, 6];
$uniqueNumbers = Iteration::unique($numbers);
print_r($uniqueNumbers);
// Output: Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 4 [4] => 5 [5] => 6 )

// Associative array with duplicate values
$fruits = [
    'a' => 'apple',
    'b' => 'banana',
    'c' => 'cherry',
    'd' => 'apple',
    'e' => 'banana',
    'f' => 'grape'
];

// Without preserving keys (default)
$uniqueFruits1 = Iteration::unique($fruits);
print_r($uniqueFruits1);
// Output: Array ( [0] => apple [1] => banana [2] => cherry [3] => grape )

// With preserving keys
$uniqueFruits2 = Iteration::unique($fruits, true);
print_r($uniqueFruits2);
// Output: Array ( [a] => apple [b] => banana [c] => cherry [f] => grape )

// Array with objects (comparing by reference)
$obj1 = new stdClass();
$obj1->name = 'Object 1';
$obj2 = new stdClass();
$obj2->name = 'Object 2';
$obj3 = new stdClass();
$obj3->name = 'Object 3';

$objects = [$obj1, $obj2, $obj1, $obj3, $obj2];
$uniqueObjects = Iteration::unique($objects);
print_r($uniqueObjects);
// Output: Array with unique objects (obj1, obj2, obj3)

// Empty array
$empty = [];
$uniqueEmpty = Iteration::unique($empty);
print_r($uniqueEmpty);
// Output: Array ( )
```

### arrayToString()

```php
public function arrayToString(
    array $array,
    string $glue = ',',
): string
```

Converts an array to a string.

Parameters:
- `$array`: The array to convert
- `$glue`: The string to use as a separator

Returns a string representation of the array.

Example:
```php
use Valksor\Functions\Iteration;

// Simple indexed array with default glue (comma)
$fruits = ['apple', 'banana', 'cherry'];
$fruitsString = Iteration::arrayToString($fruits);
echo $fruitsString; // Output: apple,banana,cherry

// Using a custom glue
$numbers = [1, 2, 3, 4, 5];
$numbersString = Iteration::arrayToString($numbers, ' | ');
echo $numbersString; // Output: 1 | 2 | 3 | 4 | 5

// Associative array (only values are used)
$person = ['name' => 'John', 'age' => 30, 'city' => 'New York'];
$personString = Iteration::arrayToString($person, ', ');
echo $personString; // Output: John, 30, New York

// Empty array
$empty = [];
$emptyString = Iteration::arrayToString($empty);
echo $emptyString; // Output: (empty string)

// Practical use case: Creating a CSV line
$csvData = ['John Doe', 'john@example.com', '123-456-7890'];
$csvLine = Iteration::arrayToString($csvData, ',');
echo $csvLine; // Output: John Doe,john@example.com,123-456-7890

// Practical use case: Creating an SQL IN clause
$ids = [1001, 1002, 1003, 1004];
$inClause = 'SELECT * FROM users WHERE id IN (' . Iteration::arrayToString($ids) . ')';
echo $inClause; // Output: SELECT * FROM users WHERE id IN (1001,1002,1003,1004)
```

## JSON Handling

### jsonEncode()

```php
public function jsonEncode(
    mixed $value,
    int $flags = 0,
    int $depth = 512,
): string
```

Encodes a value as JSON with error handling.

Parameters:
- `$value`: The value to encode
- `$flags`: JSON encoding flags
- `$depth`: Maximum depth

Returns a JSON string.

Example:
```php
use Valksor\Functions\Iteration;

// Encoding a simple array
$data = ['name' => 'John', 'age' => 30, 'city' => 'New York'];
$json = Iteration::jsonEncode($data);
echo $json; // Output: {"name":"John","age":30,"city":"New York"}

// Using JSON_PRETTY_PRINT flag for formatted output
$prettyJson = Iteration::jsonEncode($data, JSON_PRETTY_PRINT);
echo $prettyJson;
// Output:
// {
//     "name": "John",
//     "age": 30,
//     "city": "New York"
// }

// Handling UTF-8 characters with JSON_UNESCAPED_UNICODE
$unicodeData = ['name' => 'José', 'greeting' => 'Olá mundo!'];
$unicodeJson = Iteration::jsonEncode($unicodeData, JSON_UNESCAPED_UNICODE);
echo $unicodeJson; // Output: {"name":"José","greeting":"Olá mundo!"}

// Encoding a complex nested structure
$complexData = [
    'user' => [
        'name' => 'John',
        'contacts' => [
            'email' => 'john@example.com',
            'phone' => '123-456-7890'
        ]
    ],
    'orders' => [
        ['id' => 1, 'product' => 'Laptop', 'price' => 999.99],
        ['id' => 2, 'product' => 'Phone', 'price' => 499.99]
    ],
    'active' => true
];
$complexJson = Iteration::jsonEncode($complexData);
echo $complexJson;
// Output: {"user":{"name":"John","contacts":{"email":"john@example.com","phone":"123-456-7890"}},"orders":[{"id":1,"product":"Laptop","price":999.99},{"id":2,"product":"Phone","price":499.99}],"active":true}

// Error handling (the function handles errors internally)
try {
    // Create a recursive structure that would normally cause an error
    $recursive = ['data' => null];
    $recursive['data'] = &$recursive;

    $recursiveJson = Iteration::jsonEncode($recursive);
    echo $recursiveJson;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
    // The function would handle this internally and throw an appropriate exception
}
```

### jsonDecode()

```php
public function jsonDecode(
    string $json,
    ?bool $associative = null,
    int $depth = 512,
    int $flags = 0,
): mixed
```

Decodes a JSON string with error handling.

Parameters:
- `$json`: The JSON string to decode
- `$associative`: When true, returns arrays instead of objects
- `$depth`: Maximum depth
- `$flags`: JSON decoding flags

Returns the decoded value.

Example:
```php
use Valksor\Functions\Iteration;

// Decoding a simple JSON string to an object (default)
$jsonString = '{"name":"John","age":30,"city":"New York"}';
$object = Iteration::jsonDecode($jsonString);
echo $object->name; // Output: John
echo $object->age;  // Output: 30
echo $object->city; // Output: New York

// Decoding to an associative array
$array = Iteration::jsonDecode($jsonString, true);
echo $array['name']; // Output: John
echo $array['age'];  // Output: 30
echo $array['city']; // Output: New York

// Decoding a JSON array
$jsonArray = '["apple","banana","cherry"]';
$fruits = Iteration::jsonDecode($jsonArray, true);
print_r($fruits);
// Output: Array ( [0] => apple [1] => banana [2] => cherry )

// Decoding a complex nested JSON structure
$complexJson = '{"user":{"name":"John","contacts":{"email":"john@example.com","phone":"123-456-7890"}},"orders":[{"id":1,"product":"Laptop","price":999.99},{"id":2,"product":"Phone","price":499.99}],"active":true}';
$data = Iteration::jsonDecode($complexJson, true);
echo $data['user']['name']; // Output: John
echo $data['user']['contacts']['email']; // Output: john@example.com
echo $data['orders'][0]['product']; // Output: Laptop
echo $data['active'] ? 'Yes' : 'No'; // Output: Yes

// Error handling for invalid JSON
try {
    $invalidJson = '{"name":"John", "age":30, missing quotes for key}';
    $result = Iteration::jsonDecode($invalidJson);
    print_r($result);
} catch (\Exception $e) {
    echo "Error decoding JSON: " . $e->getMessage();
    // The function would handle this internally and throw an appropriate exception
}

// Using JSON_THROW_ON_ERROR flag (PHP 7.3+)
try {
    $invalidJson = '{"name":"John", "age":30,}'; // Invalid trailing comma
    $result = Iteration::jsonDecode($invalidJson, true, 512, JSON_THROW_ON_ERROR);
    print_r($result);
} catch (\Exception $e) {
    echo "JSON Error: " . $e->getMessage();
    // Output would depend on the specific error
}
```
