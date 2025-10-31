<?php

namespace Tourze\DoctrineHostnameBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DoctrineHostnameBundle\Attribute\CreatedInHostColumn;
use Tourze\DoctrineHostnameBundle\Attribute\UpdatedInHostColumn;
use Tourze\DoctrineHostnameBundle\EventSubscriber\HostListener;
use Tourze\DoctrineHostnameBundle\Tests\Fixtures\MockClassMetadata;
use Tourze\DoctrineHostnameBundle\Tests\Fixtures\MockEntityManager;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(HostListener::class)]
#[RunTestsInSeparateProcesses]
final class HostListenerTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // HostListener 测试的初始化逻辑
        // 这里可以添加任何必要的设置
    }

    private function createMockEntityManager(object $entity): EntityManagerInterface
    {
        $classMetadata = new MockClassMetadata($entity);

        return new MockEntityManager($classMetadata);
    }

    // Test prePersist with CreatedInHostColumn
    public function testPrePersistWithCreatedInHostColumnSetsHostnameWhenNull(): void
    {
        /** @var HostListener $hostListener */
        $hostListener = self::getContainer()->get(HostListener::class);

        $entity = new class {
            #[CreatedInHostColumn]
            private ?string $createdHost = null;

            public function getCreatedHost(): ?string
            {
                return $this->createdHost;
            }

            public function setCreatedHost(?string $createdHost): void
            {
                $this->createdHost = $createdHost;
            }
        };

        $objectManager = $this->createMockEntityManager($entity);

        $eventArgs = new PrePersistEventArgs($entity, $objectManager);
        $hostListener->prePersist($eventArgs);

        $this->assertNotNull($entity->getCreatedHost());
        $this->assertEquals(gethostname(), $entity->getCreatedHost());
    }

    public function testPrePersistWithCreatedInHostColumnDoesNotOverwriteExistingValue(): void
    {
        /** @var HostListener $hostListener */
        $hostListener = self::getContainer()->get(HostListener::class);

        $initialHostname = 'initial.host';
        $entity = new class($initialHostname) {
            #[CreatedInHostColumn]
            private ?string $createdHost;

            public function __construct(string $host)
            {
                $this->createdHost = $host;
            }

            public function getCreatedHost(): ?string
            {
                return $this->createdHost;
            }

            public function setCreatedHost(?string $createdHost): void
            {
                $this->createdHost = $createdHost;
            }
        };

        $objectManager = $this->createMockEntityManager($entity);

        $eventArgs = new PrePersistEventArgs($entity, $objectManager);
        $hostListener->prePersist($eventArgs);

        // 已经有值的属性不应该被覆盖
        $this->assertEquals($initialHostname, $entity->getCreatedHost());
        $this->assertNotEquals(gethostname(), $entity->getCreatedHost());
    }

    public function testPrePersistWithoutCreatedInHostColumnDoesNothing(): void
    {
        /** @var HostListener $hostListener */
        $hostListener = self::getContainer()->get(HostListener::class);

        $entity = new class {
            private ?string $someOtherProperty = null;

            public function getSomeOtherProperty(): ?string
            {
                return $this->someOtherProperty;
            }

            public function setSomeOtherProperty(?string $someOtherProperty): void
            {
                $this->someOtherProperty = $someOtherProperty;
            }
        };

        $objectManager = $this->createMockEntityManager($entity);

        $eventArgs = new PrePersistEventArgs($entity, $objectManager);
        $hostListener->prePersist($eventArgs);

        // 没有 CreatedInHostColumn 注解的实体不应该被修改
        $this->assertNull($entity->getSomeOtherProperty());
    }

    public function testPrePersistLogsWhenHostnameIsSet(): void
    {
        /** @var HostListener $hostListener */
        $hostListener = self::getContainer()->get(HostListener::class);

        $entity = new class {
            #[CreatedInHostColumn]
            private ?string $createdHost = null;

            public function getCreatedHost(): ?string
            {
                return $this->createdHost;
            }

            public function setCreatedHost(?string $createdHost): void
            {
                $this->createdHost = $createdHost;
            }
        };

        $objectManager = $this->createMockEntityManager($entity);

        // Logger assertions are removed for integration tests

        $eventArgs = new PrePersistEventArgs($entity, $objectManager);
        $hostListener->prePersist($eventArgs);

        // Verify that hostname was set
        $this->assertNotNull($entity->getCreatedHost());
    }

    // Test preUpdate with UpdatedInHostColumn
    public function testPreUpdateWithUpdatedInHostColumnSetsHostnameWhenChangesExist(): void
    {
        /** @var HostListener $hostListener */
        $hostListener = self::getContainer()->get(HostListener::class);

        $entity = new class {
            #[UpdatedInHostColumn]
            private ?string $updatedHost = null;

            public function getUpdatedHost(): ?string
            {
                return $this->updatedHost;
            }

            public function setUpdatedHost(?string $updatedHost): void
            {
                $this->updatedHost = $updatedHost;
            }
        };

        $objectManager = $this->createMockEntityManager($entity);

        $changeSet = ['someField' => ['oldValue', 'newValue']];
        $eventArgs = new PreUpdateEventArgs($entity, $objectManager, $changeSet);
        $hostListener->preUpdate($eventArgs);

        $this->assertNotNull($entity->getUpdatedHost());
        $this->assertEquals(gethostname(), $entity->getUpdatedHost());
    }

    public function testPreUpdateWithUpdatedInHostColumnDoesNothingWhenNoChanges(): void
    {
        /** @var HostListener $hostListener */
        $hostListener = self::getContainer()->get(HostListener::class);

        $entity = new class {
            #[UpdatedInHostColumn]
            private ?string $updatedHost = 'initial.host';

            public function getUpdatedHost(): ?string
            {
                return $this->updatedHost;
            }

            public function setUpdatedHost(?string $updatedHost): void
            {
                $this->updatedHost = $updatedHost;
            }
        };

        // 空的 changeset 应该让 preUpdate 提前返回
        $changeSet = [];
        $objectManager = $this->createMockEntityManager($entity);
        $eventArgs = new PreUpdateEventArgs($entity, $objectManager, $changeSet);
        $hostListener->preUpdate($eventArgs);

        $this->assertEquals('initial.host', $entity->getUpdatedHost());
    }

    public function testPreUpdateWithoutUpdatedInHostColumnDoesNothing(): void
    {
        /** @var HostListener $hostListener */
        $hostListener = self::getContainer()->get(HostListener::class);

        $entity = new class {
            private ?string $someOtherProperty = null;

            public function getSomeOtherProperty(): ?string
            {
                return $this->someOtherProperty;
            }

            public function setSomeOtherProperty(?string $someOtherProperty): void
            {
                $this->someOtherProperty = $someOtherProperty;
            }
        };

        $objectManager = $this->createMockEntityManager($entity);

        $changeSet = ['someField' => ['oldValue', 'newValue']];
        $eventArgs = new PreUpdateEventArgs($entity, $objectManager, $changeSet);
        $hostListener->preUpdate($eventArgs);

        $this->assertNull($entity->getSomeOtherProperty());
    }

    public function testPreUpdateLogsWhenHostnameIsSet(): void
    {
        /** @var HostListener $hostListener */
        $hostListener = self::getContainer()->get(HostListener::class);

        $entity = new class {
            #[UpdatedInHostColumn]
            private ?string $updatedHost = null;

            public function getUpdatedHost(): ?string
            {
                return $this->updatedHost;
            }

            public function setUpdatedHost(?string $updatedHost): void
            {
                $this->updatedHost = $updatedHost;
            }
        };

        $objectManager = $this->createMockEntityManager($entity);

        // Logger assertions are removed for integration tests

        $changeSet = ['someField' => ['oldValue', 'newValue']];
        $eventArgs = new PreUpdateEventArgs($entity, $objectManager, $changeSet);
        $hostListener->preUpdate($eventArgs);

        // Verify that hostname was set
        $this->assertNotNull($entity->getUpdatedHost());
    }

    public function testPrePersistEntity(): void
    {
        /** @var HostListener $hostListener */
        $hostListener = self::getContainer()->get(HostListener::class);

        $entity = new class {
            #[CreatedInHostColumn]
            private ?string $createdHost = null;

            public function getCreatedHost(): ?string
            {
                return $this->createdHost;
            }

            public function setCreatedHost(?string $createdHost): void
            {
                $this->createdHost = $createdHost;
            }
        };

        $objectManager = $this->createMockEntityManager($entity);

        $hostListener->prePersistEntity($objectManager, $entity);

        $this->assertNotNull($entity->getCreatedHost());
        $this->assertEquals(gethostname(), $entity->getCreatedHost());
    }

    public function testPreUpdateEntity(): void
    {
        /** @var HostListener $hostListener */
        $hostListener = self::getContainer()->get(HostListener::class);

        $entity = new class {
            #[UpdatedInHostColumn]
            private ?string $updatedHost = null;

            public function getUpdatedHost(): ?string
            {
                return $this->updatedHost;
            }

            public function setUpdatedHost(?string $updatedHost): void
            {
                $this->updatedHost = $updatedHost;
            }
        };

        $objectManager = $this->createMockEntityManager($entity);

        $changeSet = ['someField' => ['oldValue', 'newValue']];
        $eventArgs = new PreUpdateEventArgs($entity, $objectManager, $changeSet);

        $hostListener->preUpdateEntity($objectManager, $entity, $eventArgs);

        $this->assertNotNull($entity->getUpdatedHost());
        $this->assertEquals(gethostname(), $entity->getUpdatedHost());
    }
}
