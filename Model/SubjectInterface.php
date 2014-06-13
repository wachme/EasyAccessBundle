<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

interface SubjectInterface {
    /**
     * @return integer
     */
    public function getId();
    /**
     * @return string
    */
    public function getType();
    /**
     * @return string
     */
    public function getIdentifier();
}