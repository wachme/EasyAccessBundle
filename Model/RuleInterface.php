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
     * @return integer
     */
    public function getAllowMask();
    /**
     * @return integer
     */
    public function getDenyMask();
}