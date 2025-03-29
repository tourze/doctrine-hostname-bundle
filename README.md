# Doctrine Hostname Bundle

[English](#english) | [中文](#中文)

## English

A Symfony bundle that automatically records the hostname when creating or updating Doctrine entities.

### Features

- Automatically records hostname on entity creation
- Automatically records hostname on entity updates
- Uses PHP 8.1 attributes for configuration
- Integrates with Doctrine Entity Checker Bundle

### Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine Bundle 2.13 or higher

### Installation

```bash
composer require tourze/doctrine-hostname-bundle
```

### Usage

Add attributes to your entity properties:

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

## 中文

一个用于自动记录 Doctrine 实体创建和更新时主机名的 Symfony Bundle。

### 功能特点

- 自动记录实体创建时的主机名
- 自动记录实体更新时的主机名
- 使用 PHP 8.1 属性进行配置
- 与 Doctrine Entity Checker Bundle 集成

### 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine Bundle 2.13 或更高版本

### 安装

```bash
composer require tourze/doctrine-hostname-bundle
```

### 使用方法

在实体属性上添加属性：

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

Bundle 会自动：

- 在实体创建时设置 `createdInHost` 为当前主机名
- 在实体更新时设置 `updatedInHost` 为当前主机名
