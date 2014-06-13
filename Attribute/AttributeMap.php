<?php

namespace Wachme\Bundle\EasyAccessBundle\Attribute;

class AttributeMap {
    
    private $attributes = [
	   'view' =>     0b00000001,
	   'create' =>   0b00000010,
	   'edit' =>     0b00000100,
	   'delete' =>   0b00001000,
	   'undelete' => 0b00010000,
	   
	   'operator' => 0b00011111,
	   'master' =>   0b00111111,
	   'owner' =>    0b01111111
    ];
    
    /**
     * @param array $attributes
     * @return integer
     */
    public function getMask(array $attributes) {
        $mask = 0;
        foreach($attributes as $attr) {
            $attr = strtolower($attr);
            if(!isset($this->attributes[$attr]))
                throw new UnknownAttributeException($attr);
            $mask |= $this->attributes[$attr];
        }
        return $mask;
    }
}