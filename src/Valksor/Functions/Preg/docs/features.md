# Valksor Functions: Preg - Features

This document lists all the functions available in the Valksor Functions: Preg package.

## Regular Expression Functions

### match()

```php
public function match(
    string $pattern,
    string $subject,
    ?array &$matches = null,
    int $flags = 0,
    int $offset = 0,
): bool
```

Enhanced version of PHP's preg_match function.

Parameters:

- `$pattern`: The regular expression pattern
- `$subject`: The input string
- `$matches`: If provided, will be filled with the results of search
- `$flags`: Flags for the preg_match function
- `$offset`: Offset in the subject string

Returns a boolean indicating whether the pattern matches the subject.

Example:

```php
use Valksor\Functions\Preg;

// Basic usage - check if a pattern matches a subject
$emailPattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
$validEmail = 'user@example.com';
$invalidEmail = 'not-an-email';

$isValidEmail = Preg::match($emailPattern, $validEmail);
echo "Is 'user@example.com' a valid email? " . ($isValidEmail ? 'Yes' : 'No') . "\n";
// Output: Is 'user@example.com' a valid email? Yes

$isInvalidEmail = Preg::match($emailPattern, $invalidEmail);
echo "Is 'not-an-email' a valid email? " . ($isInvalidEmail ? 'Yes' : 'No') . "\n";
// Output: Is 'not-an-email' a valid email? No

// Capturing matches into the $matches parameter
$pattern = '/(\w+)@(\w+)\.(\w+)/';
$email = 'john.doe@example.com';
$matches = [];

$result = Preg::match($pattern, $email, $matches);
echo "Match result: " . ($result ? 'Found' : 'Not found') . "\n";

if ($result) {
    echo "Full match: " . $matches[0] . "\n";
    echo "Username: " . $matches[1] . "\n";
    echo "Domain: " . $matches[2] . "\n";
    echo "TLD: " . $matches[3] . "\n";
}
// Output:
// Match result: Found
// Full match: john.doe@example.com
// Username: john
// Domain: example
// TLD: com

// Using the offset parameter to start matching from a specific position
$text = "First email: user1@example.com, Second email: user2@example.com";
$emailMatches = [];

// Match the first email
$firstMatch = Preg::match($emailPattern, $text, $emailMatches);
echo "First match: " . ($firstMatch ? $emailMatches[0] : 'None') . "\n";
// Output: First match: None (because the pattern requires the entire string to be an email)

// Using a different pattern that doesn't require the entire string to match
$emailPattern2 = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
$firstMatch2 = Preg::match($emailPattern2, $text, $emailMatches);
echo "First match with new pattern: " . ($firstMatch2 ? $emailMatches[0] : 'None') . "\n";
// Output: First match with new pattern: user1@example.com

// Match the second email using offset
$secondMatch = Preg::match($emailPattern2, $text, $emailMatches, 0, strpos($text, 'Second'));
echo "Second match with offset: " . ($secondMatch ? $emailMatches[0] : 'None') . "\n";
// Output: Second match with offset: user2@example.com

// Using flags - PREG_OFFSET_CAPTURE to get positions of matches
$patternWithGroups = '/(\d{3})-(\d{3})-(\d{4})/';
$phoneNumber = "Contact us at 123-456-7890 or 987-654-3210";
$phoneMatches = [];

$matchWithPositions = Preg::match($patternWithGroups, $phoneNumber, $phoneMatches, PREG_OFFSET_CAPTURE);
if ($matchWithPositions) {
    echo "Phone number match with positions:\n";
    foreach ($phoneMatches as $index => $match) {
        echo "Group {$index}: '{$match[0]}' at position {$match[1]}\n";
    }
}
// Output:
// Phone number match with positions:
// Group 0: '123-456-7890' at position 13
// Group 1: '123' at position 13
// Group 2: '456' at position 17
// Group 3: '7890' at position 21

// Practical use case: Validating input formats
function validateInput($input, $type)
{
    $patterns = [
        'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        'phone' => '/^\d{3}-\d{3}-\d{4}$/',
        'zipcode' => '/^\d{5}(-\d{4})?$/',
        'date' => '/^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/\d{4}$/',
        'username' => '/^[a-zA-Z0-9_]{3,20}$/',
    ];

    if (!isset($patterns[$type])) {
        throw new InvalidArgumentException("Unknown validation type: {$type}");
    }

    return Preg::match($patterns[$type], $input);
}

// Validate different types of input
$inputs = [
    'email' => 'user@example.com',
    'phone' => '123-456-7890',
    'zipcode' => '12345-6789',
    'date' => '12/31/2023',
    'username' => 'john_doe123',
];

foreach ($inputs as $type => $value) {
    $isValid = validateInput($value, $type);
    echo "Is '{$value}' a valid {$type}? " . ($isValid ? 'Yes' : 'No') . "\n";
}
// Output:
// Is 'user@example.com' a valid email? Yes
// Is '123-456-7890' a valid phone? Yes
// Is '12345-6789' a valid zipcode? Yes
// Is '12/31/2023' a valid date? Yes
// Is 'john_doe123' a valid username? Yes

// Practical use case: Extracting information from text
function extractInformation($text)
{
    $info = [];

    // Extract email addresses
    $emailPattern = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/';
    $matches = [];
    if (Preg::match($emailPattern, $text, $matches)) {
        $info['email'] = $matches[0];
    }

    // Extract phone numbers
    $phonePattern = '/\b(\d{3}[-.]?\d{3}[-.]?\d{4})\b/';
    $matches = [];
    if (Preg::match($phonePattern, $text, $matches)) {
        $info['phone'] = $matches[1];
    }

    // Extract URLs
    $urlPattern = '/(https?:\/\/[^\s]+)/';
    $matches = [];
    if (Preg::match($urlPattern, $text, $matches)) {
        $info['url'] = $matches[1];
    }

    return $info;
}

// Extract information from a text
$text = "Contact John Doe at john.doe@example.com or call 555-123-4567. Visit our website at https://example.com for more information.";
$extractedInfo = extractInformation($text);

echo "\nExtracted Information:\n";
foreach ($extractedInfo as $key => $value) {
    echo "{$key}: {$value}\n";
}
// Output:
// Extracted Information:
// email: john.doe@example.com
// phone: 555-123-4567
// url: https://example.com
```

