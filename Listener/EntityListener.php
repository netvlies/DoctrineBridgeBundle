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

namespace Netvlies\Bundle\DoctrineBridgeBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Annotations\Reader;
use Metadata\MetadataFactoryInterface;
use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;

class EntityListener
{
    protected $metadataFactory;
    protected $doctrine;

    public function __construct(MetadataFactoryInterface $metadataFactory, ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->metadataFactory = $metadataFactory;
    }


    public function prePersist(LifecycleEventArgs $args)
    {

        $entity = $args->getEntity();
        $classHierarchyMetadata = $this->metadataFactory->getMetadataForClass(get_class($entity));
        $classMetadata = $classHierarchyMetadata->classMetadata[get_class($entity)];

        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {

            switch($propertyMetadata->type){
                case 'odm':

                    $dm = $this->doctrine->getManager($propertyMetadata->targetManager);
                    // Copied following two lines from Doctrine\ORM\Mapping\ClassMetadataFactory

                    list($namespaceAlias, $simpleClassName) = explode(':', $propertyMetadata->targetObject);
                    $realClassName = $dm->getConfiguration()->getDocumentNamespace($namespaceAlias) . '\\' . $simpleClassName;

                    /**
                     * @var \Doctrine\Common\Persistence\Mapping\ClassMetadata $entityMetaData
                     */
                    $documentMetaData = $dm->getClassMetadata($realClassName);
                    $document = $propertyMetadata->getValue($entity);

                    if(is_null($document)){
                        continue;
                    }

                    $idValues = $documentMetaData->getIdentifierValues($document);
                    $propertyMetadata->setValue($entity, serialize($idValues));

                    break;
                case 'mongodb':
                    throw new \Exception('MongoDB is not yet implemented');
                    break;
                default:
                    break;
            }
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->prePersist($args);
    }


    public function postLoad(LifecycleEventArgs $args)
    {

        $entity = $args->getEntity();
        $classHierarchyMetadata = $this->metadataFactory->getMetadataForClass(get_class($entity));

        $classMetadata = $classHierarchyMetadata->classMetadata[get_class($entity)];



        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {

            /* @var $propertyMetadata \Netvlies\Bundle\DoctrineBridgeBundle\Mapping\PropertyMetadata */
            $reference = null;

            switch($propertyMetadata->type){
                case 'odm':

                    $dm = $this->doctrine->getManager($propertyMetadata->targetManager);

                    // Copied following two lines from Doctrine\ORM\Mapping\ClassMetadataFactory
                    list($namespaceAlias, $simpleClassName) = explode(':', $propertyMetadata->targetObject);
                    $realClassName = $dm->getConfiguration()->getDocumentNamespace($namespaceAlias) . '\\' . $simpleClassName;

                    /**
                     * @var \Doctrine\Common\Persistence\Mapping\ClassMetadata $entityMetaData
                     */
                    $documentMetaData = $dm->getClassMetadata($realClassName);
                    $value = $propertyMetadata->getValue($entity);

                    if(empty($value)){
                        continue;
                    }

                    // This means we only have support for simple relations pointing to one id.
                    $ids = unserialize($value);
                    $id = array_shift($ids);

                    $reference = $dm->getReference($realClassName, $id);
                    break;
                case 'mongodb':
                    throw new \Exception('MongoDB is not yet implemented');
                    break;
                default:
                    break;
            }

            if(!is_null($reference)){
                $propertyMetadata->setValue($entity, $reference);
            }
        }
    }

}
