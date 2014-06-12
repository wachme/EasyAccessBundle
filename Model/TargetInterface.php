<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

interface TargetInterface {
    /**
     * @param string $name
     */
    public function setName($name);
    /**
     * @return string
     */
    public function getName();
    /**
     * @param TargetInterface $parent
     */
    public function setParent($parent);
    /**
     * @return TargetInterface
     */
    public function getParent();
}