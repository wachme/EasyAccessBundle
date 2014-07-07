<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ClassTarget
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ClassTarget extends Target {
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     */
	private $name;
	/**
	 * @var ArrayCollection
	 * 
	 * @ORM\OneToMany(targetEntity="ObjectTarget", mappedBy="class")
	 */
	private $objects;
	/**
	 * @var ArrayCollection
	 * 
	 * @ORM\OneToMany(targetEntity="ClassFieldTarget", mappedBy="class")
	 */
	private $fields;
	/**
	 * @param string $name
	 */
	public function setName($name) {
	    $this->name = $name;
	}
	/**
	 * @return string
	 */
	public function getName() {
	    return $this->name;
	}
	/**
	 * @return ArrayCollection
	 */
	public function getObjects() {
	    return $this->objects;
	}
	/**
	 * @return ArrayCollection
	 */
	public function getFields() {
	    return $this->fields;
	}
}