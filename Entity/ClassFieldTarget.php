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
     * @ORM\ManyToOne(targetEntity="ClassTarget", inversedBy="fields")
     */
	private $class;
	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;
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
}