<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Wachme\Bundle\EasyAccessBundle\Model\RuleManagerInterface;
use Wachme\Bundle\EasyAccessBundle\Model\RuleInterface;
use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Wachme\Bundle\EasyAccessBundle\Model\SubjectInterface;
use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity\Rule;

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
     * @see \Wachme\Bundle\EasyAccessBundle\Model\RuleManagerInterface::create()
     */
    public function create(TargetInterface $target, SubjectInterface $subject, $mask=0, RuleInterface $parent=null) {
        if($this->find($target, $subject, false))
            throw new RuleExistsException();
        
        $rule = new Rule();
        $rule->setTarget($target);
        $rule->setSubject($subject);
        $rule->setMask($mask);
        $rule->setParent($parent);
        $this->em->persist($rule);
        return $rule;
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Model\RuleManagerInterface::find()
     */
    public function find(TargetInterface $target, SubjectInterface $subject, $recursive=true) {
        $repo = $this->em->getRepository('EasyAccessBundle:Rule');
        if($rule = $repo->findOneBy(['target' => $target->getId(), 'subject' => $subject->getId()]))
            return $rule;
        
        return ($recursive && $parentTarget = $target->getParent()) ? $this->find($parentTarget, $subject) : null;
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Model\RuleManagerInterface::findByTarget()
     */
    public function findByTarget(TargetInterface $target) {
        $repo = $this->em->getRepository('EasyAccessBundle:Rule');
        return $repo->findOneBy(['target' => $target->getId()]);
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Model\RuleManagerInterface::findBySubject()
     */
    public function findBySubject(SubjectInterface $subject) {
        $repo = $this->em->getRepository('EasyAccessBundle:Rule');
        return $repo->findOneBy(['subject' => $subject->getId()]);
    }
}