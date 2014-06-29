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
    public function create(TargetInterface $target, SubjectInterface $subject);
    /**
     * @param TargetInterface $target
     * @param SubjectInterface $subject
     * @return RuleInterface|null
     */
    public function find(TargetInterface $target, SubjectInterface $subject);
    /**
     * @param TargetInterface $target
     * @param SubjectInterface $subject
     * @return RuleInterface
     */
    public function findOrCreate(TargetInterface $target, SubjectInterface $subject);
    /**
     * @param RuleInterface $rule
     * @param integer $mask
     */
    public function allow(RuleInterface $rule, $mask);
    /**
     * @param RuleInterface $rule
     * @param integer $mask
     */
    public function deny(RuleInterface $rule, $mask);
}