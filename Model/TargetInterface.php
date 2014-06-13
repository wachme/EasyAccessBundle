<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

interface TargetInterface {
    /**
     * @return integer
     */
    public function getId();
    /**
     * @return string
     */
    public function getName();
    /**
     * @return TargetInterface|null
     */
    public function getParent();
}