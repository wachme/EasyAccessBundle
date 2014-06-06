<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests\Manager;

use Wachme\Bundle\EasyAccessBundle\Manager\TargetManager;
use Wachme\Bundle\EasyAccessBundle\Entity\Target;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\FieldTarget;
use Wachme\Bundle\EasyAccessBundle\Tests\Models\Post;

class TargetManagerTest extends \PHPUnit_Framework_TestCase {
    private $manager;
    private $em;
    
    public function setUp() {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->manager = new TargetManager($this->em);
    }

    public function testCreateClass() {
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Models\Post';
        $target = new ClassTarget();
        $target->setName($class);
        
        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($target));
        
        $created = $this->manager->createClass($class);
        $this->assertEquals($target, $created);
        
        return $created;
    }

    /** @expectedException InvalidArgumentException */
    public function testCreateObjectException() {
        $invalid = new \stdClass();
        $this->manager->createObject($invalid);
    }
    
    /** @depends testCreateClass */
    public function testCreateObject($parentTarget) {      
        $object = new Post(10);
        $target = new ObjectTarget();
        $target->setName($object->getId());
        $target->setParent($parentTarget);
        
        $this->em->expects($this->at(0))
            ->method('persist')
            ->with($this->equalTo($parentTarget));
        
        $this->em->expects($this->at(1))
            ->method('persist')
            ->with($this->equalTo($target));
        
        $created = $this->manager->createObject($object);
        $this->assertEquals($target, $created);
        
        return $created;
    }
    
    /**
     * @depends testCreateClass
     */
    public function testCreateClassField($parentTarget) {
        $field = 'title';
        $target = new FieldTarget();
        $target->setName($field);
        $target->setParent($parentTarget);
        
        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($target));
        
        $created = $this->manager->createField($parentTarget, $field);
        $this->assertEquals($target, $created);
    }
    
    /**
     * @depends testCreateObject
     */
    public function testCreateObjectField($parentTarget) {
        $field = 'title';
        $target = new FieldTarget();
        $target->setName($field);
        $target->setParent($parentTarget);
    
        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($target));
    
        $created = $this->manager->createField($parentTarget, $field);
        $this->assertEquals($target, $created);
    }
}