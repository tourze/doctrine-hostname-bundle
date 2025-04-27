# Doctrine Hostname Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-hostname-bundle)
[![Build Status](https://img.shields.io/travis/tourze/doctrine-hostname-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/doctrine-hostname-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/doctrine-hostname-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/doctrine-hostname-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-hostname-bundle)

一个用于自动记录 Doctrine 实体创建和更新时主机名的 Symfony Bundle。

## 功能特性

- 实体创建时自动记录主机名
- 实体更新时自动记录主机名
- 基于 PHP 8.1 属性实现配置
- 与 Doctrine Entity Checker Bundle 集成

## 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine Bundle 2.13 或更高版本

## 安装说明

```bash
composer require tourze/doctrine-hostname-bundle
```

## 快速开始

在你的实体属性上添加如下属性：

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

该 Bundle 会自动：

- 在实体创建时将 `createdInHost` 设置为当前主机名
- 在实体更新时将 `updatedInHost` 设置为当前主机名

## 详细文档

- 支持通过 PHP 属性灵活配置
- 支持自定义主机名字段名
- 与其它 Tourze Doctrine 扩展兼容

## 贡献指南

欢迎提交 Issue 和 PR，建议遵循 PSR 代码规范并补充对应测试。

## 版权和许可

MIT License © Tourze

## 更新日志

详见 [CHANGELOG.md]（如有）。
