<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity;
use Wachme\Bundle\EasyAccessBundle\Entity\Target;

/**
 * Manages target entities in database
 */
class TargetManager implements TargetManagerInterface {    
    
    private static $targetClass = 'Wachme\Bundle\EasyAccessBundle\Entity\Target';
    private static $classTargetClass = 'Wachme\Bundle\EasyAccessBundle\Entity\ClassTarget';
    private static $objectTargetClass = 'Wachme\Bundle\EasyAccessBundle\Entity\ObjectTarget';
    private static $fieldTargetClass = 'Wachme\Bundle\EasyAccessBundle\Entity\FieldTarget';
    
    /** 
     * @var EntityManager
     */
    private $em;
    
    /**
     * @param string $class
     * @param string $name
     * @return Target
     */
    private function createTarget($class, $name, $parent=null) {
        $entity = new $class();
        $entity->setName($name);
        $entity->setParent($parent);
        $this->em->persist($entity);
        return $entity;
    }
    
    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::createClass()
     */
    public function createClass($class) {
        return $this->createTarget(static::$classTargetClass, $class);
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::createObject()
     */
    public function createObject($object) {
        if(!is_object($object) || !method_exists($object, 'getId'))
            throw new \InvalidArgumentException('object must implement getId() method');
        return $this->createTarget(static::$objectTargetClass, $object->getId(),
            $this->createClass(get_class($object)));
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::createField()
     */
    public function createField(Target $parent, $field) {
        return $this->createTarget(static::$fieldTargetClass, $field, $parent);
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::findByClass()
     */
    public function findByClass($class, $recursive=true) {

    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::findByObject()
     */
    public function findByObject($object, $recursive=true) {
        
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::findByClassField()
     */
    public function findByClassField($class, $field, $recursive=true) {
        
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::findByObjectField()
     */
    public function findByObjectField($object, $field, $recursive=true) {
        
    }
}