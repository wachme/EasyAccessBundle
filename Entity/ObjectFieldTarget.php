<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ObjectFieldTarget
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ObjectFieldTarget extends Target {
    /**
     * @var ObjectTarget
     * 
     * @ORM\ManyToOne(targetEntity="ObjectTarget")
     */
	private $object;
	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;
	
	public function setObject($object) {
	    $this->object = $object;
	}
	
	public function getObject() {
	    return $this->object;
	}
	
	public function setName($name) {
	    $this->name = $name;
	}
	
	public function getName() {
	    return $this->name;
	}
}