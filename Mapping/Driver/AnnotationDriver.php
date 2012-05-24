<?php
/*
 * This file is part of the Netvlies DoctrineBridgeBundle
 *
 * (c) Netvlies Internetdiensten
 * author: M. de Krijger <mdekrijger@netvlies.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netvlies\Bundle\DoctrineBridgeBundle\Mapping\Driver;

use Metadata\Driver\DriverInterface;
use Metadata\ClassMetadata;
use Doctrine\Common\Annotations\Reader;
use Netvlies\Bundle\DoctrineBridgeBundle\Mapping\PropertyMetadata;
use Netvlies\Bundle\DoctrineBridgeBundle\Mapping\Annotations as BRIDGE;


class AnnotationDriver implements DriverInterface
{

    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new ClassMetadata($class->getName());

        foreach ($class->getProperties() as $reflectionProperty) {


            foreach ($this->reader->getPropertyAnnotations($reflectionProperty) as $fieldAnnot) {
                $propertyMetadata = new PropertyMetadata($class->getName(), $reflectionProperty->getName());

                if ($fieldAnnot instanceof BRIDGE\Entity) {
                    /**
                     * @var BRIDGE\Entity $fieldAnnot
                     */
                    $propertyMetadata->targetObject = $fieldAnnot->targetEntity;
                    $propertyMetadata->targetManager = $fieldAnnot->entityManager;
                    $propertyMetadata->type = 'dbal';
                    $classMetadata->addPropertyMetadata($propertyMetadata);
                }

                if ($fieldAnnot instanceof BRIDGE\Document) {
                    /**
                     * @var BRIDGE\Document $fieldAnnot
                     */
                    $propertyMetadata->targetObject = $fieldAnnot->targetDocument;
                    $propertyMetadata->targetManager = $fieldAnnot->documentManager;
                    $propertyMetadata->type = 'odm';
                    $classMetadata->addPropertyMetadata($propertyMetadata);
                }
            }
        }
        return $classMetadata;
    }
}
