# Doctrine Hostname Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-hostname-bundle)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg?style=flat-square)](https://php.net/)
[![License](https://img.shields.io/packagist/l/tourze/doctrine-hostname-bundle.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/tourze/doctrine-hostname-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/doctrine-hostname-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://codecov.io/gh/tourze/doctrine-hostname-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/doctrine-hostname-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-hostname-bundle)

A Symfony bundle that automatically records the hostname when creating or updating Doctrine entities. This bundle helps track which server handled entity operations in distributed systems.

## Features

- **Automatic hostname recording**: Captures server hostname during entity persistence
- **Attribute-based configuration**: Uses PHP 8.1 attributes for clean, declarative setup
- **Separate creation and update tracking**: Different attributes for creation vs update operations
- **Non-intrusive**: Doesn't overwrite existing values if already set
- **Logger integration**: Optional debug logging for troubleshooting
- **High performance**: Minimal overhead with `-99` priority to run after other listeners

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine Bundle 2.13 or higher
- Doctrine ORM 3.0 or higher

## Installation

```bash
composer require tourze/doctrine-hostname-bundle
```

The bundle will be automatically registered in your Symfony application.

## Quick Start

Add the attributes to your entity properties:

```php
<?php

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineHostnameBundle\Attribute\CreatedInHostColumn;
use Tourze\DoctrineHostnameBundle\Attribute\UpdatedInHostColumn;

#[ORM\Entity]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[CreatedInHostColumn]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $createdInHost = null;

    #[UpdatedInHostColumn]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $updatedInHost = null;

    // Getters and setters...
    public function getCreatedInHost(): ?string
    {
        return $this->createdInHost;
    }

    public function setCreatedInHost(?string $createdInHost): void
    {
        $this->createdInHost = $createdInHost;
    }

    public function getUpdatedInHost(): ?string
    {
        return $this->updatedInHost;
    }

    public function setUpdatedInHost(?string $updatedInHost): void
    {
        $this->updatedInHost = $updatedInHost;
    }
}
```

## How It Works

The bundle automatically:

1. **On entity creation**: Sets `createdInHost` to the current server hostname using PHP's `gethostname()` function
2. **On entity update**: Sets `updatedInHost` to the current server hostname when entity data changes
3. **Preserves existing values**: Won't overwrite hostname if already set (useful for manual assignments)
4. **Logs operations**: Provides debug logging for monitoring hostname assignments

## Use Cases

- **Distributed systems**: Track which server processed specific entities
- **Load balancing**: Monitor entity operations across multiple application servers
- **Auditing**: Maintain hostname records for compliance and troubleshooting
- **Performance monitoring**: Analyze entity operations by server location

## Advanced Usage

### Custom Property Names

You can use any property name with the attributes:

```php
class MyEntity
{
    #[CreatedInHostColumn]
    private ?string $originServer = null;

    #[UpdatedInHostColumn]
    private ?string $lastModifiedServer = null;
}
```

### Conditional Hostname Setting

The bundle respects existing values:

```php
$entity = new Product();
$entity->setCreatedInHost('manual-override');
$entityManager->persist($entity);
$entityManager->flush();
// createdInHost will remain 'manual-override'
```

## Configuration

The bundle requires no configuration and works out of the box. It automatically registers:

- `HostListener` as a Doctrine event subscriber
- Custom `PropertyAccessor` service for safe property access
- Debug logging integration (if logger is available)

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/doctrine-hostname-bundle/tests
```

The bundle includes comprehensive unit and integration tests covering:
- Attribute functionality
- Event listener behavior
- Service container integration
- Hostname recording logic

## Performance Considerations

- **Minimal overhead**: Uses reflection only during entity operations
- **Efficient execution**: Runs with `-99` priority to execute after other listeners
- **Memory conscious**: Properly handles PropertyAccessor exceptions
- **Change detection**: Only updates hostname when entity data actually changes

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

Please follow PSR-12 coding standards and include appropriate tests.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Changelog

See [CHANGELOG.md] for version history and breaking changes (if available).
