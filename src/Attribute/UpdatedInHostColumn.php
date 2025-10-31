<?php

declare(strict_types=1);

namespace Tourze\DoctrineHostnameBundle\Attribute;

/**
 * 记录修改时hostname
 */
#[\Attribute(flags: \Attribute::TARGET_PROPERTY)]
class UpdatedInHostColumn
{
}
