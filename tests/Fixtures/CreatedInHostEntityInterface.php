<?php

namespace Tourze\DoctrineHostnameBundle\Tests\Fixtures;

/**
 * Interface for test entities that have CreatedInHost functionality
 *
 * @internal
 */
interface CreatedInHostEntityInterface
{
    public function getCreatedInHost(): ?string;

    public function setCreatedInHost(?string $createdInHost): void;
}
