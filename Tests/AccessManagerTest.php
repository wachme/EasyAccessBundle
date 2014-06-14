<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests;

use Wachme\Bundle\EasyAccessBundle\Tests\DbTestCase;
use Wachme\Bundle\EasyAccessBundle\AccessManager;
use Wachme\Bundle\EasyAccessBundle\Manager\TargetManager;
use Wachme\Bundle\EasyAccessBundle\Manager\SubjectManager;
use Wachme\Bundle\EasyAccessBundle\Manager\RuleManager;
use Wachme\Bundle\EasyAccessBundle\Attribute\AttributeMap;

class AccessManagerTest extends DbTestCase {
    private $manager;
    
    protected function setUp() {
        parent::setUp();
        
        $this->manager = static::$kernel->getContainer()->get('easy_access');
    }
    
    public function testAllow() {
        $this->loadData('user');
        $this->loadData('post');
        $this->loadData('special_post');
        
        $user = $this->em->getRepository('Test:User')->findAll()[0];
        $post = $this->em->getRepository('Test:Post')->findAll()[0];
        $specialPost = $this->em->getRepository('Test:SpecialPost')->findAll()[0];
        $class = get_class($specialPost);
        $parentClass = get_class($post);
        
        $this->assertFalse($this->manager->isAllowed($class, $user, 'view'));
        $this->assertFalse($this->manager->isAllowed($post, $user, 'view'));
        
        $this->manager->allow($parentClass, $user, 'owner');
        $this->assertTrue($this->manager->isAllowed($parentClass, $user, 'view'));
        $this->assertTrue($this->manager->isAllowed($class, $user, 'view'));
        $this->assertTrue($this->manager->isAllowed($post, $user, 'edit'));
        $this->assertTrue($this->manager->isAllowed($specialPost, $user, 'edit'));
        $this->assertTrue($this->manager->isAllowed([$specialPost, 'title'], $user, 'operator'));
        
        $this->manager->allow($post, $user, ['view', 'edit']);
        $this->assertTrue($this->manager->isAllowed($specialPost, $user, 'owner'));
        $this->assertTrue($this->manager->isAllowed($post, $user, ['view', 'edit']));
        $this->assertFalse($this->manager->isAllowed($post, $user, ['view', 'edit', 'create']));
        $this->assertFalse($this->manager->isAllowed([$post, 'title'], $user, 'operator'));
        $this->assertTrue($this->manager->isAllowed($class, $user, 'master'));
    }
}