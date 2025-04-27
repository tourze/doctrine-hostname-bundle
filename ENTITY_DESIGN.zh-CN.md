# Doctrine Hostname Bundle 实体设计说明

本模块本身不直接定义数据库实体，而是通过 PHP 8.1 属性（Attribute）为其他实体提供主机名自动记录的能力。

## 设计说明

- 通过 `#[CreatedInHostColumn]` 属性，实体可自动记录创建时的主机名。
- 通过 `#[UpdatedInHostColumn]` 属性，实体可自动记录更新时的主机名。
- 这两个属性可灵活添加到任意 Doctrine 实体的私有属性上。
- 监听器 `HostListener` 会在实体的 `prePersist` 和 `preUpdate` 阶段自动填充主机名。

## 示例

```php
use Tourze\DoctrineHostnameBundle\Attribute\CreatedInHostColumn;
use Tourze\DoctrineHostnameBundle\Attribute\UpdatedInHostColumn;

class ExampleEntity
{
    #[CreatedInHostColumn]
    private ?string $createdInHost = null;

    #[UpdatedInHostColumn]
    private ?string $updatedInHost = null;
}
```

## 设计要点

- 不限制字段名，开发者可根据实际需要添加到任意属性。
- 字段类型推荐为 `string|null`。
- 自动赋值逻辑完全由 Bundle 监听器实现，无需手动干预。
- 支持与其它 Tourze Doctrine 扩展协同使用。

## 适用场景

- 需要追踪数据创建、更新来源主机的业务系统。
- 多机部署、分布式场景下的数据溯源。
