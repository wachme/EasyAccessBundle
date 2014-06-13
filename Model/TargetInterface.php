<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

interface TargetInterface {
    /**
     * @return integer
     */
    public function getId();
    /**
     * @param string $name
     */
    public function setName($name);
    /**
     * @return string
     */
    public function getName();
    /**
     * @param TargetInterface|null $parent
     */
    public function setParent(TargetInterface $parent=null);
    /**
     * @return TargetInterface|null
     */
    public function getParent();
}