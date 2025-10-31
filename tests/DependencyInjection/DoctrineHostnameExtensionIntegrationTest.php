<?php

namespace Tourze\DoctrineHostnameBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\DoctrineHostnameBundle\DependencyInjection\DoctrineHostnameExtension;
use Tourze\DoctrineHostnameBundle\EventSubscriber\HostListener;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(DoctrineHostnameExtension::class)]
final class DoctrineHostnameExtensionIntegrationTest extends AbstractDependencyInjectionExtensionTestCase
{
    private DoctrineHostnameExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new DoctrineHostnameExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.environment', 'test');
    }

    public function testHostListenerIsRegisteredAsService(): void
    {
        // Act
        $this->extension->load([], $this->container);

        // Assert
        $this->assertTrue($this->container->hasDefinition(HostListener::class));
    }

    public function testPropertyAccessorIsRegisteredAsService(): void
    {
        // Act
        $this->extension->load([], $this->container);

        // Assert
        $this->assertTrue($this->container->hasDefinition('doctrine-host.property-accessor'));

        $definition = $this->container->getDefinition('doctrine-host.property-accessor');
        $this->assertEquals(PropertyAccessor::class, $definition->getClass());
    }

    public function testHostListenerIsAutowiredCorrectly(): void
    {
        // Act
        $this->extension->load([], $this->container);

        // Assert
        $definition = $this->container->getDefinition(HostListener::class);
        $this->assertTrue($definition->isAutowired());
        $this->assertTrue($definition->isAutoconfigured());
    }

    public function testAllRequiredServicesAreAvailable(): void
    {
        // Act
        $this->extension->load([], $this->container);

        // Assert - 验证所有必需的服务都已注册
        $this->assertTrue($this->container->hasDefinition(HostListener::class));
        $this->assertTrue($this->container->hasDefinition('doctrine-host.property-accessor'));
    }
}
