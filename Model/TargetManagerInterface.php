<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Doctrine\ORM\QueryBuilder;

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
     * @param QueryBuilder $qb
     * @param string $class
     * @return array
     */
    public function selectClassSet(QueryBuilder $qb, $class);
    /**
     * @param QueryBuilder $qb
     * @param object $object
     * @return array
     */
    public function selectObjectSet(QueryBuilder $qb, $object);
    /**
     * @param QueryBuilder $qb
     * @param string $class
     * @param string $field
     * @return array
     */
    public function selectClassFieldSet(QueryBuilder $qb, $class, $field);
    /**
     * @param QueryBuilder $qb
     * @param object $object
     * @param string $field
     * @return array
     */
    public function selectObjectFieldSet(QueryBuilder $qb, $object, $field);
}