# Valksor PHP Library

[![codecov](https://codecov.io/gh/valksor/php-valksor/graph/badge.svg?token=76KDE1W8PR)](https://codecov.io/gh/valksor/php-valksor)

A comprehensive PHP library and Symfony bundle that provides a collection of utilities, components, and integrations for modern Symfony applications. This ecosystem includes function libraries, development tools, Doctrine extensions, and real-time communication components.

## Features

- **Utility Functions**: 14+ function libraries covering date manipulation, text processing, web operations, and more
- **Symfony Bundle**: Automatic component discovery and configuration management
- **Doctrine Integration**: Enhanced ORM tools, schema management, and database utilities
- **Real-time Communication**: Server-Sent Events (SSE) component for live updates
- **Development Tools**: SPX profiler integration and build automation tools
- **Modern PHP**: Full PHP 8.4+ compatibility with promoted properties and modern syntax
- **Framework Agnostic**: Individual components work outside Symfony when needed

## Requirements

- **PHP 8.4 or higher**
- **Symfony Framework** (7.2.0 or higher)
- **Doctrine ORM** (4.0 or higher) for database components
- **Various PHP Extensions**: curl, json, random, xmlreader, zip
- **Composer** for dependency management

## Installation

Install the complete Valksor ecosystem:

```bash
composer require valksor/php-valksor
```

This meta-package automatically includes:
- **valksor/php-bundle** - Symfony bundle with auto-discovery
- **valksor/php-functions** - All function libraries
- **valksor/php-sse** - Server-Sent Events component
- **valksor/php-doctrine-tools** - Doctrine enhancements
- **valksor/php-spx-profiler** - Performance profiler

## Basic Usage

### Quick Setup

1. Register the bundle in your Symfony application:

```php
<?php

// config/bundles.php
return [
    // ...
    Valksor\Bundle\ValksorBundle::class => ['all' => true],
    // ...
];
```

2. Enable components with basic configuration:

```yaml
# config/packages/valksor.yaml
valksor:
    # Global bundle configuration
    enabled: true

    # Components are automatically discovered and can be configured individually
    sse:
        enabled: true
        port: 8080
```

### Use Function Libraries

```php
<?php

require_once 'vendor/autoload.php';

use Valksor\Functions\Text\Functions;
use Valksor\Functions\Date\Functions;

// Text manipulation
$text = new Functions();
$camelCase = $text->camelCase('hello_world'); // "helloWorld"

// Date operations
$date = new Functions();
$formatted = $date->formatDate('2023-01-15', 'Y-m-d');
```

### Use SSE Component

```php
<?php

use Valksor\Component\Sse\Service\SseService;

// In your controller
#[Route('/trigger-reload')]
public function triggerReload(SseService $sseService): Response
{
    $sseService->broadcast(['type' => 'reload']);
    return new Response('Reload triggered');
}
```

## Documentation

- **[Complete Documentation](https://github.com/valksor/php-valksor)** - Full API documentation and examples
- **[Functions Documentation](src/Valksor/Functions/README.md)** - All 14 function libraries
- **[Bundle Documentation](src/Valksor/Bundle/README.md)** - Symfony bundle configuration
- **[Component Documentation](src/Valksor/Component/)** - SSE, DoctrineTools, and SpxProfiler

## Advanced Usage

### Component Configuration

```yaml
# config/packages/valksor.yaml
valksor:
    # SSE Configuration
    sse:
        enabled: true
        host: localhost
        port: 8080
        ping_interval: 30
        max_connections: 100

    # Individual components are auto-discovered
    # Configure any component that implements Dependency interface
```

### Function Library Usage

Each function library can be installed individually:

```bash
# Install only specific function libraries
composer require valksor/php-functions-text
composer require valksor/php-functions-date
composer require valksor/php-functions-web
```

```php
<?php

require_once 'vendor/autoload.php';

// Use individual functions
use Valksor\Functions\Text\Functions;
use Valksor\Functions\Date\Functions;
use Valksor\Functions\Web\Functions;

$text = new Functions();
$date = new Functions();
$web = new Functions();

// Or use traits directly
class MyClass
{
    use Valksor\Functions\Text\Traits\_CamelCase;
    use Valksor\Functions\Date\Traits\_FormatDate;
}
```

### Doctrine Integration

Enhanced Doctrine tools for schema management and migrations:

```php
<?php

use Valksor\Component\DoctrineTools\Service\SchemaService;

// Automatic schema synchronization
$schemaService = new SchemaService($entityManager);
$schemaService->syncSchema();
```

## Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) for details on:

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions
- Component development guidelines

To contribute:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add comprehensive tests
5. Ensure all tests pass
6. Submit a pull request

### Development Setup

```bash
# Clone the repository
git clone https://github.com/valksor/php-valksor.git
cd php-valksor

# Install dependencies
composer install

# Run test suite
composer test

# Check code style
composer fix:check
composer fix:run
```

## Security

If you discover any security-related issues, please email us at security@valksor.dev instead of using the issue tracker.

For security policy and vulnerability reporting guidelines, please see our [Security Policy](SECURITY.md).

## Support

- **Documentation**: [Full documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues) for bug reports and feature requests
- **Discussions**: [GitHub Discussions](https://github.com/valksor/php-valksor/discussions) for questions and community support
- **Stack Overflow**: Use tag `valksor-php`
- **Component-specific support**: Each component has dedicated documentation and support channels

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[Symfony Project](https://symfony.com)** - Framework inspiration and many underlying components
- **[Doctrine Project](https://www.doctrine-project.org)** - ORM and database tools inspiration
- **[PHP Community](https://www.php.net)** - Language and ecosystem support

## License

This package is licensed under the [BSD-3-Clause License](LICENSE).
