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
     * @param string $class
     * @param string $field
     * @return TargetInterface
     */
    public function createClassField($class, $field);
    /**
     * @param object $object
     * @param string $field
     * @return TargetInterface
     */
    public function createObjectField($object, $field);
    /**
     * @param string $class
     * @return TargetInterface|null
     */
    public function findClass($class);
    /**
     * @param object $object
     * @return TargetInterface|null
     */
    public function findObject($object);
    /**
     * @param string $class
     * @param string $field
     * @return TargetInterface|null
     */
    public function findClassField($class, $field);
    /**
     * @param object $object
     * @param string $field
     * @return TargetInterface|null
     */
    public function findObjectField($object, $field);
    /**
     * @param string $class
     * @return TargetInterface
     */
    public function findOrCreateClass($class);
    /**
     * @param object $object
     * @return TargetInterface;
     */
    public function findOrCreateObject($object);
    /**
     * @param string $class
     * @param string $field
     * @return TargetInterface
     */
    public function findOrCreateClassField($class, $field);
    /**
     * @param object $object
     * @param string $field
     * @return TargetInterface
     */
    public function findOrCreateObjectField($object, $field);
    /**
     * @param string $class
     * @param object $user
     * @return TargetInterface|null
     */
    public function findClassSet($class, $user);
    /**
     * @param object $object
     * @param object $user
     * @return TargetInterface|null
     */
    public function findObjectSet($object, $user);
    /**
     * @param string $class
     * @param string $field
     * @param object $user
     * @return TargetInterface|null
     */
    public function findClassFieldSet($class, $field, $user);
    /**
     * @param object $object
     * @param string $field
     * @param object $user
     * @return TargetInterface|null
     */
    public function findObjectFieldSet($object, $field, $user);
}