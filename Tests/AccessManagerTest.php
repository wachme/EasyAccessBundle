<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests;

use Wachme\Bundle\EasyAccessBundle\Tests\DbTestCase;
use Wachme\Bundle\EasyAccessBundle\AccessManager;
use Wachme\Bundle\EasyAccessBundle\Manager\TargetManager;
use Wachme\Bundle\EasyAccessBundle\Manager\SubjectManager;
use Wachme\Bundle\EasyAccessBundle\Manager\RuleManager;
use Wachme\Bundle\EasyAccessBundle\Attribute\AttributeMap;

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
        $this->loadData('user');
        $this->loadData('user');
        $this->loadData('post');
        $this->loadData('special_post');
        
        $user = $this->em->getRepository('Test:User')->findAll()[0];
        $user2 = $this->em->getRepository('Test:User')->findAll()[1];
        $post = $this->em->getRepository('Test:Post')->findAll()[0];
        $specialPost = $this->em->getRepository('Test:SpecialPost')->findAll()[0];
        $class = get_class($specialPost);
        $parentClass = get_class($post);
        
        $this->manager->allow($parentClass, $user2, 'view');
        $this->manager->allow($class, $user, 'view');
        $this->manager->setParent($class, $parentClass);
        var_dump($this->manager->isAllowed($class, $user2, 'view')->getAncestors()[0]->getRules()->unwrap());
        
        $this->markTestIncomplete(); // ------------------ not implemented
        
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