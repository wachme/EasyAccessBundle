<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Wachme\Bundle\EasyAccessBundle\Model\TargetManagerInterface;
use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassFieldTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectFieldTarget;
use Doctrine\ORM\QueryBuilder;
use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Wachme\Bundle\EasyAccessBundle\Query\TargetQuery;

/**
 * Manages target entities in database
 */
class TargetManager implements TargetManagerInterface {
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
     * @param TargetInterface $target
     * @param TargetInterface $parentTarget
     */
    private function inherit($target, $parentTarget) {
        $parentTarget->addChild($target);
        if($children = $target->getChildren()) {
            foreach($children as $child)
                $parentTarget->addChild($child);
        }
    
        if($ancestors = $parentTarget->getAncestors()) {
            foreach($ancestors as $ancestor) {
                $ancestor->addChild($target);
                if($children)
                foreach($children as $child)
                    $ancestor->addChild($child);
            }
        }
    }
    
    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
        $this->query = new TargetQuery();
    }
    /**
     * {@inheritdoc}
     */
    public function createClass($class) {
        $target = new ClassTarget();
        $target->setName($class);
        $this->em->persist($target);
        return $target;
    }
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function createObjectField($object, $field) {
        $objectTarget = $this->findOrCreateObject($object);
        
        $target = new ObjectFieldTarget();
        $target->setObject($objectTarget);
        $target->setName($field);
        $this->em->persist($target);
        
        $this->inherit($target, $objectTarget);
        
        return $target;
    }
    /**
     * {@inheritdoc}
     */
    public function findClass($class) {
        return $this->getSelected([$this->query, 'selectClass'], [$class]);
    }
    /**
     * {@inheritdoc}
     */
    public function findObject($object) {
        return $this->getSelected([$this->query, 'selectObject'], [$object]);
    }
    /**
     * {@inheritdoc}
     */
    public function findClassField($class, $field) {
        return $this->getSelected([$this->query, 'selectClassField'], [$class, $field]);
    }
    /**
     * {@inheritdoc}
     */
    public function findObjectField($object, $field) {
        return $this->getSelected([$this->query, 'selectObjectField'], [$object, $field]);
    }
    /**
     * {@inheritdoc}
     */
    public function findOrCreateClass($class) {
        return $this->findClass($class) ?: $this->createClass($class);
    }
    /**
     * {@inheritdoc}
     */
    public function findOrCreateObject($object) {
        return $this->findObject($object) ?: $this->createObject($object);
    }
    /**
     * {@inheritdoc}
     */
    public function findOrCreateClassField($class, $field) {
        return $this->findClassField($class, $field) ?: $this->createClassField($class, $field);
    }
    /**
     * {@inheritdoc}
     */
    public function findOrCreateObjectField($object, $field) {
        return $this->findObjectField($object, $field) ?: $this->createObjectField($object, $field);
    }
    /**
     * {@inheritdoc}
     */
    public function findClassSet($class, $user) {
        $qb = $this->em->createQueryBuilder();
        
        $this->query->selectClass($qb, $class);
        $this->query->selectTargetMembers($qb, 'target', $user);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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