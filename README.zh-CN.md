# Doctrine Hostname Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-hostname-bundle)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg?style=flat-square)](https://php.net/)
[![License](https://img.shields.io/packagist/l/tourze/doctrine-hostname-bundle.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/tourze/doctrine-hostname-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/doctrine-hostname-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://codecov.io/gh/tourze/doctrine-hostname-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/doctrine-hostname-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-hostname-bundle)

一个用于自动记录 Doctrine 实体创建和更新时主机名的 Symfony Bundle。此 Bundle 帮助追踪分布式系统中哪个服务器处理了实体操作。

## 功能特性

- **自动记录主机名**：在实体持久化时自动捕获服务器主机名
- **基于属性的配置**：使用 PHP 8.1 属性进行简洁的声明式配置
- **分离创建和更新追踪**：为创建和更新操作提供不同的属性
- **非侵入式**：不会覆盖已设置的现有值
- **日志集成**：提供可选的调试日志记录功能
- **高性能**：使用 `-99` 优先级在其他监听器之后运行，开销最小

## 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine Bundle 2.13 或更高版本
- Doctrine ORM 3.0 或更高版本

## 安装说明

```bash
composer require tourze/doctrine-hostname-bundle
```

Bundle 会自动在你的 Symfony 应用程序中注册。

## 快速开始

在你的实体属性上添加如下属性：

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

## 工作原理

该 Bundle 自动：

1. **实体创建时**：使用 PHP 的 `gethostname()` 函数将 `createdInHost` 设置为当前服务器主机名
2. **实体更新时**：当实体数据发生变化时，将 `updatedInHost` 设置为当前服务器主机名
3. **保留现有值**：不会覆盖已设置的主机名（便于手动赋值）
4. **记录操作**：提供调试日志记录用于监控主机名分配

## 使用场景

- **分布式系统**：追踪哪个服务器处理了特定实体
- **负载均衡**：监控多个应用服务器上的实体操作
- **审计**：维护主机名记录以符合合规性和故障排除要求
- **性能监控**：按服务器位置分析实体操作

## 高级用法

### 自定义属性名

您可以为属性使用任何属性名：

```php
class MyEntity
{
    #[CreatedInHostColumn]
    private ?string $originServer = null;

    #[UpdatedInHostColumn]
    private ?string $lastModifiedServer = null;
}
```

### 条件主机名设置

Bundle 会尊重现有值：

```php
$entity = new Product();
$entity->setCreatedInHost('manual-override');
$entityManager->persist($entity);
$entityManager->flush();
// createdInHost 将保持 'manual-override'
```

## 配置

Bundle 无需配置即可开箱即用。它会自动注册：

- `HostListener` 作为 Doctrine 事件订阅者
- 用于安全属性访问的自定义 `PropertyAccessor` 服务
- 调试日志集成（如果日志记录器可用）

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/doctrine-hostname-bundle/tests
```

Bundle 包含全面的单元和集成测试，涵盖：
- 属性功能
- 事件监听器行为
- 服务容器集成
- 主机名记录逻辑

## 性能考虑

- **最小开销**：仅在实体操作期间使用反射
- **高效执行**：使用 `-99` 优先级在其他监听器之后执行
- **内存感知**：正确处理 PropertyAccessor 异常
- **变更检测**：仅在实体数据实际更改时更新主机名

## 贡献指南

1. Fork 仓库
2. 创建功能分支
3. 进行更改
4. 为新功能添加测试
5. 确保所有测试通过
6. 提交拉取请求

请遵循 PSR-12 编码标准并包含适当的测试。

## 版权和许可

MIT License (MIT)。更多信息请参阅 [License File](LICENSE)。

## 更新日志

查看 [CHANGELOG.md] 了解版本历史和重大更改（如有）。
