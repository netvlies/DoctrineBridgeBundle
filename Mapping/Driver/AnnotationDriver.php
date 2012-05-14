<?php

namespace Netvlies\Bundle\DoctrineStorageBridgeBundle\Mapping\Driver;

use Metadata\Driver\DriverInterface;
use Metadata\ClassMetadata;
use Doctrine\Common\Annotations\Reader;
use Netvlies\Bundle\DoctrineStorageBridgeBundle\Mapping\PropertyMetadata;
use Netvlies\Bundle\DoctrineStorageBridgeBundle\Mapping\Annotations as BRIDGE;


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
            $propertyMetadata = new PropertyMetadata($class->getName(), $reflectionProperty->getName());

            foreach ($this->reader->getPropertyAnnotations($reflectionProperty) as $fieldAnnot) {
                if ($fieldAnnot instanceof BRIDGE\Entity) {
                    /**
                     * @var BRIDGE\Entity $fieldAnnot
                     */
                    $propertyMetadata->targetObject = $fieldAnnot->targetEntity;
                    $propertyMetadata->targetManager = $fieldAnnot->entityManager;
                    $propertyMetadata->type = 'dbal';
                }
            }


            $classMetadata->addPropertyMetadata($propertyMetadata);

        }

        return $classMetadata;
    }
}
