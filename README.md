# Doctrine Hostname Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-hostname-bundle)
[![Build Status](https://img.shields.io/travis/tourze/doctrine-hostname-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/doctrine-hostname-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/doctrine-hostname-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-hostname-bundle)

A Symfony bundle for automatically recording the hostname when creating or updating Doctrine entities.

## Features

- Automatically records hostname on entity creation
- Automatically records hostname on entity update
- Configuration via PHP 8.1 attributes
- Integrates with Doctrine Entity Checker Bundle

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine Bundle 2.13 or higher

## Installation

```bash
composer require tourze/doctrine-hostname-bundle
```

## Quick Start

Add the attributes to your entity properties:

```php
use Tourze\DoctrineHostnameBundle\Attribute\CreatedInHostColumn;
use Tourze\DoctrineHostnameBundle\Attribute\UpdatedInHostColumn;

class YourEntity
{
    #[CreatedInHostColumn]
    private ?string $createdInHost = null;

    #[UpdatedInHostColumn]
    private ?string $updatedInHost = null;
}
```

The bundle will automatically:

- Set `createdInHost` to the current hostname when the entity is created
- Set `updatedInHost` to the current hostname when the entity is updated

## Documentation

- Simple configuration via PHP attributes
- Customizable column names for hostname fields
- Fully compatible with other Tourze Doctrine extensions

## Contributing

Feel free to submit issues and PRs. Please follow PSR code standards and provide relevant tests.

## License

MIT License © Tourze

## Changelog

See [CHANGELOG.md] for details (if available).
