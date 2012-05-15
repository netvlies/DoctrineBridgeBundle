<?php

namespace Netvlies\Bundle\DoctrineStorageBridgeBundle\Mapping;

use Metadata\PropertyMetadata as BasePropertyMetadata;

class PropertyMetadata extends BasePropertyMetadata
{
    public $targetObject;
    public $targetManager;
    public $type;


    public function serialize()
    {
        return serialize(array(
            $this->class,
            $this->name,
            $this->targetObject,
            $this->targetManager,
            $this->type
        ));
    }

    public function unserialize($str)
    {
        list($this->class, $this->name, $this->targetObject, $this->targetManager, $this->type) = unserialize($str);

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}