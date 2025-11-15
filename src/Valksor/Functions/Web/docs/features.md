# Valksor Functions: Web - Features

This document lists all the functions available in the Valksor Functions: Web package.

## IP Address Functions

### validateIPAddress

```php
public function validateIPAddress(
    string $ipAddress,
    bool $deny = true,
): bool
```

Validates an IP address and optionally denies private and reserved ranges.

Parameters:

- `$ipAddress`: The IP address to validate
- `$deny`: Whether to deny private and reserved ranges (default: true)

Returns a boolean indicating whether the IP address is valid.

### validateCIDR

```php
public function validateCIDR(
    string $cidr,
): bool
```

Validates a CIDR notation IP address.

Parameters:

- `$cidr`: The CIDR notation IP address to validate

Returns a boolean indicating whether the CIDR notation is valid.

### isCIDR

```php
public function isCIDR(
    string $cidr,
): bool
```

Checks if a string is a valid CIDR notation.

Parameters:

- `$cidr`: The string to check

Returns a boolean indicating whether the string is a valid CIDR notation.

### cidrRange

```php
public function cidrRange(
    string $cidr,
): array
```

Gets the IP range from a CIDR notation.

Parameters:

- `$cidr`: The CIDR notation

Returns an array with the start and end IP addresses of the range.

### remoteIp

```php
public function remoteIp(
    Request $request,
    bool $trust = false,
): string
```

Gets the remote IP address from a request.

Parameters:

- `$request`: The Symfony HttpFoundation Request object
- `$trust`: Whether to trust proxy headers (default: false)

Returns the remote IP address as a string.

### remoteIpCF

```php
public function remoteIpCF(
    Request $request,
): string
```

Gets the remote IP address from a request using Cloudflare headers.

Parameters:

- `$request`: The Symfony HttpFoundation Request object

Returns the remote IP address as a string.

## Email Functions

### validateEmail

```php
public function validateEmail(
    string $email,
): bool
```

Validates an email address.

Parameters:

- `$email`: The email address to validate

Returns a boolean indicating whether the email address is valid.

## URL Functions

### urlEncode

```php
public function urlEncode(
    string $url,
): string
```

Encodes a URL by parsing it and rebuilding it with proper encoding.

Parameters:

- `$url`: The URL to encode

Returns the encoded URL.

### isAbsolute

```php
public function isAbsolute(
    string $url,
): bool
```

Checks if a URL is absolute (starts with http:// or https://).

Parameters:

- `$url`: The URL to check

Returns a boolean indicating whether the URL is absolute.

### isUrl

```php
public function isUrl(
    string $url,
): bool
```

Checks if a string is a valid URL.

Parameters:

- `$url`: The string to check

Returns a boolean indicating whether the string is a valid URL.

### schema

```php
public function schema(
    bool $https = true,
): string
```

Returns the URL schema (http:// or https://).

Parameters:

- `$https`: Whether to return https:// (true) or http:// (false)

Returns the URL schema as a string.

## HTTP Functions

### isHttps

```php
public function isHttps(
    Request $request,
): bool
```

Checks if a request is using HTTPS by examining various headers and indicators.

Parameters:

- `$request`: The Symfony HttpFoundation Request object

Returns a boolean indicating whether the request is using HTTPS.

### checkHttps

```php
public function checkHttps(
    Request $request,
): bool
```

Checks if the HTTPS header is set in the request.

Parameters:

- `$request`: The Symfony HttpFoundation Request object

Returns a boolean indicating whether the HTTPS header is set.

### checkHttpXForwardedProto

```php
public function checkHttpXForwardedProto(
    Request $request,
): bool
```

Checks if the X-Forwarded-Proto header is set to HTTPS in the request.

Parameters:

- `$request`: The Symfony HttpFoundation Request object

Returns a boolean indicating whether the X-Forwarded-Proto header is set to HTTPS.

### checkHttpXForwardedSsl

```php
public function checkHttpXForwardedSsl(
    Request $request,
): bool
```

Checks if the X-Forwarded-SSL header is set to on in the request.

Parameters:

- `$request`: The Symfony HttpFoundation Request object

Returns a boolean indicating whether the X-Forwarded-SSL header is set to on.

### checkServerPort

```php
public function checkServerPort(
    Request $request,
): bool
```

Checks if the server port is the HTTPS port (443).

Parameters:

- `$request`: The Symfony HttpFoundation Request object

Returns a boolean indicating whether the server port is the HTTPS port.

### buildHttpQueryString

```php
public function buildHttpQueryString(
    array $data,
): string
```

Builds an HTTP query string from an array of data.

Parameters:

- `$data`: The array of data to convert to a query string

Returns the HTTP query string.

### buildHttpQueryArray

```php
public function buildHttpQueryArray(
    array $data,
): array
```

Builds an array suitable for HTTP query string from a nested array.

Parameters:

- `$data`: The nested array to convert

Returns an array suitable for HTTP query string.

### arrayFromQueryString

```php
public function arrayFromQueryString(
    string $queryString,
): array
```

Converts an HTTP query string to an array.

Parameters:

- `$queryString`: The HTTP query string to convert

Returns an array of query parameters.

## Browser Functions

### isIE

```php
public function isIE(
    Request $request,
): bool
```

Checks if the request is from Internet Explorer.

Parameters:

- `$request`: The Symfony HttpFoundation Request object

Returns a boolean indicating whether the request is from Internet Explorer.

## Request Functions

### requestIdentity

```php
public function requestIdentity(
    Request $request,
): string
```

Generates a unique identity for a request based on its properties.

Parameters:

- `$request`: The Symfony HttpFoundation Request object

Returns a unique identity string for the request.

### requestMethods

```php
public function requestMethods(
    bool $safe = false,
): array
```

Returns an array of HTTP request methods.

Parameters:

- `$safe`: Whether to return only safe methods (GET, HEAD)

Returns an array of HTTP request methods.

### parseHeaders

```php
public function parseHeaders(
    string $headers,
): array
```

Parses HTTP headers from a string.

Parameters:

- `$headers`: The HTTP headers string

Returns an array of parsed headers.

### rawHeaders

```php
public function rawHeaders(
): array
```

Gets the raw HTTP headers from the current request.

Returns an array of raw HTTP headers.

## Miscellaneous Functions

### buildArrayFromObject

```php
public function buildArrayFromObject(
    object $object,
): array
```

Converts an object to an array.

Parameters:

- `$object`: The object to convert

Returns an array representation of the object.

### result

```php
public function result(
    mixed $data,
    int $status = 200,
    array $headers = [],
): Response
```

Creates a Response object with the given data, status, and headers.

Parameters:

- `$data`: The data to include in the response
- `$status`: The HTTP status code (default: 200)
- `$headers`: Additional HTTP headers

Returns a Response object.

### routeExists

```php
public function routeExists(
    string $name,
    RouterInterface $router,
): bool
```

Checks if a route exists in the router.

Parameters:

- `$name`: The route name
- `$router`: The Symfony RouterInterface

Returns a boolean indicating whether the route exists.

### latestReleaseTag

```php
public function latestReleaseTag(
    string $repository,
    string $default = 'latest',
): string
```

Gets the latest release tag from a GitHub repository.

Parameters:

- `$repository`: The GitHub repository (e.g., "valksor/php-valksor")
- `$default`: The default value to return if no release is found

Returns the latest release tag or the default value.
