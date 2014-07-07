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
     * @ORM\ManyToOne(targetEntity="ObjectTarget", inversedBy="fields")
     */
	private $object;
	/**
	 * @var ClassFieldTarget
	 * 
	 * @ORM\ManyToOne(targetEntity="ClassFieldTarget")
	 */
	private $classField;
	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;
	/**
	 * @param ObjectTarget $object
	 */
	public function setObject(ObjectTarget $object) {
	    $this->object = $object;
	}
	/**
	 * @return ObjectTarget
	 */
	public function getObject() {
	    return $this->object;
	}
	/**
	 * @param ClassFieldTarget $classField
	 */
	public function setClassField(ClassFieldTarget $classField) {
	    $this->classField = $classField;
	}
	/**
	 * @return ClassFieldTarget
	 */
	public function getClassField() {
	    return $this->classField;
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