<?php

namespace Tourze\DoctrineHostnameBundle\Tests\Fixtures;

/**
 * Interface for test entities that have UpdatedInHost functionality
 *
 * @internal
 */
interface UpdatedInHostEntityInterface
{
    public function getUpdatedInHost(): ?string;

    public function setUpdatedInHost(?string $updatedInHost): void;
}
