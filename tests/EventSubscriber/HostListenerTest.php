<?php

namespace Tourze\DoctrineHostnameBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\DoctrineHostnameBundle\Attribute\CreatedInHostColumn;
use Tourze\DoctrineHostnameBundle\Attribute\UpdatedInHostColumn;
use Tourze\DoctrineHostnameBundle\EventSubscriber\HostListener;

class HostListenerTest extends TestCase
{
    private PropertyAccessor $propertyAccessor;
    private LoggerInterface $logger;
    private HostListener $hostListener;
    private EntityManagerInterface $objectManager;
    private ClassMetadata $classMetadata;

    protected function setUp(): void
    {
        $this->propertyAccessor = new PropertyAccessor();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->hostListener = new HostListener($this->propertyAccessor, $this->logger);
        $this->objectManager = $this->createMock(EntityManagerInterface::class);
        $this->classMetadata = $this->createMock(ClassMetadata::class);

        $this->objectManager->method('getClassMetadata')->willReturn($this->classMetadata);
    }

    private function createEntityWithProperties(array $propertiesConfig): object
    {
        $entity = new class() {};
        $reflectionClass = new ReflectionClass($entity);

        foreach ($propertiesConfig as $propName => $attributesAndValue) {
            if (!$reflectionClass->hasProperty($propName)) {
                $reflectionProperty = $reflectionClass->getProperty($propName) ?? new class($propName) extends ReflectionProperty { public function __construct(string $name) { parent::__construct(self::class, $name); } }; // Simplified mock
                // This is a simplified way to add properties dynamically for testing, real entities would be defined classes.
            }
            // For testing purposes, we assume properties exist or can be mocked.
            // Actual entity properties would be defined with attributes.
        }
        // This part needs a more robust way to simulate entities with attributes for testing.
        // For now, we'll focus on the listener's logic assuming reflection works as expected.
        return $entity;
    }

    // Test prePersist with CreatedInHostColumn
    public function testPrePersist_withCreatedInHostColumn_setsHostnameWhenNull(): void
    {
        $entity = new class() {
            #[CreatedInHostColumn]
            private ?string $createdHost = null;
            public function getCreatedHost(): ?string { return $this->createdHost; }
        };

        $reflectionClassMock = $this->createMock(ReflectionClass::class);
        $reflectionPropertyMock = $this->createMock(ReflectionProperty::class);

        $this->classMetadata->method('getReflectionClass')->willReturn($reflectionClassMock);
        $reflectionClassMock->method('getProperties')->with(\ReflectionProperty::IS_PRIVATE)->willReturn([$reflectionPropertyMock]);
        $reflectionPropertyMock->method('getAttributes')->with(CreatedInHostColumn::class)->willReturn([$this->createMock(CreatedInHostColumn::class)]); // Simulate attribute presence
        $reflectionPropertyMock->method('getName')->willReturn('createdHost');

        $eventArgs = new PrePersistEventArgs($entity, $this->objectManager);
        $this->hostListener->prePersist($eventArgs);

        $this->assertNotNull($entity->getCreatedHost());
        $this->assertEquals(gethostname(), $entity->getCreatedHost());
    }

    public function testPrePersist_withCreatedInHostColumn_doesNotOverwriteExistingValue(): void
    {
        $initialHostname = 'initial.host';
        $entity = new class($initialHostname) {
            #[CreatedInHostColumn]
            private ?string $createdHost;
            public function __construct(string $host) { $this->createdHost = $host; }
            public function getCreatedHost(): ?string { return $this->createdHost; }
        };

        $reflectionClassMock = $this->createMock(ReflectionClass::class);
        $reflectionPropertyMock = $this->createMock(ReflectionProperty::class);

        $this->classMetadata->method('getReflectionClass')->willReturn($reflectionClassMock);
        $reflectionClassMock->method('getProperties')->with(\ReflectionProperty::IS_PRIVATE)->willReturn([$reflectionPropertyMock]);
        $reflectionPropertyMock->method('getAttributes')->with(CreatedInHostColumn::class)->willReturn([$this->createMock(CreatedInHostColumn::class)]);
        $reflectionPropertyMock->method('getName')->willReturn('createdHost');
        
        // Simulate PropertyAccessor behavior for existing value
        // $this->propertyAccessor->method('getValue')->willReturn($initialHostname); // This requires mocking PropertyAccessor if it's not a real one

        $eventArgs = new PrePersistEventArgs($entity, $this->objectManager);
        $this->hostListener->prePersist($eventArgs);

        $this->assertEquals($initialHostname, $entity->getCreatedHost());
    }

    public function testPrePersist_withoutCreatedInHostColumn_doesNothing(): void
    {
        $entity = new class() {
            private ?string $someOtherProperty = null;
            public function getSomeOtherProperty(): ?string { return $this->someOtherProperty; }
        };

        $reflectionClassMock = $this->createMock(ReflectionClass::class);
        $reflectionPropertyMock = $this->createMock(ReflectionProperty::class);

        $this->classMetadata->method('getReflectionClass')->willReturn($reflectionClassMock);
        $reflectionClassMock->method('getProperties')->with(\ReflectionProperty::IS_PRIVATE)->willReturn([$reflectionPropertyMock]);
        $reflectionPropertyMock->method('getAttributes')->with(CreatedInHostColumn::class)->willReturn([]); // Simulate attribute absence
        $reflectionPropertyMock->method('getName')->willReturn('someOtherProperty');

        $eventArgs = new PrePersistEventArgs($entity, $this->objectManager);
        $this->hostListener->prePersist($eventArgs);

        $this->assertNull($entity->getSomeOtherProperty());
    }
    
