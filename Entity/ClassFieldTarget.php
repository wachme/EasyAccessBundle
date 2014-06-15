<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClassFieldTarget
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ClassFieldTarget extends Target {
    /**
     * @var ClassTarget
     *
     * @ORM\ManyToOne(targetEntity="ClassTarget")
     * @ORM\JoinColumn(nullable=false)
     */
	private $class;
	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;
	
	public function setClass($class) {
	    $this->class = $class;
	}
	
	public function getClass() {
	    return $this->class;
	}
	
	public function setName($name) {
	    $this->name = $name;
	}
	
	public function getName() {
	    return $this->name;
	}
}