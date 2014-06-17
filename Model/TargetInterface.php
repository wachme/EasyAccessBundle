<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

interface TargetInterface {
    /**
     * @return integer
     */
    public function getId();
    /**
     * @return ArrayCollection
     */
    public function getChildren();
    /**
     * @return ArrayCollection
     */
    public function getAncestors();
    /**
     * @return ArrayCollection
     */
    public function getRules();
}