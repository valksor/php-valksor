# Valksor Functions: Date - Features

This document lists all the functions available in the Valksor Functions: Date package.

## Date Creation and Manipulation

### date()

```php
public function date(
    ?string $dateString = null,
    ?string $format = null,
): DateTimeInterface
```

Creates a DateTimeInterface object from a date string and format.

Parameters:
- `$dateString`: The date string to parse
- `$format`: The format of the date string

Returns a DateTimeInterface object. Throws InvalidArgumentException if the date string is invalid.

Example:
```php
use Valksor\Functions\Date;

// Create a DateTime object from a string with a specific format
$dateTime = Date::date('2023-12-31', 'Y-m-d');
echo $dateTime->format('F j, Y'); // Output: December 31, 2023

// Create a DateTime object with the current date and time
$now = Date::date();
echo $now->format('Y-m-d H:i:s'); // Output: Current date and time
```

### dateNullable()

```php
public function dateNullable(
    ?string $dateString = null,
    ?string $format = null,
): ?DateTimeInterface
```

Creates a DateTimeInterface object from a date string and format, or returns null if invalid.

Parameters:
- `$dateString`: The date string to parse
- `$format`: The format of the date string

Returns a DateTimeInterface object or null if the date string is invalid.

Example:
```php
use Valksor\Functions\Date;

// Valid date string
$dateTime = Date::dateNullable('2023-12-31', 'Y-m-d');
if ($dateTime) {
    echo $dateTime->format('F j, Y'); // Output: December 31, 2023
}

// Invalid date string
$invalidDate = Date::dateNullable('2023-13-45', 'Y-m-d'); // Returns null
if ($invalidDate === null) {
    echo "Invalid date provided";
}
```

### dateWithoutFormat()

```php
public function dateWithoutFormat(
    string $date,
    array $guesses = [],
): DateTimeInterface|string
```

Attempts to create a DateTimeInterface object from a date string without specifying a format.

Parameters:
- `$date`: The date string to parse
- `$guesses`: Additional format guesses to try

Returns a DateTimeInterface object if successful, or the original date string if not.

Example:
```php
use Valksor\Functions\Date;

// Common date format that can be automatically detected
$result1 = Date::dateWithoutFormat('2023-12-31');
if ($result1 instanceof \DateTimeInterface) {
    echo $result1->format('Y-m-d'); // Output: 2023-12-31
}

// Using custom format guesses for uncommon date format
$result2 = Date::dateWithoutFormat('31.12.2023', ['d.m.Y']);
if ($result2 instanceof \DateTimeInterface) {
    echo $result2->format('Y-m-d'); // Output: 2023-12-31
}

// Date format that cannot be parsed
$result3 = Date::dateWithoutFormat('some-invalid-date');
if (is_string($result3)) {
    echo "Could not parse: " . $result3;
}
```

## Date Formatting

### excelDate()

```php
public function excelDate(
    int $timestamp,
    string $format = Functions::FORMAT,
): string
```

Converts an Excel date (serial number) to a formatted date string.

Parameters:
- `$timestamp`: The Excel date serial number
- `$format`: The format for the output date string

Returns a formatted date string.

Example:
```php
use Valksor\Functions\Date;

// Convert Excel date serial number to a formatted date string
// Excel date 44561 corresponds to January 1, 2022
$date1 = Date::excelDate(44561);
echo $date1; // Output: 2022-01-01

// Using a custom format
$date2 = Date::excelDate(44561, 'd.m.Y');
echo $date2; // Output: 01.01.2022

// Excel date 44926 corresponds to January 1, 2023
$date3 = Date::excelDate(44926, 'F j, Y');
echo $date3; // Output: January 1, 2023
```

### formatDate()

```php
public function formatDate(
    string $string,
    string $format = Functions::FORMAT,
): string|bool
```

Formats a date string according to the specified format.

Parameters:
- `$string`: The date string to format
- `$format`: The format of the input date string

Returns a formatted date string or false if the date is invalid.

Example:
```php
use Valksor\Functions\Date;

// Format a date string using the default format
$formattedDate1 = Date::formatDate('2023-12-31');
if ($formattedDate1 !== false) {
    echo $formattedDate1; // Output: 2023-12-31
}

// Format a date string using a custom format
$formattedDate2 = Date::formatDate('31/12/2023', 'd/m/Y');
if ($formattedDate2 !== false) {
    echo $formattedDate2; // Output: 2023-12-31 (in default format)
}

// Handling an invalid date
$invalidDate = Date::formatDate('invalid-date');
if ($invalidDate === false) {
    echo "Invalid date format provided";
}
```

