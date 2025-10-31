<?php

declare(strict_types=1);

namespace Tourze\DoctrineHostnameBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DoctrineHostnameBundle\DoctrineHostnameBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(DoctrineHostnameBundle::class)]
#[RunTestsInSeparateProcesses]
final class DoctrineHostnameBundleTest extends AbstractBundleTestCase
{
}
