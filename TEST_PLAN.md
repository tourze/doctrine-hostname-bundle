# Test Plan for doctrine-hostname-bundle

This document outlines the test plan for the `doctrine-hostname-bundle` package.

## Test Cases

| File | Class/Method | Test Focus | Scenario | Status | Passed |
|---|---|---|---|---|---| `src/DoctrineHostnameBundle.php` | `DoctrineHostnameBundle::getBundleDependencies()` | Bundle Dependencies | Verify correct bundle dependencies are returned | ✅ Done | ✅ | `src/DependencyInjection/DoctrineHostnameExtension.php` | `DoctrineHostnameExtension::load()` | Service Loading | Verify `services.yaml` is loaded | ✅ Done | ✅ | `src/EventSubscriber/HostListener.php` | `HostListener::prePersist()` / `prePersistEntity()` | `CreatedInHostColumn` | Entity with `CreatedInHostColumn`, property is null | ✅ Done | ❓ |
| `src/EventSubscriber/HostListener.php` | `HostListener::prePersist()` / `prePersistEntity()` | `CreatedInHostColumn` | Entity with `CreatedInHostColumn`, property has existing value | ✅ Done | ❓ |
| `src/EventSubscriber/HostListener.php` | `HostListener::prePersist()` / `prePersistEntity()` | `CreatedInHostColumn` | Entity without `CreatedInHostColumn` | ✅ Done | ❓ |
| `src/EventSubscriber/HostListener.php` | `HostListener::prePersist()` / `prePersistEntity()` | `CreatedInHostColumn` | Logger interaction when property is set | ✅ Done | ❓ |
| `src/EventSubscriber/HostListener.php` | `HostListener::preUpdate()` / `preUpdateEntity()` | `UpdatedInHostColumn` | Entity with `UpdatedInHostColumn`, changeset is not empty | ✅ Done | ❓ |
| `src/EventSubscriber/HostListener.php` | `HostListener::preUpdate()` | `UpdatedInHostColumn` | Entity with `UpdatedInHostColumn`, changeset is empty | ✅ Done | ❓ |
| `src/EventSubscriber/HostListener.php` | `HostListener::preUpdate()` / `preUpdateEntity()` | `UpdatedInHostColumn` | Entity without `UpdatedInHostColumn` | ✅ Done | ❓ |
| `src/EventSubscriber/HostListener.php` | `HostListener::preUpdate()` / `preUpdateEntity()` | `UpdatedInHostColumn` | Logger interaction when property is set | ✅ Done | ❓ | `src/Attribute/CreatedInHostColumn.php` | `CreatedInHostColumn` | Attribute Definition | Verify class exists and is an attribute | ✅ Done | ✅ (Tested via HostListenerTest) |
| `src/Attribute/UpdatedInHostColumn.php` | `UpdatedInHostColumn` | Attribute Definition | Verify class exists and is an attribute | ✅ Done | ✅ (Tested via HostListenerTest) |
| | | | | | | | | | |