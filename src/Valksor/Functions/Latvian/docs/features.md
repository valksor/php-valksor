# Valksor Functions: Latvian - Features

This document lists all the functions available in the Valksor Functions: Latvian package.

## Text Comparison and Sorting

### compare()

```php
public function compare(
    array|object $first,
    array|object $second,
    string|int $field,
): int
```

Compares two arrays or objects based on a specified field using Latvian alphabet rules.

Parameters:
- `$first`: First array or object to compare
- `$second`: Second array or object to compare
- `$field`: The field to use for comparison

Returns:
- Negative integer if $first is less than $second
- Zero if $first equals $second
- Positive integer if $first is greater than $second

Example:
```php
use Valksor\Functions\Latvian;

// Comparing arrays with Latvian names
$person1 = ['name' => 'Ādams', 'age' => 30];
$person2 = ['name' => 'Čarlijs', 'age' => 25];

$result1 = Latvian::compare($person1, $person2, 'name');
echo "Result 1: " . $result1 . "\n";
// Output: Result 1: -1 (negative because 'Ādams' comes before 'Čarlijs' in Latvian alphabet)

$person3 = ['name' => 'Ņikita', 'age' => 35];
$person4 = ['name' => 'Zane', 'age' => 28];

$result2 = Latvian::compare($person3, $person4, 'name');
echo "Result 2: " . $result2 . "\n";
// Output: Result 2: -1 (negative because 'Ņikita' comes before 'Zane' in Latvian alphabet)

// Comparing objects with Latvian city names
$city1 = new stdClass();
$city1->name = 'Rīga';
$city1->population = 632614;

$city2 = new stdClass();
$city2->name = 'Liepāja';
$city2->population = 68945;

$result3 = Latvian::compare($city1, $city2, 'name');
echo "Result 3: " . $result3 . "\n";
// Output: Result 3: 1 (positive because 'Rīga' comes after 'Liepāja' in Latvian alphabet)

// Comparing arrays with same values
$person5 = ['name' => 'Jānis', 'age' => 40];
$person6 = ['name' => 'Jānis', 'age' => 45];

$result4 = Latvian::compare($person5, $person6, 'name');
echo "Result 4: " . $result4 . "\n";
// Output: Result 4: 0 (zero because the names are equal)

// Using the function for custom sorting
$people = [
    ['name' => 'Ēriks', 'age' => 32],
    ['name' => 'Ādams', 'age' => 30],
    ['name' => 'Čarlijs', 'age' => 25],
    ['name' => 'Zane', 'age' => 28]
];

usort($people, function($a, $b) {
    return Latvian::compare($a, $b, 'name');
});

echo "Sorted names according to Latvian alphabet:\n";
foreach ($people as $person) {
    echo $person['name'] . "\n";
}
// Output:
// Ādams
// Čarlijs
// Ēriks
// Zane
```

### sortLatvian()

```php
public function sortLatvian(
    array &$names,
    string|int $field,
    ?array $callback = null,
): bool
```

Sorts an array of arrays or objects based on a specified field using Latvian alphabet rules.

Parameters:
- `$names`: The array to sort (passed by reference)
- `$field`: The field to sort by
- `$callback`: Optional custom comparison callback

Returns a boolean indicating success.

