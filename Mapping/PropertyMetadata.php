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

namespace Netvlies\Bundle\DoctrineBridgeBundle\Mapping;

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