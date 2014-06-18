<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Wachme\Bundle\EasyAccessBundle\Model\TargetManagerInterface;
use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassFieldTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectFieldTarget;
use Doctrine\ORM\QueryBuilder;

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
        call_user_method_array($method, $this, array_merge([$qb], $args));
        return $qb->getQuery()->getOneOrNullResult();
    }
    /**
     * @param QueryBuilder $qb
     * @param string $class
     * @return array
     */
    private function selectClass(QueryBuilder $qb, $class) {
        $qb
            ->addSelect('target')
            ->from('EasyAccessBundle:ClassTarget', 'target')
            ->andWhere('target.name = :target_class')
            ->setParameter('target_class', $class);
    }
    /**
     * @param QueryBuilder $qb
     * @param object $object
     * @return array
     */
    private function selectObject(QueryBuilder $qb, $object) {
        $qb
            ->addSelect('target')
            ->from('EasyAccessBundle:ObjectTarget', 'target')
            ->join('target.class', 'c')
            ->andWhere('target.identifier = :target_identifier')
            ->andWhere('c.name = :target_class')
            ->setParameter('target_identifier', $object->getId())
            ->setParameter('target_class', get_class($object));
    }
    /**
     * @param QueryBuilder $qb
     * @param string $class
     * @param string $field
     * @return array
     */
    private function selectClassField(QueryBuilder $qb, $class, $field) {
        $qb
            ->addSelect('target')
            ->from('EasyAccessBundle:ClassFieldTarget', 'target')
            ->join('target.class', 'c')
            ->andWhere('target.name = :target_field')
            ->andWhere('c.name = :target_class')
            ->setParameter('target_field', $field)
            ->setParameter('target_class', $class);
    }
    /**
     * @param QueryBuilder $qb
     * @param object $object
     * @param string $field
     * @return array
     */
    private function selectObjectField(QueryBuilder $qb, $object, $field) {
        $qb
            ->addSelect('target')
            ->from('EasyAccessBundle:ObjectFieldTarget', 'target')
            ->join('target.object', 'o')
            ->join('o.class', 'c')
            ->where('target.name = :target_field')
            ->andWhere('o.identifier = :target_identifier')
            ->andWhere('c.name = :target_class')
            ->setParameter('target_field', $field)
            ->setParameter('target_identifier', $object->getId())
            ->setParameter('target_class', get_class($object));
    }
    
    private function addSelectSet(QueryBuilder $qb, $user) {
        $qb
            ->addSelect(['target_rules', 'target_ancestors', 'target_ancestors_rules'])
            ->leftJoin('target.rules', 'target_rules', 'WITH', $qb->expr()->in(
                'target_rules.subject',
                $this->em->createQueryBuilder()
                    ->select('s1')
                    ->from('EasyAccessBundle:Subject', 's1')
                    ->where('s1.type = :subject_type')
                    ->andWhere('s1.identifier = :subject_identifier')
                    ->getDQL()
            ))
            ->leftJoin('target.ancestors', 'target_ancestors')
            ->leftJoin('target_ancestors.rules', 'target_ancestors_rules', 'WITH', $qb->expr()->in(
                'target_ancestors_rules.subject',
                $this->em->createQueryBuilder()
                    ->select('s2')
                    ->from('EasyAccessBundle:Subject', 's2')
                    ->where('s2.type = :subject_type')
                    ->andWhere('s2.identifier = :subject_identifier')
                    ->getDQL()
            ))
            ->setParameter('subject_type', get_class($user))
            ->setParameter('subject_identifier', $user->getId());
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
    public function findClassSet($class, $user) {
        $qb = $this->em->createQueryBuilder();
        $this->selectClass($qb, $class);
        $this->addSelectSet($qb, $user);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    /**
     * {@inheritdoc}
     */
    public function findObjectSet($object, $user) {
        $qb = $this->em->createQueryBuilder();
        $this->selectObject($qb, $class);
        $this->addSelectSet($qb, $user);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    /**
     * {@inheritdoc}
     */
    public function findClassFieldSet($class, $field, $user) {
        $qb = $this->em->createQueryBuilder();
        $this->selectClassField($qb, $class);
        $this->addSelectSet($qb, $user);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    /**
     * {@inheritdoc}
     */
    public function findObjectFieldSet($object, $field, $user) {
        $qb = $this->em->createQueryBuilder();
        $this->selectObjectField($qb, $class);
        $this->addSelectSet($qb, $user);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
}