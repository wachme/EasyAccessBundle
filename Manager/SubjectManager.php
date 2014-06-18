<?php

namespace Wachme\Bundle\EasyAccessBundle\Manager;

use Wachme\Bundle\EasyAccessBundle\Model\SubjectManagerInterface;
use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Entity\Subject;

class SubjectManager implements SubjectManagerInterface {
    
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
     * {@inheritdoc}
     */
    public function createUser($user) {
        $subject = new Subject();
        $subject->setType(get_class($user));
        $subject->setIdentifier($user->getId());
        $this->em->persist($subject);
        return $subject;
    }
    /**
     * {@inheritdoc}
     */
    public function findUser($user) {
        $repo = $this->em->getRepository('EasyAccessBundle:Subject');
        return $repo->findOneBy(['type' => get_class($user), 'identifier' => $user->getId()]);
    }
    /**
     * {@inheritdoc}
     */
    public function findOrCreateUser($user) {
        return $this->findUser($user) ?: $this->createUser($user);
    }
}