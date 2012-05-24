Netvlies DoctrineBridge
========================

This bundle provides a bridge functionality between different doctrine managers. The bridge functionality makes it possible to make interlinked relations between an entity and a document (and vice versa) using annotations. An example for a document:


    /**
     *
     * @PHPCRODM\Document(referenceable=true)
     */
    class MyPage
    {
         * @PHPCRODM\String
         * @BRIDGE\Entity(targetEntity="MyBundle:Media", entityManager="default")
         */
        protected $media;


And vice versa

    /**
     * @ORM\Table()
     * @ORM\Entity
     */
    class OrderLine
    {
         * @ORM\Column(name="product", type="string", length=255)
         * @BRIDGE\Document(targetDocument="MyBundle:Product", documentManager="default")
         */
        private $product;


Using this annotation, it will load a reference object when retrieved, and will be resolved from storage engine when asked for. Also when assigned and saved it will store the UID from the other side, so it can be resolved when retrieved again.

# TODO

* Currently it only supports x-to-one relations. So this is must be added later on to have at least support for one-to-many relations.
* Currently it has only support ORM and PHPCR. So support for mongoDB and/or other storage engines might be added in the future.


# Installation

You can either use composer or include it into your deps file.

Either way you need to add following line to your autoload.php and change the path accordingly (if needed)

    AnnotationRegistry::registerFile(__DIR__.'/../vendor/netvlies/doctrinebridge/Netvlies/Bundle/DoctrineBridgeBundle/Mapping/Annotations/DoctrineAnnotations.php');

And dont't forget it to include into your AppKernel.php

    new Netvlies\Bundle\DoctrineBridgeBundle\NetvliesDoctrineBridgeBundle(),


