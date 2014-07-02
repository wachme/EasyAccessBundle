<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity\Subject;

class SubjectManager {
    
    /**
     * @var EntityManager
     */
    private $em;
    
    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    /**
     * @param object $user
     * @return Subject
     */
    public function createUser($user) {
        $subject = new Subject();
        $subject->setType(get_class($user));
        $subject->setIdentifier($user->getId());
        $this->em->persist($subject);
        return $subject;
    }
    /**
     * @param object $user
     * @return Subject|null
     */
    public function findUser($user) {
        $repo = $this->em->getRepository('EasyAccessBundle:Subject');
        return $repo->findOneBy(['type' => get_class($user), 'identifier' => $user->getId()]);
    }
    /**
     * @param object $user
     * @return Subject
     */
    public function findOrCreateUser($user) {
        return $this->findUser($user) ?: $this->createUser($user);
    }
}