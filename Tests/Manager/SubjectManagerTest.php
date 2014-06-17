<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests\Manager;

use Wachme\Bundle\EasyAccessBundle\Tests\DbTestCase;
use Wachme\Bundle\EasyAccessBundle\Manager\SubjectManager;
use Wachme\Bundle\EasyAccessBundle\Entity\Subject;

class SubjectManagerTest extends DbTestCase {
    private $manager;
    
    protected function setUp() {
        parent::setUp();
        $this->manager = new SubjectManager($this->em);
    }
    
    public function testCreateUser() {
        $this->loadData('user');
        
        $user = $this->em->getRepository('Test:User')->findAll()[0];
        $subject = new Subject();
        $subject->setType(get_class($user));
        $subject->setIdentifier($user->getId());
        
        $created = $this->manager->createUser($user);
        $this->assertEquals($subject, $created);
        $this->em->flush();
        $this->assertNotNull($created->getId());
    }
    
    public function testFindUser() {
        $this->loadData('user');

        $user = $this->em->getRepository('Test:User')->findAll()[0];
        $this->assertNull($this->manager->findUser($user));
        
        $subject = $this->manager->createUser($user);
        $this->em->flush();
        $this->assertEquals($subject, $this->manager->findUser($user));
    }
}