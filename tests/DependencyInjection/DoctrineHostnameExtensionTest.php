<?php

namespace Tourze\DoctrineHostnameBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\DoctrineHostnameBundle\DependencyInjection\DoctrineHostnameExtension;
use Tourze\DoctrineHostnameBundle\EventSubscriber\HostListener;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(DoctrineHostnameExtension::class)]
final class DoctrineHostnameExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testServicesAreLoaded(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');
        $extension = new DoctrineHostnameExtension();

        $extension->load([], $container);

        $this->assertTrue($container->has(HostListener::class));

        $definition = $container->getDefinition(HostListener::class);
        $this->assertTrue($definition->isAutowired());
        $this->assertTrue($definition->isAutoconfigured());
    }
}
