<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

use Wachme\Bundle\EasyAccessBundle\Model\RuleInterface;
use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Wachme\Bundle\EasyAccessBundle\Model\SubjectInterface;

interface RuleManagerInterface {
    /**
     * @param TargetInterface $target
     * @param SubjectInterface $subject
     * @return RuleInterface
     */
    public function create(TargetInterface $target, SubjectInterface $subject, $mask=0, RuleInterface $parent=null);
    /**
     * @param TargetInterface $target
     * @param SubjectInterface $subject
     * @return RuleInterface|null
     */
    public function find(TargetInterface $target, SubjectInterface $subject);
    /**
     * @param TargetInterface $target
     * @return RuleInterface|null
     */
    public function findByTarget(TargetInterface $target);
    /**
     * @param SubjectInterface $subject
     * @return RuleInterface|null
     */
    public function findBySubject(SubjectInterface $subject);
}