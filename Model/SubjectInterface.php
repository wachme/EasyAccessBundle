<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

interface SubjectInterface {
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