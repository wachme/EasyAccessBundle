<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity\Target;
use Wachme\Bundle\EasyAccessBundle\Entity\Subject;
use Wachme\Bundle\EasyAccessBundle\Entity\Rule;

class RuleManager {
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
     * @param Target $target
     * @param Subject $subject
     * @return Rule
     */
    public function create(Target $target, Subject $subject) {
        $rule = new Rule();
        $rule->setTarget($target);
        $rule->setSubject($subject);
        $this->em->persist($rule);
        return $rule;
    }
    /**
     * @param Target $target
     * @param Subject $subject
     * @return Rule|null
     */
    public function find(Target $target, Subject $subject) {
        $repo = $this->em->getRepository('EasyAccessBundle:Rule');
        return $repo->findOneBy(['target' => $target->getId(), 'subject' => $subject->getId()]);
    }
    /**
     * @param Target $target
     * @param Subject $subject
     * @return Rule
     */
    public function findOrCreate(Target $target, Subject $subject) {
        return $this->find($target, $subject) ?: $this->create($target, $subject);
    }
    /**
     * @param Rule $rule
     * @param integer $mask
     */
    public function allow(Rule $rule, $mask) {
        $rule->setAllowMask($mask);
        $rule->setDenyMask($rule->getDenyMask() & ~$mask);
    }
    /**
     * @param Rule $rule
     * @param integer $mask
     */
    public function deny(Rule $rule, $mask) {
        $rule->setDenyMask($mask);
        $rule->setAllowMask($rule->getAllowMask() & ~$mask);
    }
}