### fromUnixTimestamp()

```php
public function fromUnixTimestamp(
    int $timestamp = 0,
    ?string $format = null,
): string
```

Converts a Unix timestamp to a formatted date string.

Parameters:
- `$timestamp`: The Unix timestamp
- `$format`: The format for the output date string

Returns a formatted date string.

Example:
```php
use Valksor\Functions\Date;

// Convert a specific Unix timestamp to a date string using default format
// Unix timestamp 1672531200 corresponds to January 1, 2023
$date1 = Date::fromUnixTimestamp(1672531200);
echo $date1; // Output: 2023-01-01

// Using a custom format
$date2 = Date::fromUnixTimestamp(1672531200, 'F j, Y');
echo $date2; // Output: January 1, 2023

// Using current timestamp (0 defaults to current time)
$currentDate = Date::fromUnixTimestamp(0, 'd.m.Y H:i:s');
echo $currentDate; // Output: Current date and time in the specified format
```

## Time Formatting

### format()

```php
public function format(
    int|float $timestamp,
    bool $asArray = false,
): array|string
```

Formats a time duration in milliseconds into a human-readable string or array.

Parameters:
- `$timestamp`: The time duration in seconds
- `$asArray`: Whether to return the result as an array

Returns a formatted time string (e.g., "1 hour 30 minutes") or an array of time units.

Example:
```php
use Valksor\Functions\Date;

// Format a duration as a human-readable string
// 3665 seconds = 1 hour, 1 minute, 5 seconds
$duration1 = Date::format(3665);
echo $duration1; // Output: "1 hour 1 minute 5 seconds"

// Format a duration as an array
$duration2 = Date::format(3665, true);
print_r($duration2);
// Output: Array (
//    [hours] => 1
//    [minutes] => 1
//    [seconds] => 5
// )

// Format a shorter duration
$duration3 = Date::format(65);
echo $duration3; // Output: "1 minute 5 seconds"
```

## Date Validation

### validateDate()

```php
public function validateDate(
    string $date,
): bool
```

Validates a date string in the format "DDMMYYYY".

Parameters:
- `$date`: The date string to validate

Returns a boolean indicating whether the date is valid.

Example:
```php
use Valksor\Functions\Date;

// Valid date in DDMMYYYY format
$isValid1 = Date::validateDate('31122023');
echo $isValid1 ? 'Valid date' : 'Invalid date'; // Output: Valid date

// Invalid date (February 30th doesn't exist)
$isValid2 = Date::validateDate('30022023');
echo $isValid2 ? 'Valid date' : 'Invalid date'; // Output: Invalid date

// Invalid date (wrong format)
$isValid3 = Date::validateDate('2023-12-31');
echo $isValid3 ? 'Valid date' : 'Invalid date'; // Output: Invalid date

// Invalid date (not a date)
$isValid4 = Date::validateDate('abcdefgh');
echo $isValid4 ? 'Valid date' : 'Invalid date'; // Output: Invalid date
```

### validateDateBasic()

```php
public function validateDateBasic(
    mixed $date,
    string $format = Functions::FORMAT,
): bool
```

Validates a date string against a specified format.

Parameters:
- `$date`: The date string to validate
- `$format`: The format to validate against

Returns a boolean indicating whether the date is valid according to the format.

Example:
```php
use Valksor\Functions\Date;

// Valid date with default format (Y-m-d)
$isValid1 = Date::validateDateBasic('2023-12-31');
echo $isValid1 ? 'Valid date' : 'Invalid date'; // Output: Valid date

// Valid date with custom format
$isValid2 = Date::validateDateBasic('31/12/2023', 'd/m/Y');
echo $isValid2 ? 'Valid date' : 'Invalid date'; // Output: Valid date

// Invalid date (wrong format)
$isValid3 = Date::validateDateBasic('2023/12/31', 'd/m/Y');
echo $isValid3 ? 'Valid date' : 'Invalid date'; // Output: Invalid date

// Invalid date (not a date)
$isValid4 = Date::validateDateBasic('not-a-date');
echo $isValid4 ? 'Valid date' : 'Invalid date'; // Output: Invalid date

// Invalid date (out of range)
$isValid5 = Date::validateDateBasic('2023-13-45');
echo $isValid5 ? 'Valid date' : 'Invalid date'; // Output: Invalid date
```
