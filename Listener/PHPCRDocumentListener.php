<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 5/13/12
 * Time: 9:48 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\DoctrineStorageBridgeBundle\Listener;

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

                    //@todo use entity manager name

                    $em = $this->doctrine->getManager();
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
                    $propertyMetadata->setValue($document, serialize($idValues));

                    break;
                case 'mongodb':

                    break;
                default:
                    break;
            }
        }
    }


    public function postLoad(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();
        $classHierarchyMetadata = $this->metadataFactory->getMetadataForClass(get_class($document));

        $classMetadata = $classHierarchyMetadata->classMetadata[get_class($document)];


        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {

            /* @var $propertyMetadata \Netvlies\Bundle\DoctrineStorageBridgeBundle\Mapping\PropertyMetadata */
            $reference = null;

            switch($propertyMetadata->type){
                case 'dbal':

                    //@todo use entity manager name

                    $em = $this->doctrine->getManager();
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

                    // This means we only have support for simple relations pointing to one id. Combined keys are rare indeed
                    // but impossible for now
                    $ids = unserialize($value);
                    $id = array_shift($ids);

                    $reference = $em->getReference($realClassName, $id);

                    break;
                case 'mongodb':

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
