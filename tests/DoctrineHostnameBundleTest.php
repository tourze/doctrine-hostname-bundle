<?php

namespace Tourze\DoctrineHostnameBundle\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineEntityCheckerBundle\DoctrineEntityCheckerBundle;
use Tourze\DoctrineHostnameBundle\DoctrineHostnameBundle;

class DoctrineHostnameBundleTest extends TestCase
{
    public function testBundleDependencies(): void
    {
        $dependencies = DoctrineHostnameBundle::getBundleDependencies();

        $this->assertIsArray($dependencies);
        $this->assertArrayHasKey(DoctrineEntityCheckerBundle::class, $dependencies);
        $this->assertEquals(['all' => true], $dependencies[DoctrineEntityCheckerBundle::class]);
    }
}
