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

use Doctrine\ODM\PHPCR\Event\LifecycleEventArgs;
use Doctrine\Common\Annotations\Reader;
use Metadata\MetadataFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;


class PHPCRDocumentListener
{

    protected $metadataFactory;
    protected $doctrine;

    public function __construct(MetadataFactoryInterface $metadataFactory, Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->metadataFactory = $metadataFactory;
    }


    public function prePersist(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();
        $classHierarchyMetadata = $this->metadataFactory->getMetadataForClass(get_class($document));
        $classMetadata = $classHierarchyMetadata->classMetadata[get_class($document)];

        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {

            switch($propertyMetadata->type){
                case 'dbal':

                    $em = $this->doctrine->getManager($propertyMetadata->targetManager);
                    // Copied following two lines from Doctrine\ORM\Mapping\ClassMetadataFactory
                    list($namespaceAlias, $simpleClassName) = explode(':', $propertyMetadata->targetObject);
                    $realClassName = $em->getConfiguration()->getEntityNamespace($namespaceAlias) . '\\' . $simpleClassName;

                    /**
                     * @var \Doctrine\Common\Persistence\Mapping\ClassMetadata $entityMetaData
                     */
                    $entityMetaData = $em->getClassMetadata($realClassName);
                    $entity = $propertyMetadata->getValue($document);

                    if(is_null($entity)){
                        continue;
                    }

                    $idValues = $entityMetaData->getIdentifierValues($entity);
                    //$idValues['uid'] = microtime().rand(0, 1000);
                    $propertyMetadata->setValue($document, serialize($idValues));

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
        $document = $args->getDocument();
        $classHierarchyMetadata = $this->metadataFactory->getMetadataForClass(get_class($document));

        $classMetadata = $classHierarchyMetadata->classMetadata[get_class($document)];


        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {

            /* @var $propertyMetadata \Netvlies\Bundle\DoctrineBridgeBundle\Mapping\PropertyMetadata */
            $reference = null;

            switch($propertyMetadata->type){
                case 'dbal':

                    $em = $this->doctrine->getManager($propertyMetadata->targetManager);
                    // Copied following two lines from Doctrine\ORM\Mapping\ClassMetadataFactory
                    list($namespaceAlias, $simpleClassName) = explode(':', $propertyMetadata->targetObject);
                    $realClassName = $em->getConfiguration()->getEntityNamespace($namespaceAlias) . '\\' . $simpleClassName;

                    /**
                     * @var \Doctrine\Common\Persistence\Mapping\ClassMetadata $entityMetaData
                     */
                    $entityMetaData = $em->getClassMetadata($realClassName);
                    $value = $propertyMetadata->getValue($document);

                    if(empty($value)){
                        continue;
                    }

                    // This means we only have support for simple relations pointing to one id.
                    $ids = unserialize($value);
                    $id = array_shift($ids);

                    $reference = $em->getReference($realClassName, $id);

                    break;
                case 'mongodb':
                    throw new \Exception('MongoDB is not yet implemented');
                    break;
                default:
                    break;
            }
            if(!is_null($reference)){
                $propertyMetadata->setValue($document, $reference);

            }
        }
    }
}