    public function testPrePersist_logsWhenHostnameIsSet(): void
    {
        $entity = new class() {
            #[CreatedInHostColumn]
            private ?string $createdHost = null;
        };

        $reflectionClassMock = $this->createMock(ReflectionClass::class);
        $reflectionPropertyMock = $this->createMock(ReflectionProperty::class);

        $this->classMetadata->method('getReflectionClass')->willReturn($reflectionClassMock);
        $reflectionClassMock->method('getProperties')->with(\ReflectionProperty::IS_PRIVATE)->willReturn([$reflectionPropertyMock]);
        $reflectionPropertyMock->method('getAttributes')->with(CreatedInHostColumn::class)->willReturn([$this->createMock(CreatedInHostColumn::class)]);
        $reflectionPropertyMock->method('getName')->willReturn('createdHost');

        $this->logger->expects($this->once())->method('debug')->with(
            '设置创建host',
            $this->callback(function ($context) use ($entity, $reflectionPropertyMock) {
                return $context['className'] === get_class($entity) &&
                       $context['entity'] === $entity &&
                       $context['time'] === gethostname() &&
                       $context['property'] === $reflectionPropertyMock;
            })
        );

        $eventArgs = new PrePersistEventArgs($entity, $this->objectManager);
        $this->hostListener->prePersist($eventArgs);
    }

    // Test preUpdate with UpdatedInHostColumn
    public function testPreUpdate_withUpdatedInHostColumn_setsHostnameWhenChangesExist(): void
    {
        $entity = new class() {
            #[UpdatedInHostColumn]
            private ?string $updatedHost = null;
            public function getUpdatedHost(): ?string { return $this->updatedHost; }
        };

        $reflectionClassMock = $this->createMock(ReflectionClass::class);
        $reflectionPropertyMock = $this->createMock(ReflectionProperty::class);

        $this->classMetadata->method('getReflectionClass')->willReturn($reflectionClassMock);
        $reflectionClassMock->method('getProperties')->with(\ReflectionProperty::IS_PRIVATE)->willReturn([$reflectionPropertyMock]);
        $reflectionPropertyMock->method('getAttributes')->with(UpdatedInHostColumn::class)->willReturn([$this->createMock(UpdatedInHostColumn::class)]);
        $reflectionPropertyMock->method('getName')->willReturn('updatedHost');

        $changeSet = ['someField' => ['oldValue', 'newValue']];
        $eventArgs = new PreUpdateEventArgs($entity, $this->objectManager, $changeSet);
        $this->hostListener->preUpdate($eventArgs);

        $this->assertNotNull($entity->getUpdatedHost());
        $this->assertEquals(gethostname(), $entity->getUpdatedHost());
    }

    public function testPreUpdate_withUpdatedInHostColumn_doesNothingWhenNoChanges(): void
    {
        $entity = new class() {
            #[UpdatedInHostColumn]
            private ?string $updatedHost = 'initial.host';
            public function getUpdatedHost(): ?string { return $this->updatedHost; }
        };

        // No reflection mocks needed here as preUpdate should return early if changeset is empty
        
        $changeSet = []; // Empty changeset
        $eventArgs = new PreUpdateEventArgs($entity, $this->objectManager, $changeSet);
        $this->hostListener->preUpdate($eventArgs);

        $this->assertEquals('initial.host', $entity->getUpdatedHost());
    }

    public function testPreUpdate_withoutUpdatedInHostColumn_doesNothing(): void
    {
        $entity = new class() {
            private ?string $someOtherProperty = null;
        };

        $reflectionClassMock = $this->createMock(ReflectionClass::class);
        $reflectionPropertyMock = $this->createMock(ReflectionProperty::class);

        $this->classMetadata->method('getReflectionClass')->willReturn($reflectionClassMock);
        $reflectionClassMock->method('getProperties')->with(\ReflectionProperty::IS_PRIVATE)->willReturn([$reflectionPropertyMock]);
        $reflectionPropertyMock->method('getAttributes')->with(UpdatedInHostColumn::class)->willReturn([]); // Attribute not present
        $reflectionPropertyMock->method('getName')->willReturn('someOtherProperty');

        $changeSet = ['someField' => ['oldValue', 'newValue']];
        $eventArgs = new PreUpdateEventArgs($entity, $this->objectManager, $changeSet);
        $this->hostListener->preUpdate($eventArgs);

        $this->assertNull($this->propertyAccessor->getValue($entity, 'someOtherProperty')); // Assuming it's accessible for assertion
    }
    
    public function testPreUpdate_logsWhenHostnameIsSet(): void
    {
        $entity = new class() {
            #[UpdatedInHostColumn]
            private ?string $updatedHost = null;
        };

        $reflectionClassMock = $this->createMock(ReflectionClass::class);
        $reflectionPropertyMock = $this->createMock(ReflectionProperty::class);

        $this->classMetadata->method('getReflectionClass')->willReturn($reflectionClassMock);
        $reflectionClassMock->method('getProperties')->with(\ReflectionProperty::IS_PRIVATE)->willReturn([$reflectionPropertyMock]);
        $reflectionPropertyMock->method('getAttributes')->with(UpdatedInHostColumn::class)->willReturn([$this->createMock(UpdatedInHostColumn::class)]);
        $reflectionPropertyMock->method('getName')->willReturn('updatedHost');

        $this->logger->expects($this->once())->method('debug')->with(
            '设置更新host',
            $this->callback(function ($context) use ($entity) {
                return $context['className'] === get_class($entity) &&
                       $context['entity'] === $entity &&
                       $context['time'] === gethostname();
            })
        );
        
        $changeSet = ['someField' => ['oldValue', 'newValue']];
        $eventArgs = new PreUpdateEventArgs($entity, $this->objectManager, $changeSet);
        $this->hostListener->preUpdate($eventArgs);
    }
}