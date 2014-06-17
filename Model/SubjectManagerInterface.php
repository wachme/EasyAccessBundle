<?php

namespace Wachme\Bundle\EasyAccessBundle\Model;

use Wachme\Bundle\EasyAccessBundle\Model\SubjectInterface;
use Doctrine\ORM\QueryBuilder;

interface SubjectManagerInterface {
    /**
     * @param object $user
     * @return SubjectInterface
     */
    public function createUser($user);
    /**
     * @param object $user
     * @return SubjectInterface|null
     */
    public function findUser($user);
    /**
     * @param object $user
     * @return SubjectInterface
     */
    public function findOrCreateUser($user);
    /**
     * @param QueryBuilder $qb
     * @param object $user
     * @return array
     */
    public function selectUserSet(QueryBuilder $qb, $user);
}