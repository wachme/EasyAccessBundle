<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Wachme\Bundle\EasyAccessBundle\Model\TargetManagerInterface;
use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassFieldTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectFieldTarget;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Select;

/**
 * Manages target entities in database
 */
class TargetManager implements TargetManagerInterface {
    /** 
     * @var EntityManager
     */
    private $em;
    
    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    private function getSelected($method, $args) {
        $qb = $this->em->createQueryBuilder();
        return $qb->select(
            call_user_method_array($method, $this, array_merge([$qb], $args))
        )->getQuery()->getOneOrNullResult();
    }
    /**
     * @param QueryBuilder $qb
     * @param string $class
     * @return array
     */
    private function selectClass(QueryBuilder $qb, $class) {
        $qb
        ->from('EasyAccessBundle:ClassTarget', 'target')
        ->andWhere('target.name = :class')
        ->setParameter('class', $class);
    
        return ['target'];
    }
    /**
     * @param QueryBuilder $qb
     * @param object $object
     * @return array
     */
    private function selectObject(QueryBuilder $qb, $object) {
        $qb
        ->from('EasyAccessBundle:ObjectTarget', 'target')
        ->join('target.class', 'c')
        ->where('target.identifier = :identifier')
        ->andWhere('c.name = :class')
        ->setParameters([
            'identifier' => $object->getId(),
            'class' => get_class($object)
            ]);
    
        return ['target'];
    }
    /**
     * @param QueryBuilder $qb
     * @param string $class
     * @param string $field
     * @return array
     */
    private function selectClassField(QueryBuilder $qb, $class, $field) {
        $qb
        ->from('EasyAccessBundle:ClassFieldTarget', 'target')
        ->join('target.class', 'c')
        ->where('target.name = :name')
        ->andWhere('c.name = :class')
        ->setParameters([
            'name' => $field,
            'class' => $class
            ]);
    
        return ['target'];
    }
    /**
     * @param QueryBuilder $qb
     * @param object $object
     * @param string $field
     * @return array
     */
    private function selectObjectField(QueryBuilder $qb, $object, $field) {
        $qb
        ->from('EasyAccessBundle:ObjectFieldTarget', 'target')
        ->join('target.object', 'o')
        ->join('o.class', 'c')
        ->where('target.name = :name')
        ->andWhere('o.identifier = :identifier')
        ->andWhere('c.name = :class')
        ->setParameters([
            'name' => $field,
            'identifier' => $object->getId(),
            'class' => get_class($object)
            ]);
    
        return ['target'];
    }
    
    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
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
        return $target;
    }
    /**
     * {@inheritdoc}
     */
    public function findClass($class) {
        return $this->getSelected('selectClass', [$class]);
    }
    /**
     * {@inheritdoc}
     */
    public function findObject($object) {
        return $this->getSelected('selectObject', [$object]);
    }
    /**
     * {@inheritdoc}
     */
    public function findClassField($class, $field) {
        return $this->getSelected('selectClassField', [$class, $field]);
    }
    /**
     * {@inheritdoc}
     */
    public function findObjectField($object, $field) {
        return $this->getSelected('selectObjectField', [$object, $field]);
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
    public function selectClassSet(QueryBuilder $qb, $class) {
        $select = $this->selectClass($qb, $class);
        $qb->leftJoin('target.ancestors', 'target_ancestors');
        
        return array_merge($select, ['target_ancestors']);
    }
    /**
     * {@inheritdoc}
     */
    public function selectObjectSet(QueryBuilder $qb, $object) {
        $select = $this->selectObject($qb, $object);
        $qb>leftJoin('target.ancestors', 'target_ancestors');
        
        return array_merge($select, ['target_ancestors']);
    }
    /**
     * {@inheritdoc}
     */
    public function selectClassFieldSet(QueryBuilder $qb, $class, $field) {
        $select = $this->selectObject($qb, $object);
        $qb->leftJoin('target.ancestors', 'target_ancestors');
        
        return array_merge($select, ['target_ancestors']);
    }
    /**
     * {@inheritdoc}
     */
    public function selectObjectFieldSet(QueryBuilder $qb, $object, $field) {
        $select = $this->selectObject($qb, $object);
        $qb->leftJoin('target.ancestors', 'target_ancestors');
        
        return array_merge($select, ['target_ancestors']);
    }
}