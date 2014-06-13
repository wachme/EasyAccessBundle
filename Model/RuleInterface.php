<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Wachme\Bundle\EasyAccessBundle\Model\SubjectInterface;

interface RuleInterface {
    /**
     * @return integer
     */
    public function getId();
    /**
     * @return TargetInterface
     */
    public function getTarget();
    /**
     * @return SubjectInterface
     */
    public function getSubject();
    /**
     * @param integer $mask
     */
    public function setMask($mask);
    /**
     * @return integer
     */
    public function getMask();
    /**
     * @param RuleInterface|null $parent
     */
    public function setParent(RuleInterface $parent=null);
    /**
     * @return RuleInterface|null
     */
    public function getParent();
}