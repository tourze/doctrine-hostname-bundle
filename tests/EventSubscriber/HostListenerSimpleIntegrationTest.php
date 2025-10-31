<?php

declare(strict_types=1);

namespace Tourze\DoctrineHostnameBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DoctrineHostnameBundle\Attribute\CreatedInHostColumn;
use Tourze\DoctrineHostnameBundle\Attribute\UpdatedInHostColumn;
use Tourze\DoctrineHostnameBundle\EventSubscriber\HostListener;
use Tourze\DoctrineHostnameBundle\Tests\Fixtures\CreatedInHostEntityInterface;
use Tourze\DoctrineHostnameBundle\Tests\Fixtures\MockClassMetadata;
use Tourze\DoctrineHostnameBundle\Tests\Fixtures\MockEntityManager;
use Tourze\DoctrineHostnameBundle\Tests\Fixtures\UpdatedInHostEntityInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(HostListener::class)]
#[RunTestsInSeparateProcesses]
final class HostListenerSimpleIntegrationTest extends AbstractEventSubscriberTestCase
{
    private HostListener $hostListener;

    /**
     * 创建一个实现了CreatedInHostEntityInterface的stub对象
     */
    private function createCreatedInHostEntityStub(?string $initialValue = null): CreatedInHostEntityInterface
    {
        return new class($initialValue) implements CreatedInHostEntityInterface {
            #[CreatedInHostColumn]
            private ?string $createdInHost = null;

            public function __construct(?string $initialValue = null)
            {
                $this->createdInHost = $initialValue;
            }

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

    /**
     * 创建一个实现了UpdatedInHostEntityInterface的stub对象
     */
    private function createUpdatedInHostEntityStub(?string $initialValue = null): UpdatedInHostEntityInterface
    {
        return new class($initialValue) implements UpdatedInHostEntityInterface {
            #[UpdatedInHostColumn]
            private ?string $updatedInHost = null;

            public function __construct(?string $initialValue = null)
            {
                $this->updatedInHost = $initialValue;
            }

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

    public function testHostListenerIsAutowiredCorrectly(): void
    {
        // Assert
        $this->assertNotNull($this->hostListener);
    }

    public function testPrePersistWithMockEntitySetsHostnameCorrectly(): void
    {
        // Arrange
        $entity = $this->createCreatedInHostEntityStub();
        $entityManager = $this->createMockEntityManager($entity);

        // Act
        $this->hostListener->prePersistEntity($entityManager, $entity);

        // Assert
        $this->assertNotNull($entity->getCreatedInHost());
        $this->assertEquals(gethostname(), $entity->getCreatedInHost());
    }

    private function createMockEntityManager(object $entity): EntityManagerInterface
    {
        $classMetadata = new MockClassMetadata($entity);

        return new MockEntityManager($classMetadata);
    }

    public function testPreUpdateWithMockEntitySetsHostnameCorrectly(): void
    {
        // Arrange
        $entity = $this->createUpdatedInHostEntityStub();
        $entityManager = $this->createMockEntityManager($entity);
        $eventArgs = $this->createMockPreUpdateEventArgs($entity, $entityManager);

        // Act
        $this->hostListener->preUpdateEntity($entityManager, $entity, $eventArgs);

        // Assert
        $this->assertNotNull($entity->getUpdatedInHost());
        $this->assertEquals(gethostname(), $entity->getUpdatedInHost());
    }

    private function createMockPreUpdateEventArgs(object $entity, EntityManagerInterface $entityManager): PreUpdateEventArgs
    {
        // PreUpdateEventArgs 需要 changeSet 参数，这里模拟一个实际的更改
        $changeSet = ['someField' => ['oldValue', 'newValue']];

        return new PreUpdateEventArgs($entity, $entityManager, $changeSet);
    }

    public function testPrePersistWithExistingValueDoesNotOverwrite(): void
    {
        // Arrange
        $entity = $this->createCreatedInHostEntityStub('predefined.host');
        $predefinedHost = 'predefined.host';

        $entityManager = $this->createMockEntityManager($entity);

        // Act
        $this->hostListener->prePersistEntity($entityManager, $entity);

        // Assert
        $this->assertEquals($predefinedHost, $entity->getCreatedInHost());
        $this->assertNotEquals(gethostname(), $entity->getCreatedInHost());
    }

    public function testPrePersistEntity(): void
    {
        // Arrange
        $entity = $this->createCreatedInHostEntityStub();
        $entityManager = $this->createMockEntityManager($entity);

        // Act
        $this->hostListener->prePersistEntity($entityManager, $entity);

        // Assert
        $this->assertNotNull($entity->getCreatedInHost());
        $this->assertEquals(gethostname(), $entity->getCreatedInHost());
    }

    public function testPreUpdateEntity(): void
    {
        // Arrange
        $entity = $this->createUpdatedInHostEntityStub();
        $entityManager = $this->createMockEntityManager($entity);
        $eventArgs = $this->createMockPreUpdateEventArgs($entity, $entityManager);

        // Act
        $this->hostListener->preUpdateEntity($entityManager, $entity, $eventArgs);

        // Assert
        $this->assertNotNull($entity->getUpdatedInHost());
        $this->assertEquals(gethostname(), $entity->getUpdatedInHost());
    }

    protected function onSetUp(): void
    {
        $this->hostListener = self::getService(HostListener::class);
    }
}
