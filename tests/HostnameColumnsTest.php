<?php

namespace Tourze\DoctrineHostnameBundle\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineHostnameBundle\Attribute\CreatedInHostColumn;
use Tourze\DoctrineHostnameBundle\Attribute\UpdatedInHostColumn;

class TestEntity
{
    #[CreatedInHostColumn]
    private ?string $createdInHost = null;

    #[UpdatedInHostColumn]
    private ?string $updatedInHost = null;

    private string $name = 'Test';

    public function getCreatedInHost(): ?string
    {
        return $this->createdInHost;
    }

    public function setCreatedInHost(?string $host): self
    {
        $this->createdInHost = $host;
        return $this;
    }

    public function getUpdatedInHost(): ?string
    {
        return $this->updatedInHost;
    }

    public function setUpdatedInHost(?string $host): self
    {
        $this->updatedInHost = $host;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}

class HostnameColumnsTest extends TestCase
{
    public function testCreatedInHostColumnAttribute(): void
    {
        $reflection = new \ReflectionClass(TestEntity::class);
        $property = $reflection->getProperty('createdInHost');

        $attributes = $property->getAttributes(CreatedInHostColumn::class);
        $this->assertNotEmpty($attributes, 'Property should have CreatedInHostColumn attribute');
    }

    public function testUpdatedInHostColumnAttribute(): void
    {
        $reflection = new \ReflectionClass(TestEntity::class);
        $property = $reflection->getProperty('updatedInHost');

        $attributes = $property->getAttributes(UpdatedInHostColumn::class);
        $this->assertNotEmpty($attributes, 'Property should have UpdatedInHostColumn attribute');
    }

    public function testHostnameSetterAndGetter(): void
    {
        $entity = new TestEntity();

        // 测试 CreatedInHost
        $hostname = gethostname();
        $entity->setCreatedInHost($hostname);
        $this->assertEquals($hostname, $entity->getCreatedInHost());

        // 测试 UpdatedInHost
        $entity->setUpdatedInHost($hostname);
        $this->assertEquals($hostname, $entity->getUpdatedInHost());
    }
}
