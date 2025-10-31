<?php

namespace Tourze\DoctrineHostnameBundle\Tests\Fixtures;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Cache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;

/**
 * Simplified EntityManager implementation for testing
 * Only implements the methods actually used by HostListener
 *
 * @internal
 */
final class MockEntityManager implements EntityManagerInterface
{
    private object $classMetadata;

    public function __construct(object $classMetadata)
    {
        $this->classMetadata = $classMetadata;
    }

    public function getClassMetadata($className): ClassMetadata
    {
        return $this->classMetadata;
    }

    // All other methods throw exceptions since they're not used
    public function getRepository(string $className): EntityRepository
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function find($entityName, $id, $lockMode = null, $lockVersion = null): ?object
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function persist($entity): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function remove($entity): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function merge($entity): object
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function clear($entityName = null): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function detach($entity): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function refresh(object $object, LockMode|int|null $lockMode = null): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function flush(): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getReference($entityName, $id): object
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getPartialReference($entityName, $identifier): object
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function close(): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function copy($entity, $deep = false): object
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function lock($entity, $lockMode, $lockVersion = null): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getEventManager(): EventManager
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getConfiguration(): Configuration
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function isOpen(): bool
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getUnitOfWork(): UnitOfWork
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getHydrator($hydrationMode): AbstractHydrator
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function newHydrator($hydrationMode): AbstractHydrator
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getProxyFactory(): ProxyFactory
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function initializeObject($obj): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function contains($entity): bool
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getCache(): ?Cache
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function createQuery($dql = ''): Query
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function createNamedQuery($name): Query
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function createNativeQuery($sql, ResultSetMapping $rsm): NativeQuery
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function createNamedNativeQuery($name): NativeQuery
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function createQueryBuilder(): QueryBuilder
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getConnection(): Connection
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getExpressionBuilder(): Expr
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function beginTransaction(): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function transactional($func): mixed
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function commit(): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function rollback(): void
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getMetadataFactory(): ClassMetadataFactory
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function hasFilters(): bool
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function getFilters(): FilterCollection
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function isFiltersStateClean(): bool
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function wrapInTransaction(callable $func): mixed
    {
        throw new \BadMethodCallException('Not implemented in test');
    }

    public function isUninitializedObject(mixed $obj): bool
    {
        throw new \BadMethodCallException('Not implemented in test');
    }
}
