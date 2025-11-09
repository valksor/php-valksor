# Valksor Component: DoctrineTools

[![BSD-3-Clause](https://img.shields.io/badge/BSD--3--Clause-green?style=flat)](https://github.com/valksor/php-doctrine-tools/blob/master/LICENSE)
[![Coverage Status](https://coveralls.io/repos/github/valksor/php-doctrine-tools/badge.svg?branch=master)](https://coveralls.io/github/valksor/php-doctrine-tools?branch=master)

A comprehensive collection of Doctrine tools, traits, and utilities that enhance database operations, provide custom DQL functions, UTC datetime handling, and entity management capabilities for Symfony applications using Doctrine ORM and DBAL.

## Features

- **Custom Entity Traits**: Reusable traits for common entity patterns (UUID, ULID, timestamps, versioning, etc.)
- **UTC DateTime Types**: Custom Doctrine types for consistent UTC datetime handling
- **Custom DQL Functions**: Extended DQL functions for PostgreSQL-specific features
- **Database Extensions**: Automatic registration of PostgreSQL extensions (unaccent, pgcrypto)
- **Migration Tools**: Enhanced migration utilities and version comparison
- **Entity Management**: Simplified entity creation and lifecycle management
- **Event Subscribers**: Doctrine event listeners for schema and migration management

## Installation

Install the package via Composer:

```bash
composer require valksor/php-doctrine-tools
```

## Requirements

- PHP 8.4 or higher
- Doctrine DBAL 4.0 or higher
- Doctrine ORM
- Doctrine Migrations
- Symfony Framework
- Valksor Bundle (for automatic configuration)

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

2. Enable the DoctrineTools component:

```yaml
# config/packages/valksor.yaml
valksor:
    doctrine_tools:
        enabled: true
```

The component will automatically configure Doctrine with all available types, functions, and extensions.

### Entity Traits

#### Complete Entity

Use the `_Entity` trait for a complete entity setup with common fields:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Valksor\Component\DoctrineTools\Doctrine\ORM\Traits\_Entity;

#[ORM\Entity]
class User
{
    use _Entity;

    #[ORM\Column(length: 255)]
    private string $name;

    // Getters and setters...
}
```

This includes:
- ID field (auto-increment integer)
- `isActive` boolean field
- `createdAt` and `updatedAt` datetime fields
- Version field for optimistic locking

#### Individual Traits

Mix and match individual traits based on your needs:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Valksor\Component\DoctrineTools\Doctrine\ORM\Traits\_Uuid;
use Valksor\Component\DoctrineTools\Doctrine\ORM\Traits\_CreatedUpdated;
use Valksor\Component\DoctrineTools\Doctrine\ORM\Traits\_IsActive;

#[ORM\Entity]
class Product
{
    use _Uuid;              // Uses UUID as primary key
    use _CreatedUpdated;    // Adds timestamp fields
    use _IsActive;         // Adds soft delete capability

    #[ORM\Column(length: 255)]
    private string $name;

    // Getters and setters...
}
```

#### Available Traits

**Primary Key Traits:**
- `_Id` - Standard auto-increment integer ID
- `_Uuid` - UUID primary key (string)
- `_Ulid` - ULID primary key (string)
- `__Id` - Abstract ID trait (used internally)
- `__Uuid` - Abstract UUID trait (used internally)
- `__Ulid` - Abstract ULID trait (used internally)
- `__None` - No primary key trait (for entities without defined primary keys)

**Entity Management Traits:**
- `_Entity` - Complete entity with ID, timestamps, version, and active status
- `_SimpleEntity` - Entity with ID field only
- `_CreatedUpdated` - Adds `createdAt` and `updatedAt` timestamp fields
- `_Version` - Adds version field for optimistic locking
- `_IsActive` - Adds `isActive` boolean field for soft deletes

### UTC DateTime Types

The component automatically registers UTC datetime types that handle timezone conversion properly:

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            datetime: Valksor\Component\DoctrineTools\Doctrine\DBAL\Type\UTCDateTimeType
            datetime_immutable: Valksor\Component\DoctrineTools\Doctrine\DBAL\Type\UTCDateTimeImmutableType
            date: Valksor\Component\DoctrineTools\Doctrine\DBAL\Type\UTCDateType
            date_immutable: Valksor\Component\DoctrineTools\Doctrine\DBAL\Type\UTCDateImmutableType
```

Usage in entities:

```php
<?php

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Event
{
    #[ORM\Column(type: 'datetime')]
    private \DateTime $occurredAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    // All datetime values are automatically handled in UTC
}
```

### Custom DQL Functions

The component provides additional DQL functions for PostgreSQL-specific features:

#### String Functions

```php
// Case-insensitive LIKE (PostgreSQL)
$queryBuilder->andWhere('e.name ILIKE :search')
             ->setParameter('search', '%john%');

// Case-insensitive LIKE with unaccent (PostgreSQL)
$queryBuilder->andWhere('e.name ILIKE_UNACCENT(:search)')
             ->setParameter('search', '%joao%'); // Matches "João", "joão", "JOAO", etc.

// Regular expression replace
$queryBuilder->select('REGEXP_REPLACE(e.description, pattern, replacement, flags) AS cleanDescription');

// String aggregation
$queryBuilder->select('STRING_AGG(e.tags.name, \',\') AS tagList');

// Array position
$queryBuilder->andWhere('ARRAY_POSITION(e.versions, :version) > 0')
             ->setParameter('version', 'v1.0.0');

// Casting
$queryBuilder->select('CAST(e.score AS DECIMAL) AS decimalScore');
```

#### Numeric Functions

```php
// Custom numeric operations (automatically registered)
$queryBuilder->select('SOME_NUMERIC_FUNCTION(e.value) AS result');
```

#### DateTime Functions

```php
// PostgreSQL TO_CHAR function for date formatting
$queryBuilder->select('TO_CHAR(e.createdAt, \'YYYY-MM-DD\') AS formattedDate');
```

### Database Extensions

The component automatically manages PostgreSQL extensions:

```php
// Extensions are automatically registered in migrations:
// - unaccent: For accent-insensitive text search
// - pgcrypto: For cryptographic functions
```

### Event Subscribers

#### DoctrineMigrationsFilter

Filter migrations to prevent execution of migrations from other packages:

```yaml
# Automatically configured through the component
```

#### FixDefaultSchemaListener

Automatically fixes default schema issues in Doctrine:

```yaml
# Automatically configured through the component
```

### Normalizer

#### UTC DateTime Normalizer

Symfony serializer normalizer for consistent UTC datetime handling:

```php
use Valksor\Component\DoctrineTools\Normalizer\UTCDateTimeNormalizer;

// Automatically registered when using Valksor Bundle
```

### Migration Tools

#### Version Comparator

Enhanced version comparison for migrations:

```php
use Valksor\Component\DoctrineTools\Doctrine\Migrations\VersionComparatorWithoutNamespace;

// Automatically configured as the migration version comparator
```

## Configuration

### Basic Configuration

```yaml
# config/packages/valksor.yaml
valksor:
    doctrine_tools:
        enabled: true  # Enable/disable the component
```

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled` | boolean | `true` | Enable or disable the DoctrineTools component |

*See: [`DoctrineToolsConfiguration.php`](DependencyInjection/DoctrineToolsConfiguration.php) for the complete configuration schema.*

### Custom Entity Manager Configuration

The component automatically configures all registered entity managers:

```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        entity_managers:
            default:
                # Automatically gets all DQL functions
            read_only:
                # Also gets all DQL functions
            write_only:
                # And this one too
```

## Advanced Usage

### Creating Custom Entities

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Valksor\Component\DoctrineTools\Doctrine\ORM\Traits\_Uuid;
use Valksor\Component\DoctrineTools\Doctrine\ORM\Traits\_CreatedUpdated;

#[ORM\Entity]
#[ORM\Table(name: 'blog_posts')]
class BlogPost
{
    use _Uuid;
    use _CreatedUpdated;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isPublished = false;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    // ... other getters and setters
}
```

### Using Custom DQL Functions in Repositories

```php
<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\BlogPost;

class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    public function findBySearchTerm(string $term): array
    {
        return $this->createQueryBuilder('bp')
            ->where('bp.title ILIKE_UNACCENT :term')
            ->orWhere('bp.content ILIKE_UNACCENT :term')
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }

    public function getMonthlyArchiveData(): array
    {
        return $this->createQueryBuilder('bp')
            ->select('TO_CHAR(bp.createdAt, \'YYYY-MM\') AS month, COUNT(bp.id) AS count')
            ->groupBy('month')
            ->orderBy('month', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
```

### Working with UTC Datetimes

```php
<?php

namespace App\Service;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;

class BlogPostService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createPost(string $title, string $content): BlogPost
    {
        $post = new BlogPost();
        $post->setTitle($title);
        $post->setContent($content);

        // Timestamps are automatically handled in UTC
        $this->em->persist($post);
        $this->em->flush();

        return $post;
    }
}
```

## Migration Management

The component provides migrations for PostgreSQL extensions:

```bash
# Run migrations to install extensions
php bin/console doctrine:migrations:migrate

# Migrations included:
# - VersionAddExtensionUnaccent: Installs unaccent extension
# - VersionAddExtensionPGCrypto: Installs pgcrypto extension
```

## Performance Considerations

- **UTC Types**: All datetime types ensure consistent timezone handling
- **Lazy Loading**: Entity traits use lazy initialization where appropriate
- **Indexing**: Consider adding database indexes for commonly queried fields
- **Query Optimization**: Use the provided DQL functions for database-level operations


## Contributing

Contributions are welcome!

- Code style requirements (PSR-12)
- Testing requirements for PRs
- One feature per pull request
- Development setup instructions

To contribute to DoctrineTools component:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/doctrine-enhancement`)
3. Implement your enhancement following existing patterns
4. Add comprehensive tests for new functionality
5. Ensure all tests pass and code style is correct
6. Submit a pull request

### Adding New Entity Traits

When adding new entity traits:

1. Create trait under `Valksor\Component\DoctrineTools\Doctrine\ORM\Traits\`
2. Follow naming conventions (prefixed with underscore)
3. Add proper property declarations and methods
4. Add comprehensive unit tests
5. Update documentation with examples

### Adding New DQL Functions

When adding new DQL functions:

1. Create function class implementing appropriate interface
2. Register function in configuration
3. Add SQL implementations for each supported database
4. Add comprehensive tests
5. Update documentation with usage examples

## Security

If you discover any security-related issues, please email us at packages@valksor.com instead of using the issue tracker.

## Support

- **Documentation**: [Full documentation](https://github.com/valksor/php-valksor)
- **Issues**: [GitHub Issues](https://github.com/valksor/php-valksor/issues) for bug reports and feature requests
- **Discussions**: [GitHub Discussions](https://github.com/valksor/php-valksor/discussions) for questions and community support
- **Stack Overflow**: Use tag `valksor-php-doctrine-tools`
- **Doctrine Documentation**: [Official Doctrine docs](https://www.doctrine-project.org/)

## Credits

- **[Original Author](https://github.com/valksor)** - Creator and maintainer
- **[All Contributors](https://github.com/valksor/php-valksor/graphs/contributors)** - Thank you to all who contributed
- **[Doctrine Project](https://www.doctrine-project.org)** - ORM and DBAL framework inspiration
- **[PostgreSQL Team](https://www.postgresql.org)** - Database-specific features and extensions
- **[Symfony Team](https://symfony.com/doc/current/doctrine.html)** - Doctrine integration best practices
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

If you find this DoctrineTools component useful, you might want to check out the full Valksor project for additional tools and utilities that can enhance your Symfony application development.

To install the complete package:

```bash
composer require valksor/php-valksor
```
