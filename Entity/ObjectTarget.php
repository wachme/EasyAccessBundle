<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="ClassTarget")
     * @ORM\JoinColumn(nullable=false)
     */
	private $class;
	/**
	 * @var integer
	 * 
	 * @ORM\Column(type="integer")
	 */
	private $identifier;
	
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
}
