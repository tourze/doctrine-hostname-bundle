<?php

namespace Tourze\DoctrineHostnameBundle\Tests\EventSubscriber;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use Tourze\DoctrineHostnameBundle\Attribute\CreatedInHostColumn;
use Tourze\DoctrineHostnameBundle\Attribute\UpdatedInHostColumn;
use Tourze\DoctrineHostnameBundle\EventSubscriber\HostListener;
use Tourze\DoctrineHostnameBundle\Tests\IntegrationTestCase;

class HostListenerSimpleIntegrationTest extends IntegrationTestCase
{
    private HostListener $hostListener;

    public function test_hostListener_isAutowiredCorrectly(): void
    {
        // Assert
        $this->assertInstanceOf(HostListener::class, $this->hostListener);
        $this->assertNotNull($this->hostListener);
    }

    public function test_prePersist_withMockEntity_setsHostnameCorrectly(): void
    {
        // Arrange
        $entity = $this->createMockEntityWithCreatedInHostColumn();
        $objectManager = $this->createMockObjectManager($entity::class);

        // Act
        $this->hostListener->prePersistEntity($objectManager, $entity);

        // Assert
        $this->assertNotNull($entity->getCreatedInHost());
        $this->assertEquals(gethostname(), $entity->getCreatedInHost());
    }

    private function createMockEntityWithCreatedInHostColumn(): object
    {
        return new class() {
            #[CreatedInHostColumn]
            private ?string $createdInHost = null;

            public function getCreatedInHost(): ?string
            {
                return $this->createdInHost;
            }

            public function setCreatedInHost(?string $createdInHost): void
            {
                $this->createdInHost = $createdInHost;
            }
        };
    }

    private function createMockObjectManager(string $entityClass): ObjectManager&MockObject
    {
        $objectManager = $this->createMock(ObjectManager::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $reflectionClass = new ReflectionClass($entityClass);

        $classMetadata->method('getReflectionClass')
            ->willReturn($reflectionClass);

        $objectManager->method('getClassMetadata')
            ->with($entityClass)
            ->willReturn($classMetadata);

        return $objectManager;
    }

    public function test_preUpdate_withMockEntity_setsHostnameCorrectly(): void
    {
        // Arrange
        $entity = $this->createMockEntityWithUpdatedInHostColumn();
        $objectManager = $this->createMockObjectManager($entity::class);
        $eventArgs = $this->createMockPreUpdateEventArgs($entity, $objectManager);

        // Act
        $this->hostListener->preUpdateEntity($objectManager, $entity, $eventArgs);

        // Assert
        $this->assertNotNull($entity->getUpdatedInHost());
        $this->assertEquals(gethostname(), $entity->getUpdatedInHost());
    }

    private function createMockEntityWithUpdatedInHostColumn(): object
    {
        return new class() {
            #[UpdatedInHostColumn]
            private ?string $updatedInHost = null;

            public function getUpdatedInHost(): ?string
            {
                return $this->updatedInHost;
            }

            public function setUpdatedInHost(?string $updatedInHost): void
            {
                $this->updatedInHost = $updatedInHost;
            }
        };
    }

    private function createMockPreUpdateEventArgs(object $entity, ObjectManager $objectManager): PreUpdateEventArgs&MockObject
    {
        $eventArgs = $this->createMock(PreUpdateEventArgs::class);
        $eventArgs->method('getObject')->willReturn($entity);
        $eventArgs->method('getObjectManager')->willReturn($objectManager);

        return $eventArgs;
    }

    public function test_prePersist_withExistingValue_doesNotOverwrite(): void
    {
        // Arrange
        $entity = $this->createMockEntityWithCreatedInHostColumn();
        $predefinedHost = 'predefined.host';
        $entity->setCreatedInHost($predefinedHost);

        $objectManager = $this->createMockObjectManager($entity::class);

        // Act
        $this->hostListener->prePersistEntity($objectManager, $entity);

        // Assert
        $this->assertEquals($predefinedHost, $entity->getCreatedInHost());
        $this->assertNotEquals(gethostname(), $entity->getCreatedInHost());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->hostListener = static::getContainer()->get(HostListener::class);
    }
}
