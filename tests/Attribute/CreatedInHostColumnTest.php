<?php

namespace Tourze\DoctrineHostnameBundle\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineHostnameBundle\Attribute\CreatedInHostColumn;

class CreatedInHostColumnTest extends TestCase
{
    public function testAttributeCanBeCreated(): void
    {
        $attribute = new CreatedInHostColumn();
        $this->assertInstanceOf(CreatedInHostColumn::class, $attribute);
    }

    public function testAttributeTargetsProperty(): void
    {
        $reflectionClass = new \ReflectionClass(CreatedInHostColumn::class);
        $attributes = $reflectionClass->getAttributes(\Attribute::class);

        $this->assertCount(1, $attributes);
        $this->assertEquals(
            \Attribute::TARGET_PROPERTY,
            $attributes[0]->newInstance()->flags
        );
    }
}
