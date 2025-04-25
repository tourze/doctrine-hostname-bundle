<?php

namespace Tourze\DoctrineHostnameBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\DoctrineHostnameBundle\DependencyInjection\DoctrineHostnameExtension;
use Tourze\DoctrineHostnameBundle\EventSubscriber\HostListener;

class DoctrineHostnameExtensionTest extends TestCase
{
    public function testServicesAreLoaded(): void
    {
        $container = new ContainerBuilder();
        $extension = new DoctrineHostnameExtension();

        $extension->load([], $container);

        // 断言 HostListener 服务已注册
        $this->assertTrue($container->has(HostListener::class));

        // 检查服务定义是否符合预期
        $definition = $container->getDefinition(HostListener::class);
        $this->assertTrue($definition->isAutowired());
        $this->assertTrue($definition->isAutoconfigured());
    }
}
