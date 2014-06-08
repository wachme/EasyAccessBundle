<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Wachme\Bundle\EasyAccessBundle\Entity\Target;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\FieldTarget;

interface TargetManagerInterface {
    /**
     * @param string $class
     * @return ClassTarget
     */
    public function createClass($class);
    /**
     * @param object $object
     * @return ObjectTarget;
     */
    public function createObject($object);
    /**
     * 
     * @param string $class
     * @param string $field
     * @return FieldTarget
     */
    public function createClassField($class, $field);
    /**
     *
     * @param object $object
     * @param string $field
     * @return FieldTarget
     */
    public function createObjectField($object, $field);
    /**
     * @param string $class
     * @param boolean $recursive 
     * @return Target|null
     */
    public function findByClass($class, $recursive=true);
    /**
     * @param object $object
     * @param boolean $recursive 
     * @return ObjectTarget|null
     */
    public function findByObject($object, $recursive=true);
    /**
     * @param string $class
     * @param string $field
     * @param boolean $recursive 
     * @return Target|null
     */
    public function findByClassField($class, $field, $recursive=true);
    /**
     * @param object $object
     * @param string $field
     * @param boolean $recursive 
     * @return Target|null
     */
    public function findByObjectField($object, $field, $recursive=true);
    /**
     * @return void
     */
    public function save();
}