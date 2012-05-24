<?php
/*
 * This file is part of the Netvlies DoctrineBridgeBundle
 *
 * (c) Netvlies Internetdiensten
 * author: M. de Krijger <mdekrijger@netvlies.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netvlies\Bundle\DoctrineBridgeBundle\Mapping\Annotations;

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
    public $entityManager='default';
}

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Document
{
    /** @var string */
    public $targetDocument;
    /** @var string */
    public $documentManager='default';
}