### matchAll()

```php
public function matchAll(
    string $pattern,
    string $subject,
    array &$matches,
    int $flags = 0,
    int $offset = 0,
): int
```

Enhanced version of PHP's preg_match_all function.

Parameters:

- `$pattern`: The regular expression pattern
- `$subject`: The input string
- `$matches`: Array to be filled with all matches
- `$flags`: Flags for the preg_match_all function
- `$offset`: Offset in the subject string

Returns the number of full pattern matches.

### replace()

```php
public function replace(
    array|string $pattern,
    string $replacement,
    $subject,
    int $limit = -1,
    ?int &$count = null,
): string
```

Enhanced version of PHP's preg_replace function.

Parameters:

- `$pattern`: The pattern or array of patterns to search for
- `$replacement`: The string or array of strings to replace with
- `$subject`: The string or array of strings to search and replace in
- `$limit`: The maximum possible replacements for each pattern
- `$count`: If provided, will be set to the number of replacements performed

Returns the replaced string.

### replaceCallback()

```php
public function replaceCallback(
    array|string $pattern,
    callable $callback,
    $subject,
    int $limit = -1,
    ?int &$count = null,
): string
```

Enhanced version of PHP's preg_replace_callback function.

Parameters:

- `$pattern`: The pattern or array of patterns to search for
- `$callback`: The callback function that will be called for each match
- `$subject`: The string or array of strings to search and replace in
- `$limit`: The maximum possible replacements for each pattern
- `$count`: If provided, will be set to the number of replacements performed

Returns the replaced string.

### split()

```php
public function split(
    string $pattern,
    string $subject,
    int $limit = -1,
    int $flags = 0,
): array
```

Enhanced version of PHP's preg_split function.

Parameters:

- `$pattern`: The pattern to search for
- `$subject`: The input string
- `$limit`: The maximum number of elements to return
- `$flags`: Flags for the preg_split function

Returns an array containing substrings of the subject split by the pattern.