Example:
```php
use Valksor\Functions\Latvian;

// Sorting an array of arrays with Latvian names
$students = [
    ['name' => 'Jānis', 'grade' => 'B'],
    ['name' => 'Ēriks', 'grade' => 'A'],
    ['name' => 'Līga', 'grade' => 'A+'],
    ['name' => 'Ādams', 'grade' => 'C'],
    ['name' => 'Čarlijs', 'grade' => 'B+'],
    ['name' => 'Zane', 'grade' => 'A-'],
];

// Sort by name using Latvian alphabet rules
$success1 = Latvian::sortLatvian($students, 'name');
echo "Sort success: " . ($success1 ? 'Yes' : 'No') . "\n";

echo "Students sorted by name:\n";
foreach ($students as $student) {
    echo $student['name'] . " - " . $student['grade'] . "\n";
}
// Output:
// Ādams - C
// Čarlijs - B+
// Ēriks - A
// Jānis - B
// Līga - A+
// Zane - A-

// Sorting an array of objects with Latvian city names
$cities = [];
$city1 = new stdClass();
$city1->name = 'Rīga';
$city1->population = 632614;

$city2 = new stdClass();
$city2->name = 'Liepāja';
$city2->population = 68945;

$city3 = new stdClass();
$city3->name = 'Daugavpils';
$city3->population = 82046;

$city4 = new stdClass();
$city4->name = 'Cēsis';
$city4->population = 14748;

$cities = [$city1, $city2, $city3, $city4];

// Sort by name using Latvian alphabet rules
$success2 = Latvian::sortLatvian($cities, 'name');
echo "\nCities sorted by name:\n";
foreach ($cities as $city) {
    echo $city->name . " - Population: " . $city->population . "\n";
}
// Output:
// Cēsis - Population: 14748
// Daugavpils - Population: 82046
// Liepāja - Population: 68945
// Rīga - Population: 632614

// Using a custom callback for reverse sorting
$customCallback = [Latvian::class, 'compare'];
$success3 = Latvian::sortLatvian($students, 'name', $customCallback);

// Reverse the order
$studentsReversed = array_reverse($students);
echo "\nStudents in reverse order:\n";
foreach ($studentsReversed as $student) {
    echo $student['name'] . " - " . $student['grade'] . "\n";
}
// Output:
// Zane - A-
// Līga - A+
// Jānis - B
// Ēriks - A
// Čarlijs - B+
// Ādams - C
```

## Personal Identification Code Validation

### validatePersonCode()

```php
public function validatePersonCode(
    string $personCode,
): bool
```

Validates a Latvian personal identification code (both new and old formats).

Parameters:
- `$personCode`: The personal identification code to validate

Returns a boolean indicating whether the code is valid.

Example:
```php
use Valksor\Functions\Latvian;

// Validating old format Latvian personal codes (11 digits)
// Note: These are fictional examples, not real personal codes

// Valid old format personal code (format: DDMMYY-XXXXX)
$oldCode1 = '121282-12345';
$isValid1 = Latvian::validatePersonCode($oldCode1);
echo "Old format code 1 is " . ($isValid1 ? 'valid' : 'invalid') . "\n";
// Output: Old format code 1 is valid

// Invalid old format personal code (incorrect date)
$oldCode2 = '321282-12345'; // 32 is not a valid day
$isValid2 = Latvian::validatePersonCode($oldCode2);
echo "Old format code 2 is " . ($isValid2 ? 'valid' : 'invalid') . "\n";
// Output: Old format code 2 is invalid

// Invalid old format personal code (incorrect checksum)
$oldCode3 = '121282-12346'; // Incorrect last digit
$isValid3 = Latvian::validatePersonCode($oldCode3);
echo "Old format code 3 is " . ($isValid3 ? 'valid' : 'invalid') . "\n";
// Output: Old format code 3 is invalid

// Validating new format Latvian personal codes (starting with "32")
// Note: These are fictional examples, not real personal codes

// Valid new format personal code
$newCode1 = '32123456789';
$isValid4 = Latvian::validatePersonCode($newCode1);
echo "New format code 1 is " . ($isValid4 ? 'valid' : 'invalid') . "\n";
// Output: New format code 1 is valid

// Invalid new format personal code
$newCode2 = '32123456780'; // Incorrect checksum
$isValid5 = Latvian::validatePersonCode($newCode2);
echo "New format code 2 is " . ($isValid5 ? 'valid' : 'invalid') . "\n";
// Output: New format code 2 is invalid

// The function also works with or without hyphens
$codeWithHyphen = '121282-12345';
$codeWithoutHyphen = '12128212345';
$isValid6 = Latvian::validatePersonCode($codeWithHyphen);
$isValid7 = Latvian::validatePersonCode($codeWithoutHyphen);
echo "Code with hyphen is " . ($isValid6 ? 'valid' : 'invalid') . "\n";
echo "Code without hyphen is " . ($isValid7 ? 'valid' : 'invalid') . "\n";
// Both should return the same result
```

