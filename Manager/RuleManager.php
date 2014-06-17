<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Wachme\Bundle\EasyAccessBundle\Model\RuleManagerInterface;
use Wachme\Bundle\EasyAccessBundle\Model\RuleInterface;
use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Wachme\Bundle\EasyAccessBundle\Model\SubjectInterface;
use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity\Rule;
use Doctrine\ORM\QueryBuilder;

class RuleManager implements RuleManagerInterface {
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
    public function create(TargetInterface $target, SubjectInterface $subject, $mask=0) {
        $rule = new Rule();
        $rule->setTarget($target);
        $rule->setSubject($subject);
        $rule->setMask($mask);
        $this->em->persist($rule);
        return $rule;
    }
    /**
     * {@inheritdoc}
     */
    public function find(TargetInterface $target, SubjectInterface $subject) {
        $repo = $this->em->getRepository('EasyAccessBundle:Rule');
        return $repo->findOneBy(['target' => $target->getId(), 'subject' => $subject->getId()]);
    }
    /**
     * {@inheritdoc}
     */
    public function findOrCreate(TargetInterface $target, SubjectInterface $subject) {
        return $this->find($target, $subject) ?: $this->create($target, $subject);
    }
    /**
     * {@inheritdoc}
     */
    public function selectRuleSet(QueryBuilder $qb) {
        $qb
            ->leftJoin('target.rules', 'target_rules')
            ->leftJoin('target_ancestors.rules', 'target_ancestors_rules');
        
        return ['target_rules', 'target_ancestors_rules'];
    }
}