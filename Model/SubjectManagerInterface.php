<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

use Wachme\Bundle\EasyAccessBundle\Model\SubjectInterface;

interface SubjectManagerInterface {
    /**
     * @param object $user
     * @return SubjectInterface
     */
    public function createUser($user);
    /**
     * @param object $user
     * @return SubjectInterface
     */
    public function findByUser($user);
}