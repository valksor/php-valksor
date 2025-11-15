# Valksor Functions: Text - Features

This document lists all the functions available in the Valksor Functions: Text package.

## Case Conversion

### camelCase()

```php
public function camelCase(
    string $string,
): string
```

Converts a string to camelCase format.

Parameters:

- `$string`: The input string to convert

Returns the string in camelCase format (first word lowercase, subsequent words capitalized with no spaces or underscores).

### pascalCase()

```php
public function pascalCase(
    string $string,
): string
```

Converts a string to PascalCase format.

Parameters:

- `$string`: The input string to convert

Returns the string in PascalCase format (all words capitalized with no spaces or underscores).

### snakeCaseFromCamelCase()

```php
public function snakeCaseFromCamelCase(
    string $string,
): string
```

Converts a camelCase string to snake_case format.

Parameters:

- `$string`: The camelCase string to convert

Returns the string in snake_case format.

### snakeCaseFromSentence()

```php
public function snakeCaseFromSentence(
    string $string,
): string
```

Converts a sentence to snake_case format.

Parameters:

- `$string`: The sentence to convert

Returns the string in snake_case format.

## String Generation

### randomString()

```php
public function randomString(
    int $length = 32,
    string $chars = Functions::BASIC,
): string
```

Generates a random string of the specified length using the given character set.

Parameters:

- `$length`: The length of the random string (default: 32)
- `$chars`: The character set to use (default: lowercase and uppercase English letters plus digits)

Returns a random string of the specified length.

### uniqueId()

```php
public function uniqueId(
    string $prefix = '',
): string
```

Generates a unique identifier with an optional prefix.

Parameters:

- `$prefix`: Optional prefix for the unique ID

Returns a unique identifier string.

## Text Sanitization

### sanitize()

```php
public function sanitize(
    string $text,
): string
```

Sanitizes text by removing HTML tags and converting quotes to HTML entities.

Parameters:

- `$text`: The text to sanitize

Returns the sanitized text.

### sanitizeFloat()

```php
public function sanitizeFloat(
    string $float,
): string
```

Sanitizes a float string by ensuring it uses the correct decimal separator.

Parameters:

- `$float`: The float string to sanitize

Returns the sanitized float string.

### cleanText()

```php
public function cleanText(
    string $text,
): string
```

Cleans text by removing unwanted characters and normalizing whitespace.

Parameters:

- `$text`: The text to clean

Returns the cleaned text.

### keepNumeric()

```php
public function keepNumeric(
    string $string,
): string
```

Removes all non-numeric characters from a string.

Parameters:

- `$string`: The input string

Returns a string containing only numeric characters.

### stripSpace()

```php
public function stripSpace(
    string $string,
): string
```

Removes all whitespace characters from a string.

Parameters:

- `$string`: The input string

Returns the string with all whitespace removed.

### oneSpace()

```php
public function oneSpace(
    string $string,
): string
```

Replaces multiple consecutive spaces with a single space.

Parameters:

- `$string`: The input string

Returns the string with normalized spaces.

## Character Conversion

### cyrillicToLatin()

```php
public function cyrillicToLatin(
    string $text,
): string
```

Converts Cyrillic characters to their Latin equivalents.

Parameters:

- `$text`: The text with Cyrillic characters

Returns the text with Cyrillic characters converted to Latin.

### latinToCyrillic()

```php
public function latinToCyrillic(
    string $text,
): string
```

Converts Latin characters to their Cyrillic equivalents.

Parameters:

- `$text`: The text with Latin characters

Returns the text with Latin characters converted to Cyrillic.

### htmlEntityDecode()

```php
public function htmlEntityDecode(
    string $string,
): string
```

Decodes HTML entities in a string.

Parameters:

- `$string`: The string with HTML entities

Returns the decoded string.

### br2nl()

```php
public function br2nl(
    string $string,
): string
```

Converts HTML line breaks to newlines.

Parameters:

- `$string`: The string with HTML line breaks

Returns the string with HTML line breaks converted to newlines.

### nl2br()

```php
public function nl2br(
    string $string,
): string
```

Converts newlines to HTML line breaks.

Parameters:

- `$string`: The string with newlines

Returns the string with newlines converted to HTML line breaks.

## Text Manipulation

### limitChars()

```php
public function limitChars(
    string $text,
    int $limit = 100,
    string $append = '...',
): string
```

Limits the number of characters in a text and appends a suffix if truncated.

Parameters:

