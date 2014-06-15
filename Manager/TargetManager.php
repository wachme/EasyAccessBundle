<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Wachme\Bundle\EasyAccessBundle\Model\TargetManagerInterface;
use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassFieldTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectFieldTarget;

/**
 * Manages target entities in database
 */
class TargetManager implements TargetManagerInterface {
    /** 
     * @var EntityManager
     */
    private $em;
    
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
        $repo = $this->em->getRepository('EasyAccessBundle:ClassTarget');
        return $repo->findOneByName($class);
    }
    /**
     * {@inheritdoc}
     */
    public function findObject($object) {
        return $this->em->createQueryBuilder()
        ->select('t')
        ->from('EasyAccessBundle:ObjectTarget', 't')
        ->join('t.class', 'c')
        ->where('t.identifier = :identifier')
        ->andWhere('c.name = :class')
        ->setParameters([
            'identifier' => $object->getId(),
            'class' => get_class($object)
            ])
            ->getQuery()->getOneOrNullResult();
    }
    /**
     * {@inheritdoc}
     */
    public function findClassField($class, $field) {
        return $this->em->createQueryBuilder()
        ->select('t')
        ->from('EasyAccessBundle:ClassFieldTarget', 't')
        ->join('t.class', 'c')
        ->where('t.name = :name')
        ->andWhere('c.name = :class')
        ->setParameters([
            'name' => $field,
            'class' => $class
            ])
            ->getQuery()->getOneOrNullResult();
    }
    /**
     * {@inheritdoc}
     */
    public function findObjectField($object, $field) {
        return $this->em->createQueryBuilder()
        ->select('t')
        ->from('EasyAccessBundle:ObjectFieldTarget', 't')
        ->join('t.object', 'o')
        ->join('o.class', 'c')
        ->where('t.name = :name')
        ->andWhere('o.identifier = :identifier')
        ->andWhere('c.name = :class')
        ->setParameters([
            'name' => $field,
            'identifier' => $object->getId(),
            'class' => get_class($object)
            ])
            ->getQuery()->getOneOrNullResult();
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
}