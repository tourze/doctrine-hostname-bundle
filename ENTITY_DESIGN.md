# Doctrine Hostname Bundle Entity Design

This bundle does not define its own database entities. Instead, it provides PHP 8.1 attributes to help other Doctrine entities automatically record the hostname for creation and update events.

## Design Overview

- Use the `#[CreatedInHostColumn]` attribute to automatically record the hostname when an entity is created.
- Use the `#[UpdatedInHostColumn]` attribute to automatically record the hostname when an entity is updated.
- These attributes can be flexibly added to any private property of a Doctrine entity.
- The `HostListener` event subscriber automatically fills in the hostname during the `prePersist` and `preUpdate` events.

## Example

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

## Design Notes

- There is no restriction on the property names; developers can add the attributes to any property as needed.
- The recommended type for these properties is `string|null`.
- The assignment logic is fully handled by the bundle's event subscriber; no manual intervention is needed.
- Fully compatible with other Tourze Doctrine extensions.

## Use Cases

- Track which host created or updated a record in multi-server environments.
- Data provenance in distributed deployments.
