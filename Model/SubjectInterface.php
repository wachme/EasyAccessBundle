<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

interface SubjectInterface {
    /**
     * @return integer
     */
    public function getId();
    /**
     * @param string $type
     */
    public function setType($type);
    /**
     * @return string
    */
    public function getType();
    /**
     * @param string $name
     */
    public function setIdentifier($identifier);
    /**
     * @return string
     */
    public function getIdentifier();
}