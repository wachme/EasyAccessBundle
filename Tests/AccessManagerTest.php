<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests;

use Wachme\Bundle\EasyAccessBundle\Tests\DbTestCase;
use Wachme\Bundle\EasyAccessBundle\AccessManager;
use Wachme\Bundle\EasyAccessBundle\Manager\TargetManager;
use Wachme\Bundle\EasyAccessBundle\Manager\SubjectManager;
use Wachme\Bundle\EasyAccessBundle\Manager\RuleManager;
use Wachme\Bundle\EasyAccessBundle\Attribute\AttributeMap;
use Doctrine\DBAL\Logging\DebugStack;

class AccessManagerTest extends DbTestCase {
    /**
     * @var AccessManager
     */
    private $manager;
    
    protected function setUp() {
        parent::setUp();
        
        $this->manager = new AccessManager(
            $this->em,
	        new TargetManager($this->em),
            new SubjectManager($this->em),
            new RuleManager($this->em),
            new AttributeMap()
        );
    }
    
    public function testAllow() {
        $this->loadData('post');
        $this->loadData('user');
        
        $user = $this->em->getRepository('Test:User')->findAll()[0];
        $object = $this->em->getRepository('Test:Post')->findAll()[0];
        $class = get_class($object);
        
        $this->manager->allow($class, $user, 'view');
        $this->manager->allow([$class, 'title'], $user, 'edit');
        $this->manager->deny($object, $user, 'edit');
        $this->em->clear();
        
        $this->assertTrue($this->manager->isAllowed([$object, 'title'], $user, 'view'));
        $this->assertFalse($this->manager->isAllowed([$object, 'title'], $user, 'edit'));
    }
}