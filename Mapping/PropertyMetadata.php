<?php

namespace Netvlies\Bundle\DoctrineStorageBridgeBundle\Mapping;

use Metadata\PropertyMetadata as BasePropertyMetadata;

class PropertyMetadata extends BasePropertyMetadata
{
    public $targetObject;
    public $targetManager;
    public $type;
}