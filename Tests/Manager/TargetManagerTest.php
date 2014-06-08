<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests\Manager;

use Wachme\Bundle\EasyAccessBundle\Tests\DbTestCase;
use Wachme\Bundle\EasyAccessBundle\Manager\TargetManager;
use Wachme\Bundle\EasyAccessBundle\Entity\Target;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\FieldTarget;
use Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post;

class TargetManagerTest extends DbTestCase {
    private $manager;
    
    private function getPost() {
        return $this->em->getRepository('Test:Post')->findAll()[0];
    }
    
    protected function setUp() {
        parent::setUp();
        $this->manager = new TargetManager($this->em);
    }

    public function testCreateClass() {
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post';
        $target = new ClassTarget();
        $target->setName($class);
        
        $created = $this->manager->createClass($class);
        $this->assertEquals($target, $created);
        $this->manager->save();
        $this->assertNotNull($created->getId());
        
        return $created;
    }

    /** @expectedException InvalidArgumentException */
    public function testCreateObjectException() {
        $invalid = new \stdClass();
        $this->manager->createObject($invalid);
    }
    
    public function testCreateObject() {
        $this->loadData('post');
        $parentTarget = $this->testCreateClass();
        $object = $this->getPost();
        $target = new ObjectTarget();
        $target->setName($object->getId());
        $target->setParent($parentTarget);
        
        $created = $this->manager->createObject($object);
        $this->assertEquals($target, $created);
        $this->manager->save();
        $this->assertNotNull($created->getId());
        
        return $created;
    }

    public function testCreateClassField() {

    }
    

    public function testCreateObjectField() {

    }
}