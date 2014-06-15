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
}