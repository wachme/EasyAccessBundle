<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;

interface TargetManagerInterface {
    /**
     * @param string $class
     * @return TargetInterface
     */
    public function createClass($class);
    /**
     * @param object $object
     * @return TargetInterface;
     */
    public function createObject($object);
    /**
     * 
     * @param string $class
     * @param string $field
     * @return TargetInterface
     */
    public function createClassField($class, $field);
    /**
     *
     * @param object $object
     * @param string $field
     * @return TargetInterface
     */
    public function createObjectField($object, $field);
    /**
     * @param string $class
     * @param boolean $recursive 
     * @return TargetInterface|null
     */
    public function findByClass($class, $recursive=true);
    /**
     * @param object $object
     * @param boolean $recursive 
     * @return TargetInterface|null
     */
    public function findByObject($object, $recursive=true);
    /**
     * @param string $class
     * @param string $field
     * @param boolean $recursive 
     * @return TargetInterface|null
     */
    public function findByClassField($class, $field, $recursive=true);
    /**
     * @param object $object
     * @param string $field
     * @param boolean $recursive 
     * @return TargetInterface|null
     */
    public function findByObjectField($object, $field, $recursive=true);
}