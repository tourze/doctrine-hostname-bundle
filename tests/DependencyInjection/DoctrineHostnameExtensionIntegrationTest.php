<?php

namespace Tourze\DoctrineHostnameBundle\Tests\DependencyInjection;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\DoctrineHostnameBundle\EventSubscriber\HostListener;
use Tourze\DoctrineHostnameBundle\Tests\IntegrationTestCase;

class DoctrineHostnameExtensionIntegrationTest extends IntegrationTestCase
{
    public function test_hostListener_isRegisteredAsService(): void
    {
        // Act
        $hostListener = static::getContainer()->get(HostListener::class);

        // Assert
        $this->assertInstanceOf(HostListener::class, $hostListener);
    }

    public function test_propertyAccessor_isRegisteredAsService(): void
    {
        // Act
        $propertyAccessor = static::getContainer()->get('doctrine-host.property-accessor');

        // Assert
        $this->assertInstanceOf(PropertyAccessor::class, $propertyAccessor);
    }

    public function test_hostListener_isAutowiredCorrectly(): void
    {
        // Act
        $hostListener = static::getContainer()->get(HostListener::class);

        // Assert
        $this->assertInstanceOf(HostListener::class, $hostListener);
        // 验证监听器已正确配置
        $this->assertNotNull($hostListener);
    }

    public function test_allRequiredServices_areAvailable(): void
    {
        $container = static::getContainer();

        // Assert - 验证所有必需的服务都已注册
        $this->assertTrue($container->has(HostListener::class));
        $this->assertTrue($container->has('doctrine-host.property-accessor'));
    }
}