### cleanPersonCode()

```php
public function cleanPersonCode(
    string $personCode,
): string
```

Cleans and formats a Latvian personal identification code by removing non-numeric characters and hyphens.

Parameters:
- `$personCode`: The personal identification code to clean

Returns the cleaned personal identification code.

Throws:
- `InvalidArgumentException` if the resulting code is not exactly 11 characters long

Example:
```php
use Valksor\Functions\Latvian;

// Note: These are fictional examples, not real personal codes

// Cleaning a code with a hyphen
try {
    $dirtyCode1 = '121282-12345';
    $cleanCode1 = Latvian::cleanPersonCode($dirtyCode1);
    echo "Original: {$dirtyCode1}, Cleaned: {$cleanCode1}\n";
    // Output: Original: 121282-12345, Cleaned: 12128212345
} catch (\InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Cleaning a code with spaces and other characters
try {
    $dirtyCode2 = '121282 - 12345';
    $cleanCode2 = Latvian::cleanPersonCode($dirtyCode2);
    echo "Original: {$dirtyCode2}, Cleaned: {$cleanCode2}\n";
    // Output: Original: 121282 - 12345, Cleaned: 12128212345
} catch (\InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Cleaning a code with letters (which will be removed)
try {
    $dirtyCode3 = 'PK-121282-12345';
    $cleanCode3 = Latvian::cleanPersonCode($dirtyCode3);
    echo "Original: {$dirtyCode3}, Cleaned: {$cleanCode3}\n";
    // Output: Original: PK-121282-12345, Cleaned: 12128212345
} catch (\InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Attempting to clean an invalid code (too short after cleaning)
try {
    $invalidCode = '123-456';
    $cleanInvalidCode = Latvian::cleanPersonCode($invalidCode);
    echo "Original: {$invalidCode}, Cleaned: {$cleanInvalidCode}\n";
} catch (\InvalidArgumentException $e) {
    echo "Error for '{$invalidCode}': " . $e->getMessage() . "\n";
    // Output: Error for '123-456': Invalid person code length
}

// Attempting to clean an invalid code (too long after cleaning)
try {
    $invalidCode2 = '12345678901234';
    $cleanInvalidCode2 = Latvian::cleanPersonCode($invalidCode2);
    echo "Original: {$invalidCode2}, Cleaned: {$cleanInvalidCode2}\n";
} catch (\InvalidArgumentException $e) {
    echo "Error for '{$invalidCode2}': " . $e->getMessage() . "\n";
    // Output: Error for '12345678901234': Invalid person code length
}

// Practical use case: Cleaning before validation
try {
    $userInput = '121282-12345';
    $cleanCode = Latvian::cleanPersonCode($userInput);
    $isValid = Latvian::validatePersonCode($cleanCode);
    echo "The code {$userInput} is " . ($isValid ? 'valid' : 'invalid') . " after cleaning.\n";
    // Output: The code 121282-12345 is valid after cleaning.
} catch (\InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

### validatePersonCodeNew()

```php
public function validatePersonCodeNew(
    string $personCode,
): bool
```

Validates a new format Latvian personal identification code (starting with "32").

Parameters:
- `$personCode`: The personal identification code to validate

Returns a boolean indicating whether the code is valid.

Example:
```php
use Valksor\Functions\Latvian;

// Note: These are fictional examples, not real personal codes

// Valid new format personal code (starting with "32")
$validCode1 = '32123456789';
$isValid1 = Latvian::validatePersonCodeNew($validCode1);
echo "Code {$validCode1} is " . ($isValid1 ? 'valid' : 'invalid') . "\n";
// Output: Code 32123456789 is valid

// Invalid new format personal code (incorrect checksum)
$invalidCode1 = '32123456780';
$isValid2 = Latvian::validatePersonCodeNew($invalidCode1);
echo "Code {$invalidCode1} is " . ($isValid2 ? 'valid' : 'invalid') . "\n";
// Output: Code 32123456780 is invalid

