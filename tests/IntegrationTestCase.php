<?php

namespace Tourze\DoctrineHostnameBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Tourze\DoctrineHostnameBundle\DoctrineHostnameBundle;
use Tourze\IntegrationTestKernel\IntegrationTestKernel;

abstract class IntegrationTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected static function createKernel(array $options = []): KernelInterface
    {
        $env = $options['environment'] ?? $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'test';
        $debug = $options['debug'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? true;

        return new IntegrationTestKernel($env, $debug, [
            DoctrineHostnameBundle::class => ['all' => true],
        ]);
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->createDatabaseSchema();
        $this->cleanDatabase();
    }

    protected function createDatabaseSchema(): void
    {
        $connection = $this->entityManager->getConnection();

        // 创建测试表
        $sql = "
            CREATE TABLE IF NOT EXISTS test_entity (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                created_in_host VARCHAR(255) NULL,
                updated_in_host VARCHAR(255) NULL
            )
        ";

        $connection->executeStatement($sql);
    }

    protected function cleanDatabase(): void
    {
        $connection = $this->entityManager->getConnection();

        // 如果表存在则清理数据
        try {
            $connection->executeStatement('DELETE FROM test_entity');
        } catch (\Exception $e) {
            // 表不存在时忽略错误
        }
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
        self::ensureKernelShutdown();
        parent::tearDown();
    }
}
