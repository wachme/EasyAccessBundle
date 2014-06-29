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
     * @param boolean $typeAncestors
     * @return array
     */
    private function selectObject(QueryBuilder $qb, $object, $typeAncestors=false) {
        if(!$typeAncestors) {
            $qb
                ->addSelect('target')
                ->from('EasyAccessBundle:ObjectTarget', 'target')
                ->join('target.class', 'c')
                ->andWhere('target.identifier = :target_identifier')
                ->andWhere('c.name = :target_class');
        }
        else {
            $qb
                ->addSelect(['class_target', 'target'])
                ->from('EasyAccessBundle:ClassTarget', 'class_target')
                ->leftJoin('class_target.objects', 'target', 'WITH', 'target.identifier = :target_identifier')
                ->andWhere('class_target.name = :target_class');
        }
        $qb
            ->setParameter('target_identifier', $object->getId())
            ->setParameter('target_class', get_class($object));
    }
    /**
     * @param QueryBuilder $qb
     * @param string $class
     * @param string $field
     * @param boolean $typeAncestors
     * @return array
     */
    private function selectClassField(QueryBuilder $qb, $class, $field, $typeAncestors=false) {
        if(!$typeAncestors) {
            $qb
                ->addSelect('target')
                ->from('EasyAccessBundle:ClassFieldTarget', 'target')
                ->join('target.class', 'c')
                ->andWhere('target.name = :target_name')
                ->andWhere('c.name = :target_class');
        }
        else {
            $qb
                ->addSelect(['class_target', 'target'])
                ->from('EasyAccessBundle:ClassTarget', 'class_target')
                ->leftJoin('class_target.fields', 'target', 'WITH', 'target.name = :target_name')
                ->andWhere('class_target.name = :target_class');
        }
        $qb
            ->setParameter('target_name', $field)
            ->setParameter('target_class', $class);
    }
    /**
     * @param QueryBuilder $qb
     * @param object $object
     * @param string $field
     * @param boolean $typeAncestors
     * @return array
     */
    private function selectObjectField(QueryBuilder $qb, $object, $field, $typeAncestors=false) {
        if(!$typeAncestors) {
            $qb
                ->addSelect('target')
                ->from('EasyAccessBundle:ObjectFieldTarget', 'target')
                ->join('target.object', 'o')
                ->join('o.class', 'c')
                ->where('target.name = :target_name')
                ->andWhere('o.identifier = :target_identifier')
                ->andWhere('c.name = :target_class');
        }
        else {
            $qb
                ->addSelect(['class_target', 'class_fields_target', 'object_target', 'target'])
                ->from('EasyAccessBundle:ClassTarget', 'class_target')
                ->leftJoin('class_target.fields', 'class_fields_target', 'WITH', 'class_fields_target.name = :target_name')
                ->leftJoin('class_target.objects', 'object_target', 'WITH', 'object_target.identifier = :target_identifier')
                ->leftJoin('object_target.fields', 'target', 'WITH', 'target.name = :target_name')
                ->andWhere('class_target.name = :target_class');
        }
        $qb
            ->setParameter('target_name', $field)
            ->setParameter('target_identifier', $object->getId())
            ->setParameter('target_class', get_class($object));
    }
    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @param object $user
     */
    private function selectTargetRules(QueryBuilder $qb, $alias, $user) {
        $rules = $alias . '_rules';
        $subject = $rules . '_subject';
        
        $subjectType = $subject . '_type';
        $subjectIdentifier = $subject . '_identifier';
        
        $qb
            ->addSelect($rules)
            ->leftJoin($alias . '.rules', $rules, 'WITH', $qb->expr()->in(
                $rules . '.subject',
                $this->em->createQueryBuilder()
                    ->select($subject)
                    ->from('EasyAccessBundle:Subject', $subject)
                    ->where($subject . '.type = :' . $subjectType)
                    ->andWhere($subject . '.identifier = :' . $subjectIdentifier)
                    ->getDQL()
            ))
            ->setParameter($subjectType, get_class($user))
            ->setParameter($subjectIdentifier, $user->getId());
    }
    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @param object $user
     */
    private function selectTargetMembers(QueryBuilder $qb, $alias, $user) {
        $ancestors = $alias . '_ancestors';
        
        $this->selectTargetRules($qb, $alias, $user);
        $qb
            ->addSelect($ancestors)
            ->leftJoin($alias . '.ancestors', $ancestors);
        $this->selectTargetRules($qb, $ancestors, $user);
    }
    /**
     * @param TargetInterface $target
     * @param TargetInterface $parentTarget
     */
    public function inherit($target, $parentTarget) {
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
        $this->selectTargetMembers($qb, 'target', $user);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    /**
     * {@inheritdoc}
     */
    public function findObjectSet($object, $user) {
        $qb = $this->em->createQueryBuilder();
        
        $this->selectObject($qb, $object, true);
        $this->selectTargetMembers($qb, 'target', $user);
        $this->selectTargetMembers($qb, 'class_target', $user);
        
        if(!$target = $qb->getQuery()->getOneOrNullResult())
            return null;
        
        return $target->getObjects()->isEmpty() ? $target : $target->getObjects()[0];
    }
    /**
     * {@inheritdoc}
     */
    public function findClassFieldSet($class, $field, $user) {
        $qb = $this->em->createQueryBuilder();
        
        $this->selectClassField($qb, $class, $field, true);
        $this->selectTargetMembers($qb, 'target', $user);
        $this->selectTargetMembers($qb, 'class_target', $user);
        
        if(!$target = $qb->getQuery()->getOneOrNullResult())
            return null;
        
        return $target->getFields()->isEmpty() ? $target : $target->getFields()[0];
    }
    /**
     * {@inheritdoc}
     */
    public function findObjectFieldSet($object, $field, $user) {
        $qb = $this->em->createQueryBuilder();
        
        $this->selectObjectField($qb, $object, $field, true);
        $this->selectTargetMembers($qb, 'target', $user);
        $this->selectTargetMembers($qb, 'class_target', $user);
        $this->selectTargetMembers($qb, 'class_fields_target', $user);
        $this->selectTargetMembers($qb, 'object_target', $user);
        
        if(!$target = $qb->getQuery()->getOneOrNullResult())
            return null;
        if($target->getObjects()->isEmpty())
            return $target->getFields()->isEmpty() ? $target : $target->getFields()[0];
        $objectTarget = $target->getObjects()[0];
        return $objectTarget->getFields()->isEmpty() ? $objectTarget : $objectTarget->getFields()[0];
    }
}