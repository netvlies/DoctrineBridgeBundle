<?php

namespace Netvlies\Bundle\DoctrineStorageBridgeBundle\Mapping\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Entity
{
    /** @var string */
    public $targetEntity;
    /** @var string */
    public $entityManager;
}
