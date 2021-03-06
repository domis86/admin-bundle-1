<?php

declare(strict_types=1);

namespace AdminPanel\Component\DataSource\Driver\Doctrine\Extension\Core\Field;

use AdminPanel\Component\DataSource\Driver\Doctrine\DoctrineFieldInterface;
use AdminPanel\Component\DataSource\Driver\Doctrine\ORM\Extension\Core\Field\Boolean as BaseField;

/**
 * Boolean field.
 * @deprecated since version 1.2
 */
class Boolean extends BaseField implements DoctrineFieldInterface
{
}
