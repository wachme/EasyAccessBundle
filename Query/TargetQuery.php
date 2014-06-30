<?php

namespace Wachme\Bundle\EasyAccessBundle\Query;
use Doctrine\ORM\QueryBuilder;

class TargetQuery {
    /**
     * @param QueryBuilder $qb
     * @param string $class
     * @param string $alias
     */
    public function selectClass(QueryBuilder $qb, $class, $alias='target') {
        $qb
            ->addSelect($alias)
            ->from('EasyAccessBundle:ClassTarget', $alias)
            ->andWhere("{$alias}.name = :{$alias}_class_name")
            ->setParameter("{$alias}_class_name", $class);
    }
    /**
     * @param QueryBuilder $qb
     * @param object $object
     * @param boolean $typeAncestors
     * @param string $alias
     */
    public function selectObject(QueryBuilder $qb, $object, $typeAncestors=false, $alias='target') {
        if(!$typeAncestors) {
            $qb
                ->addSelect($alias)
                ->from('EasyAccessBundle:ObjectTarget', $alias)
                ->join("{$alias}.class", "{$alias}_class")
                ->andWhere("{$alias}.identifier = :{$alias}_identifier")
                ->andWhere("{$alias}_class.name = :{$alias}_class_name");
        }
        else {
            $qb
                ->addSelect(["{$alias}_class", $alias])
                ->from('EasyAccessBundle:ClassTarget', "{$alias}_class")
                ->leftJoin("{$alias}_class.objects", $alias, 'WITH', "{$alias}.identifier = :{$alias}_identifier")
                ->andWhere("{$alias}_class.name = :{$alias}_class_name");
        }
        $qb
            ->setParameter("{$alias}_identifier", $object->getId())
            ->setParameter("{$alias}_class_name", get_class($object));
    }
    /**
     * @param QueryBuilder $qb
     * @param string $class
     * @param string $field
     * @param boolean $typeAncestors
     * @param string $alias
     */
    public function selectClassField(QueryBuilder $qb, $class, $field, $typeAncestors=false, $alias='target') {
        if(!$typeAncestors) {
            $qb
                ->addSelect($alias)
                ->from('EasyAccessBundle:ClassFieldTarget', $alias)
                ->join("{$alias}.class", "{$alias}_class")
                ->andWhere("{$alias}.name = :{$alias}_name")
                ->andWhere("{$alias}_class.name = :{$alias}_class_name");
        }
        else {
            $qb
                ->addSelect(["{$alias}_class", $alias])
                ->from('EasyAccessBundle:ClassTarget', "{$alias}_class")
                ->leftJoin("{$alias}_class.fields", $alias, 'WITH', "{$alias}.name = :{$alias}_name")
                ->andWhere("{$alias}_class.name = :{$alias}_class_name");
        }
        $qb
            ->setParameter("{$alias}_name", $field)
            ->setParameter("{$alias}_class_name", $class);
    }
    /**
     * @param QueryBuilder $qb
     * @param object $object
     * @param string $field
     * @param boolean $typeAncestors
     * @param string $alias
     */
    public function selectObjectField(QueryBuilder $qb, $object, $field, $typeAncestors=false, $alias='target') {
        if(!$typeAncestors) {
            $qb
                ->addSelect($alias)
                ->from('EasyAccessBundle:ObjectFieldTarget', $alias)
                ->join("{$alias}.object", "{$alias}_object")
                ->join("{$alias}_object.class", "{$alias}_object_class")
                ->where("{$alias}.name = :{$alias}_name")
                ->andWhere("{$alias}_object.identifier = :{$alias}_object_identifier")
                ->andWhere("{$alias}_object_class.name = :{$alias}_object_class_name");
        }
        else {
            $qb
                ->addSelect(["{$alias}_class", "{$alias}_class_field", "{$alias}_object", $alias])
                ->from('EasyAccessBundle:ClassTarget', "{$alias}_class")
                ->leftJoin("{$alias}_class.fields", "{$alias}_class_field", 'WITH', "{$alias}_class_field.name = :{$alias}_name")
                ->leftJoin("{$alias}_class.objects", "{$alias}_object", 'WITH', "{$alias}_object.identifier = :{$alias}_object_identifier")
                ->leftJoin("{$alias}_object.fields", $alias, 'WITH', "{$alias}.name = :{$alias}_name")
                ->andWhere("{$alias}_class.name = :{$alias}_object_class_name");
        }
        $qb
            ->setParameter("{$alias}_name", $field)
            ->setParameter("{$alias}_object_identifier", $object->getId())
            ->setParameter("{$alias}_object_class_name", get_class($object));
    }
    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @param object $user
     */
    public function selectTargetRules(QueryBuilder $qb, $alias, $user=null) {
        $qb
            ->addSelect("{$alias}_rules");
        
        if($user) {
            $qb
                ->leftJoin("{$alias}.rules", "{$alias}_rules", 'WITH', $qb->expr()->in(
                    "{$alias}_rules.subject",
                    $qb->getEntityManager()->createQueryBuilder()
                        ->select("{$alias}_rules_subject")
                        ->from('EasyAccessBundle:Subject', "{$alias}_rules_subject")
                        ->where("{$alias}_rules_subject.type = :{$alias}_rules_subject_type")
                        ->andWhere("{$alias}_rules_subject.identifier = :{$alias}_rules_subject_identifier")
                        ->getDQL()
                ))
                ->setParameter("{$alias}_rules_subject_type", get_class($user))
                ->setParameter("{$alias}_rules_subject_identifier", $user->getId());
        }
        else {
            $qb
                ->leftJoin("{$alias}.rules", "{$alias}_rules");
        }
    }
    /**
     * @param QueryBuilder $qb
     * @param string $alias
     */
    public function selectTargetAncestors(QueryBuilder $qb, $alias) {
        $qb
            ->addSelect("{$alias}_ancestors")
            ->leftJoin("{$alias}.ancestors", "{$alias}_ancestors");
    }
    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @param object $user
     */
    public function selectTargetMembers(QueryBuilder $qb, $alias, $user=null) {
        $this->selectTargetAncestors($qb, $alias);
        $this->selectTargetRules($qb, $alias, $user);
        $this->selectTargetRules($qb, "{$alias}_ancestors", $user);
    }
}