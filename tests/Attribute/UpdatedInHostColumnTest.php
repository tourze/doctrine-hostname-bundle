<?php

namespace Tourze\DoctrineHostnameBundle\Tests\Attribute;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DoctrineHostnameBundle\Attribute\UpdatedInHostColumn;

/**
 * @internal
 */
#[CoversClass(UpdatedInHostColumn::class)]
final class UpdatedInHostColumnTest extends TestCase
{
    public function testAttributeCanBeCreated(): void
    {
        $attribute = new UpdatedInHostColumn();
        $this->assertInstanceOf(UpdatedInHostColumn::class, $attribute);
    }

    public function testAttributeTargetsProperty(): void
    {
        $reflectionClass = new \ReflectionClass(UpdatedInHostColumn::class);
        $attributes = $reflectionClass->getAttributes(\Attribute::class);

        $this->assertCount(1, $attributes);
        $this->assertEquals(
            \Attribute::TARGET_PROPERTY,
            $attributes[0]->newInstance()->flags
        );
    }
}
