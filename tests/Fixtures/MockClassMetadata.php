<?php

namespace Tourze\DoctrineHostnameBundle\Tests\Fixtures;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Simplified ClassMetadata implementation for testing
 *
 * @internal
 * @template T of object
 * @extends ClassMetadata<T>
 */
final class MockClassMetadata extends ClassMetadata
{
    private object $entity;

    public function __construct(object $entity)
    {
        parent::__construct($entity::class);
        $this->entity = $entity;
    }

    public function getReflectionClass(): \ReflectionClass
    {
        return new \ReflectionClass($this->entity);
    }

    public function getName(): string
    {
        return $this->entity::class;
    }
}
