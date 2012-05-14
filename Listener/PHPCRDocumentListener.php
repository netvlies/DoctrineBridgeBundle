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

class PHPCRDocumentListener
{

    protected $metadataFactory;

    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();
        $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($document));

        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            /* @var $propertyMetadata \Matthias\AnnotationBundle\Metadata\PropertyMetadata */

            switch($propertyMetadata->type){
                case 'dbal':
                    //@todo use entity manager name
                    //@todo how to convert short notation to classname
                    $this->getDoctrine()->getEntityManager()->getProxyFactory()->getProxy( , 1);
                    break;
                case 'phpcr':

                    break;
                default:
                    break;
            }
            $propertyMetadata->setValue($object, $propertyMetadata->defaultValue);
        }
    }

}
