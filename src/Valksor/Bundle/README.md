# Valksor Bundle

[![Latest Version on Packagist](https://img.shields.io/packagist/v/valksor/php-bundle.svg)](https://packagist.org/packages/valksor/php-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/valksor/php-bundle.svg)](https://packagist.org/packages/valksor/php-bundle)
[![License](https://img.shields.io/packagist/l/valksor/php-bundle.svg)](LICENSE)
[![PHP Version Require](https://img.shields.io/packagist/require-v/valksor/php-bundle/php)](https://packagist.org/packages/valksor/php-bundle)

A comprehensive Symfony bundle that provides automatic component discovery, configuration management, and dependency injection for Valksor components and related packages.

## Features

- **Automatic Component Discovery**: Automatically discovers and registers Valksor components and ValksorDev packages
- **Dynamic Configuration**: Provides flexible configuration system for all components
- **Build System Integration**: Seamless integration with the new 3-command development architecture
- **Service Registry Support**: Manages extensible flag-based service providers for build tools
- **Dependency Management**: Handles component dependencies and enables/disables components based on availability
- **Doctrine Integration**: Supports Doctrine migrations and database schema management
- **Memoization**: Built-in caching and memoization support for improved performance
- **Bundle Architecture**: Follows Symfony best practices for bundle development

## Installation

Install the package via Composer:

```bash
composer require valksor/php-bundle
```

## Requirements

- PHP 8.4 or higher
- Symfony Framework
- Doctrine DBAL (for database-related components)
- Doctrine Migrations (for schema management)

## Usage

### Basic Setup

1. Register the bundle in your Symfony application:

```php
// config/bundles.php
return [
    // ...
    Valksor\Bundle\ValksorBundle::class => ['all' => true],
    // ...
];
```

2. Create a basic configuration file:

```yaml
# config/packages/valksor.yaml
valksor:
    # Global bundle configuration
    enabled: true

    # Component-specific configurations will be automatically discovered
    # Each discovered component can be enabled/disabled individually
```

### Component Discovery

The bundle automatically discovers all available components that implement the `Dependency` interface and end with `Configuration` in their class name. Components are discovered from:

- All namespaces starting with `Valksor\`
- All namespaces starting with `ValksorDev\`

### Configuration Examples

#### Enable/Disable Specific Components

```yaml
# config/packages/valksor.yaml
valksor:
    # Disable a specific component
    some_component:
        enabled: false

    # Configure a specific component
    another_component:
        enabled: true
        option1: value1
        option2: value2
```

#### Component-Specific Configuration

Each component can define its own configuration structure. The bundle automatically merges component configurations with the global Valksor configuration.

```yaml
# Example configuration for a hypothetical cache component
valksor:
    cache_component:
        enabled: true
        ttl: 3600
        storage: redis
        redis:
            host: localhost
            port: 6379
```

### Build System Integration

The Valksor Bundle provides seamless integration with the new Valksor Dev build system architecture, enabling automatic discovery and configuration of build services through the service registry.

#### Build Services Configuration

When the `valksor-dev/php-dev-build` package is installed, the bundle automatically discovers and configures build services:

```yaml
# config/packages/valksor.yaml
valksor:
    build:
        enabled: true
        services:
            # Binary management (always runs first)
            binaries:
                enabled: true
                flags: [init, dev, prod]
                options:
                    download_dir: 'bin/build-tools/'
                    esbuild_version: 'latest'
                    tailwind_version: 'latest'

            # Hot reload with SSE integration
            hot_reload:
                enabled: true
                flags: [dev]
                options:
                    watch_paths: ['templates/', 'src/', 'assets/']
                    exclude_patterns: ['vendor/', 'var/']

            # Tailwind CSS compilation
            tailwind:
                enabled: true
                flags: [dev, prod]
                options:
                    input: 'assets/css/app.css'
                    output: 'public/build/app.css'
                    minify: false

            # Import map management
            importmap:
                enabled: true
                flags: [dev, prod]
                options:
                    importmap_path: 'importmap.json'
                    vendor_dir: 'assets/vendor/'

            # Icon generation
            icons:
                enabled: true
                flags: [prod]
                options:
                    input_dir: 'assets/icons/'
                    output_dir: 'templates/icons/'
```

#### Available Development Commands

The bundle registers the following console commands when build services are enabled:

| Command | Description | Services Run |
|---------|-------------|--------------|
| `valksor:dev` | Lightweight development (SSE + hot reload) | `binaries` + `hot_reload` |
| `valksor:watch` | Full development environment | All services with `dev` flag |
| `valksor-prod:build` | Production asset building | All services with `prod` flag |
| `valksor:hot-reload` | Standalone hot reload service | Hot reload only |
| `valksor:tailwind` | Tailwind CSS compilation | Tailwind only |
| `valksor:importmap` | Import map synchronization | Import map only |
| `valksor:binary` | Binary management | Binary download only |

#### Service Registry Integration

The bundle integrates with the service registry system to provide:

- **Automatic Provider Discovery**: Scans for build service providers
- **Flag-Based Execution**: Services run based on command flags (`init`, `dev`, `prod`)
- **Dependency Resolution**: Handles service dependencies automatically
- **Process Management**: Coordinates multiple build processes
- **Configuration Validation**: Ensures service configurations are valid

#### SSE Component Integration

When both SSE and build components are enabled, the bundle automatically configures:

```yaml
valksor:
    # SSE configuration for development
    sse:
        enabled: true
        port: 8080
        host: localhost
        ping_interval: 30

    # Build system with integrated SSE
    build:
        services:
            hot_reload:
                enabled: true
                flags: [dev]
                options:
                    sse_port: 8080  # Matches SSE configuration
```

This integration ensures that running `valksor:dev` or `valksor:watch` automatically starts the SSE server with hot reload functionality.

### Working with Components

#### Accessing Configuration in Services

You can access component configuration in your services:

```php
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Valksor\Bundle\ValksorBundle;

class YourService
{
    public function __construct(ContainerBuilder $builder)
    {
        // Get global Valksor configuration
        $config = ValksorBundle::getConfig('valksor', $builder);

        // Get specific component parameter
        $enabled = ValksorBundle::p($builder, 'your_component', 'enabled');
    }
}
```

#### Creating Custom Components

To create a custom component that will be discovered by the bundle:

1. Create a configuration class that implements the `Dependency` interface:

```php
<?php

namespace YourNamespace;

use Valksor\Bundle\DependencyInjection\Dependency;

class YourComponentConfiguration implements Dependency
{
    public function build(ContainerBuilder $container): void
    {
        // Build-time logic
    }

    public function addSection(ArrayNodeDefinition $rootNode, callable $wrapper, string $component): void
    {
        // Define configuration schema
    }

    public function registerConfiguration(ContainerConfigurator $container, ContainerBuilder $builder, string $component): void
    {
        // Register services and parameters
    }

    public function registerPreConfiguration(ContainerConfigurator $container, ContainerBuilder $builder, string $component): void
    {
        // Pre-configuration logic
    }

    public function autoDiscover(): bool
    {
        return true; // Set to false to disable auto-discovery
    }

    public function usesDoctrine(): bool
    {
        return false; // Set to true if this component uses Doctrine
    }
}
```

2. The component will be automatically discovered and can be configured under:

```yaml
valksor:
    your_component:
        enabled: true
        # Your component-specific options
```

### Advanced Features

#### Conditional Component Loading

Components can be conditionally loaded based on:

- Package availability (checked via autoloader)
- Explicit enabled/disabled configuration
- Dependency availability

#### Doctrine Integration

If any component reports that it uses Doctrine (`usesDoctrine()` returns `true`), the bundle automatically:

- Registers global migrations
- Sets up Doctrine configuration
- Handles database-related setup

#### Memoization

The bundle includes built-in memoization support to improve performance by caching expensive operations during component discovery and configuration.

## Configuration Reference

### Global Configuration

```yaml
valksor:
    # Enable/disable the entire bundle
    enabled: true

    # Component configurations (auto-discovered)
    component_name:
        enabled: true
        # Component-specific options...
```

### Component Configuration Pattern

Each discovered component follows this pattern:

```yaml
valksor:
    component_id:
        enabled: boolean
        # Component-specific configuration options
```


## Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) for details on:

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to the bundle:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/bundle-enhancement`)
3. Implement your bundle enhancement following existing patterns
4. Add comprehensive tests for new functionality
5. Ensure all tests pass and code style is correct
6. Submit a pull request

### Adding Component Support

When adding support for new components:

1. Ensure component implements `Dependency` interface
2. Create proper configuration schema
3. Add discovery logic if needed
4. Add tests for component integration
5. Update documentation with examples

## Security

If you discover any security-related issues, please email us at security@valksor.dev instead of using the issue tracker.

For security policy and vulnerability reporting guidelines, please see our [Security Policy](SECURITY.md).

## Support

- **Documentation**: [Full documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues) for bug reports and feature requests
- **Discussions**: [GitHub Discussions](https://github.com/valksor/php-valksor/discussions) for questions and community support
- **Stack Overflow**: Use tag `valksor-php-bundle`
- **Symfony Documentation**: [Official Symfony bundle docs](https://symfony.com/doc/current/bundles.html)

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[Symfony Team](https://symfony.com)** - Bundle framework and best practices inspiration
- **[Valksor Project](https://github.com/valksor)** - Part of the larger Valksor PHP ecosystem

## License

This package is licensed under the [BSD-3-Clause License](LICENSE).

## About Valksor

This package is part of the [valksor/php-valksor](https://github.com/valksor/php-valksor) project - a comprehensive PHP library and Symfony bundle that provides a collection of utilities, components, and integrations for Symfony applications.

The main project includes:
- Various utility functions and components
- Doctrine ORM tools and extensions
- API Platform integrations
- Symfony bundle for easy configuration
- And much more

If you find this Bundle component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```