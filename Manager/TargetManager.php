<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity\Target;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassFieldTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectFieldTarget;
use Doctrine\ORM\QueryBuilder;
use Wachme\Bundle\EasyAccessBundle\Query\TargetQuery;

/**
 * Manages target entities in database
 */
class TargetManager {
    /** 
     * @var EntityManager
     */
    private $em;
    /**
     * @var TargetQuery
     */
    private $query;
    
    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    private function getSelected($func, $args) {
        $qb = $this->em->createQueryBuilder();
        call_user_func_array($func, array_merge([$qb], $args));
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
        $this->query = new TargetQuery();
    }
    /**
     * @param string $class
     * @return ClassTarget
     */
    public function createClass($class) {
        $target = new ClassTarget();
        $target->setName($class);
        $this->em->persist($target);
        return $target;
    }
    /**
     * @param object $object
     * @return ObjectTarget
     */
    public function createObject($object) {
        $class = get_class($object);
        $id = $object->getId();
        $classTarget = $this->findOrCreateClass($class);
    
        $target = new ObjectTarget();
        $target->setClass($classTarget);
        $target->setIdentifier($id);
        $this->em->persist($target);
        
        $this->inherit($target, $classTarget);
        
        return $target;
    }
    /**
     * @param string $class
     * @param string $field
     * @return ClassFieldTarget
     */
    public function createClassField($class, $field) {
        $classTarget = $this->findOrCreateClass($class);
    
        $target = new ClassFieldTarget();
        $target->setClass($classTarget);
        $target->setName($field);
        $this->em->persist($target);
        
        $this->inherit($target, $classTarget);
        
        return $target;
    }
    /**
     * @param object $object
     * @param string $field
     * @return ObjectFieldTarget
     */
    public function createObjectField($object, $field) {
        $objectTarget = $this->findOrCreateObject($object);
        $classFieldTarget = $this->findOrCreateClassField(get_class($object), $field);
        
        $target = new ObjectFieldTarget();
        $target->setObject($objectTarget);
        $target->setClassField($classFieldTarget);
        $target->setName($field);
        $this->em->persist($target);
        
        $this->inherit($target, $classFieldTarget);
        $this->inherit($target, $objectTarget);
        
        return $target;
    }
    /**
     * @param Target $target
     * @param Target $parentTarget
     */
    public function inherit(Target $target, Target $parentTarget) {
        foreach(array_merge($target->getChildren()->toArray(), [$target]) as $child) {
            foreach(array_merge($parentTarget->getAncestors()->toArray(), [$parentTarget]) as $ancestor)
                $ancestor->addChild($child);
        }
    }
    /**
     * @param string $class
     * @return ClassTarget|null
     */
    public function findClass($class) {
        return $this->getSelected([$this->query, 'selectClass'], [$class]);
    }
    /**
     * @param object $object
     * @return ObjectTarget|null
     */
    public function findObject($object) {
        return $this->getSelected([$this->query, 'selectObject'], [$object]);
    }
    /**
     * @param string $class
     * @param string $field
     * @return ClassFieldTarget|null
     */
    public function findClassField($class, $field) {
        return $this->getSelected([$this->query, 'selectClassField'], [$class, $field]);
    }
    /**
     * @param object $object
     * @param string $field
     * @return ObjectFieldTarget|null
     */
    public function findObjectField($object, $field) {
        return $this->getSelected([$this->query, 'selectObjectField'], [$object, $field]);
    }
    /**
     * @param string $class
     * @return ClassTarget
     */
    public function findOrCreateClass($class) {
        return $this->findClass($class) ?: $this->createClass($class);
    }
    /**
     * @param object $object
     * @return ObjectTarget
     */
    public function findOrCreateObject($object) {
        return $this->findObject($object) ?: $this->createObject($object);
    }
    /**
     * @param string $class
     * @param string $field
     * @return ClassFieldTarget
     */
    public function findOrCreateClassField($class, $field) {
        return $this->findClassField($class, $field) ?: $this->createClassField($class, $field);
    }
    /**
     * @param object $object
     * @param string $field
     * @return ObjectFieldTarget
     */
    public function findOrCreateObjectField($object, $field) {
        return $this->findObjectField($object, $field) ?: $this->createObjectField($object, $field);
    }
    /**
     * @param string $class
     * @param object $user
     * @return Target|null
     */
    public function findClassSet($class, $user) {
        $qb = $this->em->createQueryBuilder();
        
        $this->query->selectClass($qb, $class);
        $this->query->selectTargetMembers($qb, 'target', $user);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    /**
     * @param object $object
     * @param object $user
     * @return Target|null
     */
    public function findObjectSet($object, $user) {
        $qb = $this->em->createQueryBuilder();
        
        $this->query->selectObject($qb, $object, true);
        $this->query->selectTargetMembers($qb, 'target', $user);
        $this->query->selectTargetMembers($qb, 'target_class', $user);
        
        if(!$target = $qb->getQuery()->getOneOrNullResult())
            return null;
        
        return $target->getObjects()->isEmpty() ? $target : $target->getObjects()[0];
    }
    /**
     * @param string $class
     * @param string $field
     * @param object $user
     * @return Target|null
     */
    public function findClassFieldSet($class, $field, $user) {
        $qb = $this->em->createQueryBuilder();
        
        $this->query->selectClassField($qb, $class, $field, true);
        $this->query->selectTargetMembers($qb, 'target', $user);
        $this->query->selectTargetMembers($qb, 'target_class', $user);
        
        if(!$target = $qb->getQuery()->getOneOrNullResult())
            return null;
        
        return $target->getFields()->isEmpty() ? $target : $target->getFields()[0];
    }
    /**
     * @param object $object
     * @param string $field
     * @param object $user
     * @return Target|null
     */
    public function findObjectFieldSet($object, $field, $user) {
        $qb = $this->em->createQueryBuilder();
        
        $this->query->selectObjectField($qb, $object, $field, true);
        $this->query->selectTargetMembers($qb, 'target', $user);
        $this->query->selectTargetMembers($qb, 'target_class', $user);
        $this->query->selectTargetMembers($qb, 'target_class_field', $user);
        $this->query->selectTargetMembers($qb, 'target_object', $user);
        
        if(!$target = $qb->getQuery()->getOneOrNullResult())
            return null;
        if($target->getObjects()->isEmpty())
            return $target->getFields()->isEmpty() ? $target : $target->getFields()[0];
        
        $objectTarget = $target->getObjects()[0];
        return $objectTarget->getFields()->isEmpty() ? $objectTarget : $objectTarget->getFields()[0];
    }
}