- `$text`: The text to limit
- `$limit`: The maximum number of characters (default: 100)
- `$append`: The string to append if truncated (default: '...')

Returns the limited text.

### limitWords()

```php
public function limitWords(
    string $text,
    int $limit = 100,
    string $append = '...',
): string
```

Limits the number of words in a text and appends a suffix if truncated.

Parameters:

- `$text`: The text to limit
- `$limit`: The maximum number of words (default: 100)
- `$append`: The string to append if truncated (default: '...')

Returns the limited text.

### truncateSafe()

```php
public function truncateSafe(
    string $string,
    int $length,
    string $append = '...',
): string
```

Safely truncates a string to a specified length without cutting words.

Parameters:

- `$string`: The string to truncate
- `$length`: The maximum length
- `$append`: The string to append if truncated (default: '...')

Returns the safely truncated string.

### reverseUTF8()

```php
public function reverseUTF8(
    string $string,
): string
```

Reverses a UTF-8 encoded string.

Parameters:

- `$string`: The UTF-8 string to reverse

Returns the reversed string.

### shuffle()

```php
public function shuffle(
    string $string,
): string
```

Shuffles the characters in a string.

Parameters:

- `$string`: The string to shuffle

Returns the shuffled string.

### lastPart()

```php
public function lastPart(
    string $string,
    string $delimiter,
): string
```

Gets the last part of a string after the last occurrence of a delimiter.

Parameters:

- `$string`: The input string
- `$delimiter`: The delimiter character

Returns the last part of the string.

## String Analysis

### contains()

```php
public function contains(
    string $haystack,
    string $needle,
): bool
```

Checks if a string contains a substring.

Parameters:

- `$haystack`: The string to search in
- `$needle`: The substring to search for

Returns a boolean indicating whether the substring was found.

### containsAny()

```php
public function containsAny(
    string $haystack,
    array $needles,
): bool
```

Checks if a string contains any of the given substrings.

Parameters:

- `$haystack`: The string to search in
- `$needles`: An array of substrings to search for

Returns a boolean indicating whether any of the substrings were found.

### strStartsWithAny()

```php
public function strStartsWithAny(
    string $haystack,
    array $needles,
): bool
```

Checks if a string starts with any of the given substrings.

Parameters:

- `$haystack`: The string to check
- `$needles`: An array of possible prefixes to check for

Returns a boolean indicating whether the string starts with any of the given prefixes.

### compare()

```php
public function compare(
    string $first,
    string $second,
): bool
```

Compares two strings for equality (case-insensitive).

Parameters:

- `$first`: The first string
- `$second`: The second string

Returns a boolean indicating whether the strings are equal.

### longestSubstrLength()

```php
public function longestSubstrLength(
    string $first,
    string $second,
): int
```

Finds the length of the longest common substring between two strings.

Parameters:

- `$first`: The first string
- `$second`: The second string

Returns the length of the longest common substring.

### isHex()

```php
public function isHex(
    string $string,
): bool
```

Checks if a string is a valid hexadecimal value.

Parameters:

- `$string`: The string to check

Returns a boolean indicating whether the string is a valid hexadecimal value.

## Conversion

### toString()

```php
public function toString(
    mixed $value,
): string
```

Converts a value to a string representation.

Parameters:

- `$value`: The value to convert

Returns the string representation of the value.

### scalarToString()

```php
public function scalarToString(
    mixed $value,
): string
```

Converts a scalar value to a string.

Parameters:

- `$value`: The scalar value to convert

Returns the string representation of the scalar value.

### normalizedValue()

```php
public function normalizedValue(
    mixed $value,
): string
```

Normalizes a value to a standardized string representation.

Parameters:

- `$value`: The value to normalize

Returns the normalized string representation.

## Miscellaneous

### pluralize()

```php
public function pluralize(
    string $singular,
): string
```

Converts a singular English word to its plural form.

Parameters:

- `$singular`: The singular word

Returns the plural form of the word.

### singularize()

```php
public function singularize(
    string $plural,
): string
```

Converts a plural English word to its singular form.

Parameters:

- `$plural`: The plural word

Returns the singular form of the word.

### countryName()

```php
public function countryName(
    string $code,
): string
```

Gets the country name from a country code.

Parameters:

- `$code`: The country code (ISO 3166-1 alpha-2)

Returns the country name.

### sha()

```php
public function sha(
    string $string,
    int $length = 40,
): string
```

Generates a SHA hash of a string.

Parameters:

- `$string`: The string to hash
- `$length`: The length of the hash (default: 40)

Returns the SHA hash.
