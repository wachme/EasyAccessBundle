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
     * @see \Wachme\Bundle\EasyAccessBundle\Model\SubjectManagerInterface::createUser()
     */
    public function createUser($user) {
        if($this->findByUser($user))
            throw new SubjectExistsException();
        
        $subject = new Subject();
        $subject->setType(get_class($user));
        $subject->setIdentifier($user->getId());
        $this->em->persist($subject);
        return $subject;
    }
    /**
     * @see \Wachme\Bundle\EasyAccessBundle\Model\SubjectManagerInterface::findByUser()
     */
    public function findByUser($user) {
        $repo = $this->em->getRepository('EasyAccessBundle:Subject');
        return $repo->findOneBy(['type' => get_class($user), 'identifier' => $user->getId()]);
    }
}