<?php

namespace Tourze\DoctrineHostnameBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;
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
    private LoggerInterface&MockObject $logger;
    private HostListener $hostListener;
    private EntityManagerInterface&MockObject $objectManager;
    private ClassMetadata&MockObject $classMetadata;

    protected function setUp(): void
    {
        $this->propertyAccessor = new PropertyAccessor();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->hostListener = new HostListener($this->propertyAccessor, $this->logger);
        $this->objectManager = $this->createMock(EntityManagerInterface::class);
        $this->classMetadata = $this->createPartialMock(ClassMetadata::class, ['getReflectionClass']);

        $this->objectManager->method('getClassMetadata')->willReturn($this->classMetadata);
    }


    // Test prePersist with CreatedInHostColumn
    public function testPrePersist_withCreatedInHostColumn_setsHostnameWhenNull(): void
    {
        $entity = new class() {
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
            
            public function getSomeOtherProperty(): ?string 
            { 
                return $this->someOtherProperty; 
            }
            
            public function setSomeOtherProperty(?string $someOtherProperty): void 
            { 
                $this->someOtherProperty = $someOtherProperty; 
            }
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
            
            public function getCreatedHost(): ?string 
            { 
                return $this->createdHost; 
            }
            
            public function setCreatedHost(?string $createdHost): void 
            { 
                $this->createdHost = $createdHost; 
            }
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
            
            public function getUpdatedHost(): ?string 
            { 
                return $this->updatedHost; 
            }
            
            public function setUpdatedHost(?string $updatedHost): void 
            { 
                $this->updatedHost = $updatedHost; 
            }
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
            
            public function getUpdatedHost(): ?string 
            { 
                return $this->updatedHost; 
            }
            
            public function setUpdatedHost(?string $updatedHost): void 
            { 
                $this->updatedHost = $updatedHost; 
            }
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
            
            public function getSomeOtherProperty(): ?string 
            { 
                return $this->someOtherProperty; 
            }
            
            public function setSomeOtherProperty(?string $someOtherProperty): void 
            { 
                $this->someOtherProperty = $someOtherProperty; 
            }
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

        $this->assertNull($entity->getSomeOtherProperty()); // 直接使用 getter 方法
    }
    
    public function testPreUpdate_logsWhenHostnameIsSet(): void
    {
        $entity = new class() {
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
