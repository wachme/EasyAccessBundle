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
	/**
	 * @param ClassTarget $class
	 */
	public function setClass(ClassTarget $class) {
	    $this->class = $class;
	}
	/**
	 * @return ClassTarget
	 */
	public function getClass() {
	    return $this->class;
	}
	/**
	 * @param integer $identifier
	 */
	public function setIdentifier($identifier) {
	    $this->identifier = $identifier;
	}
	/**
	 * @return integer
	 */
	public function getIdentifier() {
	    return $this->identifier;
	}
	/**
	 * @return ArrayCollection
	 */
	public function getFields() {
	    return $this->fields;
	}
}