// Invalid new format personal code (doesn't start with "32")
$invalidCode2 = '12345678901';
$isValid3 = Latvian::validatePersonCodeNew($invalidCode2);
echo "Code {$invalidCode2} is " . ($isValid3 ? 'valid' : 'invalid') . "\n";
// Output: Code 12345678901 is invalid

// Invalid new format personal code (wrong length)
$invalidCode3 = '321234567';
$isValid4 = Latvian::validatePersonCodeNew($invalidCode3);
echo "Code {$invalidCode3} is " . ($isValid4 ? 'valid' : 'invalid') . "\n";
// Output: Code 321234567 is invalid

// Practical use case: Validating user input
function processNewFormatCode($code) {
    if (Latvian::validatePersonCodeNew($code)) {
        echo "The new format code {$code} is valid and can be processed.\n";
        // Process the code...
    } else {
        echo "The new format code {$code} is invalid. Please check and try again.\n";
    }
}

processNewFormatCode('32123456789');
// Output: The new format code 32123456789 is valid and can be processed.

processNewFormatCode('12345678901');
// Output: The new format code 12345678901 is invalid. Please check and try again.
```

### validatePersonCodeOld()

```php
public function validatePersonCodeOld(
    string $personCode,
): bool
```

Validates an old format Latvian personal identification code.

Parameters:
- `$personCode`: The personal identification code to validate

Returns a boolean indicating whether the code is valid.

Example:
```php
use Valksor\Functions\Latvian;

// Note: These are fictional examples, not real personal codes

// Valid old format personal code (format: DDMMYY-XXXXX)
$validCode1 = '121282-12345';
$isValid1 = Latvian::validatePersonCodeOld($validCode1);
echo "Code {$validCode1} is " . ($isValid1 ? 'valid' : 'invalid') . "\n";
// Output: Code 121282-12345 is valid

// Valid old format personal code without hyphen
$validCode2 = '12128212345';
$isValid2 = Latvian::validatePersonCodeOld($validCode2);
echo "Code {$validCode2} is " . ($isValid2 ? 'valid' : 'invalid') . "\n";
// Output: Code 12128212345 is valid

// Invalid old format personal code (invalid date - day 32)
$invalidCode1 = '321282-12345';
$isValid3 = Latvian::validatePersonCodeOld($invalidCode1);
echo "Code {$invalidCode1} is " . ($isValid3 ? 'valid' : 'invalid') . "\n";
// Output: Code 321282-12345 is invalid

// Invalid old format personal code (invalid date - month 13)
$invalidCode2 = '121382-12345';
$isValid4 = Latvian::validatePersonCodeOld($invalidCode2);
echo "Code {$invalidCode2} is " . ($isValid4 ? 'valid' : 'invalid') . "\n";
// Output: Code 121382-12345 is invalid

// Invalid old format personal code (incorrect checksum)
$invalidCode3 = '121282-12346';
$isValid5 = Latvian::validatePersonCodeOld($invalidCode3);
echo "Code {$invalidCode3} is " . ($isValid5 ? 'valid' : 'invalid') . "\n";
// Output: Code 121282-12346 is invalid

// Invalid old format personal code (wrong length)
$invalidCode4 = '121282-123';
$isValid6 = Latvian::validatePersonCodeOld($invalidCode4);
echo "Code {$invalidCode4} is " . ($isValid6 ? 'valid' : 'invalid') . "\n";
// Output: Code 121282-123 is invalid

// Practical use case: Form validation
function validateUserInput($code) {
    // First clean the code to remove any unwanted characters
    try {
        $cleanCode = Latvian::cleanPersonCode($code);

        // Then validate it as an old format code
        if (Latvian::validatePersonCodeOld($cleanCode)) {
            echo "The old format code {$code} is valid.\n";
            return true;
        } else {
            echo "The old format code {$code} is invalid.\n";
            return false;
        }
    } catch (\InvalidArgumentException $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return false;
    }
}

validateUserInput('121282-12345'); // Valid
// Output: The old format code 121282-12345 is valid.

validateUserInput('121282 - 12345'); // Valid after cleaning
// Output: The old format code 121282 - 12345 is valid.

validateUserInput('121382-12345'); // Invalid date
// Output: The old format code 121382-12345 is invalid.
```
