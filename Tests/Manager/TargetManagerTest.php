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

    public function testCreateClass($class=null) {
        if(!$class)
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
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post';
        $field = 'title';
        $parentTarget = $this->testCreateClass();
        $target = new FieldTarget();
        $target->setParent($parentTarget);
        $target->setName($field);
        
        $created = $this->manager->createClassField($class, $field);
        $this->assertEquals($target, $created);
        $this->manager->save();
        $this->assertNotNull($created->getId());
        
        return $created;
    }
    
    public function testCreateObjectField() {
        $field = 'title';
        $parentTarget = $this->testCreateObject();
        $object = $this->getPost();
        $target = new FieldTarget();
        $target->setParent($parentTarget);
        $target->setName($field);
        
        $created = $this->manager->createObjectField($object, $field);
        $this->assertEquals($target, $created);
        $this->manager->save();
        $this->assertNotNull($created->getId());
        
        return $created;
    }
    
    public function testFindByClass() {
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\SpecialPost';
        $parentClass = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post';
        
        $this->assertNull($this->manager->findByClass($class));
        
        $parentTarget = $this->manager->createClass($parentClass);
        $this->manager->save();
        $this->assertEquals($parentTarget, $this->manager->findByClass($class));
        $this->assertNull($this->manager->findByClass($class, false));
        
        $target = $this->manager->createClass($class);
        $this->manager->save();
        $this->assertEquals($target, $this->manager->findByClass($class));
        $this->assertEquals($target, $this->manager->findByClass($class, false));
    }
    
    public function testFindByObject() {
        $this->loadData('post');
        $this->loadData('special_post');
        
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\SpecialPost';
        $parentClass = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post';
        $object = $this->em->getRepository('Test:SpecialPost')->findAll()[0];
        $this->assertNull($this->manager->findByObject($object));
        
        $parentClassTarget = $this->manager->createClass($parentClass) ;
        $this->manager->save();
        $this->assertEquals($parentClassTarget, $this->manager->findByObject($object));
        $this->assertNull($this->manager->findByObject($object, false));
        
        // Object of a class does not inherit target from parent class object:
        $parentClassObject = $this->getPost();
        $this->manager->createObject($parentClassObject);
        $this->manager->save();
        $this->assertEquals($parentClassTarget, $this->manager->findByObject($object));
        $this->assertNull($this->manager->findByObject($object, false));

        $classTarget = $this->manager->createClass($class);
        $this->manager->save();
        $this->assertEquals($classTarget, $this->manager->findByObject($object));
        $this->assertNull($this->manager->findByObject($object, false));
        
        $target = $this->manager->createObject($object);
        $this->manager->save();
        $this->assertEquals($target, $this->manager->findByObject($object));
        $this->assertEquals($target, $this->manager->findByObject($object, false));
    }
    
    public function testFindByClassField() {
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\SpecialPost';
        $parentClass = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post';
        $field = 'title';
        $this->assertNull($this->manager->findByClassField($class, $field));
        
        $parentClassTarget = $this->manager->createClass($parentClass);
        $this->manager->save();
        $this->assertEquals($parentClassTarget, $this->manager->findByClassField($class, $field));
        $this->assertNull($this->manager->findByClassField($class, $field, false));
        
        $parentClassFieldTarget = $this->manager->createClassField($parentClass, $field);
        $this->manager->save();
        $this->assertEquals($parentClassFieldTarget, $this->manager->findByClassField($class, $field));
        $this->assertNull($this->manager->findByClassField($class, $field, false));
        
        $classTarget = $this->manager->createClass($class);
        $this->manager->save();
        $this->assertEquals($classTarget, $this->manager->findByClassField($class, $field));
        $this->assertNull($this->manager->findByClassField($class, $field, false));
        
        $target = $this->manager->createClassField($class, $field);
        $this->manager->save();
        $this->assertEquals($target, $this->manager->findByClassField($class, $field));
        $this->assertEquals($target, $this->manager->findByClassField($class, $field, false));
    }
    
    public function testFindByObjectField() {
        $this->loadData('post');
        $this->loadData('special_post');
        
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\SpecialPost';
        $parentClass = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post';
        $object = $this->em->getRepository('Test:SpecialPost')->findAll()[0];
        $field = 'title';
        $this->assertNull($this->manager->findByObjectField($object, $field));
        
        $parentClassTarget = $this->manager->createClass($parentClass);
        $this->manager->save();
        $this->assertEquals($parentClassTarget, $this->manager->findByObjectField($object, $field));
        $this->assertNull($this->manager->findByObjectField($object, $field, false));
        
        $parentClassFieldTarget = $this->manager->createClassField($parentClass, $field);
        $this->manager->save();
        $this->assertEquals($parentClassFieldTarget, $this->manager->findByObjectField($object, $field));
        $this->assertNull($this->manager->findByObjectField($object, $field, false));
        
        $classTarget = $this->manager->createClass($class);
        $this->manager->save();
        $this->assertEquals($classTarget, $this->manager->findByObjectField($object, $field));
        $this->assertNull($this->manager->findByObjectField($object, $field, false));
        
        $classFieldTarget = $this->manager->createClassField($class, $field);
        $this->manager->save();
        $this->assertEquals($classFieldTarget, $this->manager->findByObjectField($object, $field));
        $this->assertNull($this->manager->findByObjectField($object, $field, false));
        
        $objectTarget = $this->manager->createObject($object);
        $this->manager->save();
        $this->assertEquals($objectTarget, $this->manager->findByObjectField($object, $field));
        $this->assertNull($this->manager->findByObjectField($object, $field, false));
        
        $target = $this->manager->createObjectField($object, $field);
        $this->manager->save();
        $this->assertEquals($target, $this->manager->findByObjectField($object, $field));
        $this->assertEquals($target, $this->manager->findByObjectField($object, $field, false));
    }
}