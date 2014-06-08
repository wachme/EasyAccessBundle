<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity\Target;
use Wachme\Bundle\EasyAccessBundle\Entity\FieldTarget;

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
     * @param Target $parent
     * @param string $field
     * @param boolean $recursive
     * @return FieldTarget
     */
    private function findByField($parent, $field, $recursive=true) {
        $repo = $this->em->getRepository(static::$fieldTargetClass);
        if($target = $repo->findOneBy(['parent' => $parent->getId(), 'name' => $field]))
            return $target;
        
        return $recursive ? $parent : null;
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
        if($this->findByClass($class, false))
            throw new TargetExistsException();
        
        return $this->createTarget(static::$classTargetClass, $class);
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::createObject()
     */
    public function createObject($object) {
        if(!is_object($object) || !method_exists($object, 'getId'))
            throw new \InvalidArgumentException('object must implement getId() method');
        
        if($this->findByObject($object, false))
            throw new TargetExistsException();
        
        $class = get_class($object);
        $id = $object->getId();
        
        if(!$parent = $this->findByClass($class, false))
            $parent = $this->createClass($class);
        
        return $this->createTarget(static::$objectTargetClass, $id, $parent);
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::createClassField()
     */
    public function createClassField($class, $field) {
        if($this->findByClassField($class, $field, false))
            throw new TargetExistsException();
        
        if(!$parent = $this->findByClass($class, false))
            $parent = $this->createClass($class);
        
        return $this->createTarget(static::$fieldTargetClass, $field, $parent);
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::createObjectField()
     */
    public function createObjectField($object, $field) {
        if($this->findByObjectField($object, $field, false))
            throw new TargetExistsException();
        
        if(!$parent = $this->findByObject($object, false))
            $parent = $this->createObject($object);
        
        return $this->createTarget(static::$fieldTargetClass, $field, $parent);
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::findByClass()
     */
    public function findByClass($class, $recursive=true) {
        $repo = $this->em->getRepository(static::$classTargetClass);
        if($target = $repo->findOneByName($class))
            return $target;
        
        return ($recursive && $parent = get_parent_class($class)) ? $this->findByClass($parent) : null;
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::findByObject()
     */
    public function findByObject($object, $recursive=true) {
        if(!$parent = $this->findByClass(get_class($object), false))
            return $recursive ? $this->findByClass(get_class($object)) : null;
        
        $repo = $this->em->getRepository(static::$objectTargetClass);
        if($target = $repo->findOneBy(['parent' => $parent->getId(), 'name' => $object->getId()]))
            return $target;
        
        return $recursive ? $parent : null;
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::findByClassField()
     */
    public function findByClassField($class, $field, $recursive=true) {
        if(!$parent = $this->findByClass($class, $recursive))
            return null;
        
        return $this->findByField($parent, $field, $recursive);
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::findByObjectField()
     */
    public function findByObjectField($object, $field, $recursive=true) {
        if(!$parent = $this->findByObject($object, $recursive))
            return null;
        
        return $this->findByField($parent, $field, $recursive);
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Manager\TargetManagerInterface::save()
     */
    public function save() {
        $this->em->flush();
    }
}