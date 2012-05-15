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

    }


    public function postLoad(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();
        $classHierarchyMetadata = $this->metadataFactory->getMetadataForClass(get_class($document));

        $classMetadata = $classHierarchyMetadata->classMetadata[get_class($document)];


        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {

            /* @var $propertyMetadata \Netvlies\Bundle\DoctrineStorageBridgeBundle\Mapping\PropertyMetadata */
            $proxy = null;

            switch($propertyMetadata->type){
                case 'dbaal':
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

                    $entityMetaData->getIdentifierFieldNames();
                    exit;


                    $proxy = $em->getProxyFactory()->getProxy($realClassName, array('id' => 1));

                    break;
                case 'mongodb':

                    break;
                default:
                    break;
            }
            if(!is_null($proxy)){
                $propertyMetadata->setValue($document, $proxy);

            }
        }

    }

}
