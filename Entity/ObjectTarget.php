<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ObjectTarget
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ObjectTarget extends Target {
    /**
     * @var ClassTarget
     * 
     * @ORM\ManyToOne(targetEntity="ClassTarget", inversedBy="objects")
     */
	private $class;
	/**
	 * @var integer
	 * 
	 * @ORM\Column(type="integer")
	 */
	private $identifier;
	/**
	 * @var ArrayCollection
	 * 
	 * @ORM\OneToMany(targetEntity="ObjectFieldTarget", mappedBy="object")
	 */
	private $fields;
	
	public function setClass($class) {
	    $this->class = $class;
	}
	
	public function getClass() {
	    return $this->class;
	}
	
	public function setIdentifier($identifier) {
	    $this->identifier = $identifier;
	}
	
	public function getIdentifier() {
	    return $this->identifier;
	}
	
	public function getFields() {
	    return $this->fields;
	}
}
