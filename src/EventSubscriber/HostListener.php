<?php

declare(strict_types=1);

namespace Tourze\DoctrineHostnameBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PropertyAccess\Exception\UninitializedPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\DoctrineEntityCheckerBundle\Checker\EntityCheckerInterface;
use Tourze\DoctrineHostnameBundle\Attribute\CreatedInHostColumn;
use Tourze\DoctrineHostnameBundle\Attribute\UpdatedInHostColumn;

#[AsDoctrineListener(event: Events::prePersist, priority: -99)]
#[AsDoctrineListener(event: Events::preUpdate, priority: -99)]
#[WithMonologChannel(channel: 'doctrine_hostname')]
readonly class HostListener implements EntityCheckerInterface
{
    public function __construct(
        #[Autowire(service: 'doctrine-host.property-accessor')] private PropertyAccessor $propertyAccessor,
        private LoggerInterface $logger,
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->prePersistEntity($args->getObjectManager(), $args->getObject());
    }

    public function prePersistEntity(ObjectManager $objectManager, object $entity): void
    {
        $reflection = $objectManager->getClassMetadata($entity::class)->getReflectionClass();
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            if ([] === $property->getAttributes(CreatedInHostColumn::class)) {
                continue;
            }

            try {
                $oldValue = $this->propertyAccessor->getValue($entity, $property->getName());
                if (null !== $oldValue && '' !== $oldValue && false !== $oldValue) {
                    continue;
                }
            } catch (UninitializedPropertyException $exception) {
                // The property "XXX\Entity\XXX::$createTime" is not readable because it is typed "DateTimeInterface". You should initialize it or declare a default value instead.
                // 跳过这个错误
            }

            $hostname = gethostname();
            $this->logger->debug('设置创建host', [
                'className' => get_class($entity),
                'entity' => $entity,
                'time' => $hostname,
                'property' => $property,
            ]);
            $this->propertyAccessor->setValue($entity, $property->getName(), $hostname);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        // 如果数据都没变化，那我们也没必要更新时间
        if ([] === $args->getEntityChangeSet()) {
            return;
        }
        $this->preUpdateEntity($args->getObjectManager(), $args->getObject(), $args);
    }

    public function preUpdateEntity(ObjectManager $objectManager, object $entity, PreUpdateEventArgs $eventArgs): void
    {
        $reflection = $objectManager->getClassMetadata($entity::class)->getReflectionClass();
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            if ([] === $property->getAttributes(UpdatedInHostColumn::class)) {
                continue;
            }
            $hostname = gethostname();
            $this->logger->debug('设置更新host', [
                'className' => get_class($entity),
                'entity' => $entity,
                'time' => $hostname,
            ]);
            $this->propertyAccessor->setValue($entity, $property->getName(), $hostname);
        }
    }
}
