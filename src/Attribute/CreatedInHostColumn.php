<?php

declare(strict_types=1);

namespace Tourze\DoctrineHostnameBundle\Attribute;

/**
 * 记录创建时hostname
 */
#[\Attribute(flags: \Attribute::TARGET_PROPERTY)]
class CreatedInHostColumn
{
}
