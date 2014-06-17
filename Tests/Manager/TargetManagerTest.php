<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests\Manager;

use Wachme\Bundle\EasyAccessBundle\Tests\DbTestCase;
use Wachme\Bundle\EasyAccessBundle\Manager\TargetManager;
use Wachme\Bundle\EasyAccessBundle\Entity\Target;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\FieldTarget;
use Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post;
use Wachme\Bundle\EasyAccessBundle\Entity\ClassFieldTarget;
use Wachme\Bundle\EasyAccessBundle\Entity\ObjectFieldTarget;
use Doctrine\ORM\QueryBuilder;
use Wachme\Bundle\EasyAccessBundle\Entity\Rule;
use Wachme\Bundle\EasyAccessBundle\Entity\Subject;

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
        $this->em->flush();
        $this->assertNotNull($created->getId());
    }

    public function testFindClass() {
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\SpecialPost';
    
        $this->assertNull($this->manager->findClass($class));

        $target = $this->manager->createClass($class);
        $this->em->flush();
        $this->assertEquals($target, $this->manager->findClass($class));
    }
    
    public function testCreateObject() {
        $this->loadData('post');
        
        $object = $this->em->getRepository('Test:Post')->findAll()[0];
        
        $classTarget = new ClassTarget();
        $classTarget->setName(get_class($object));
        $target = new ObjectTarget();
        $target->setIdentifier($object->getId());
        $target->setClass($classTarget);
        
        $created = $this->manager->createObject($object);
        $this->assertEquals($target, $created);
        $this->em->flush();
        $this->assertNotNull($created->getId());
    }
    
    public function testFindObject() {
        $this->loadData('post');
        $this->loadData('post');
        $this->loadData('post');
    
        $object = $this->em->getRepository('Test:Post')->findAll()[1];

        $this->assertNull($this->manager->findObject($object));
        
        $target = $this->manager->createObject($object);
        $this->em->flush();
        $this->assertEquals($target, $this->manager->findObject($object));
    }
    
    public function testCreateClassField() {
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post';
        $field = 'title';
        
        $classTarget = new ClassTarget();
        $classTarget->setName($class);
        $target = new ClassFieldTarget();
        $target->setClass($classTarget);
        $target->setName($field);
        
        $created = $this->manager->createClassField($class, $field);
        $this->assertEquals($target, $created);
        $this->em->flush();
        $this->assertNotNull($created->getId());
    }
    
    public function testFindClassField() {
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post';
        $field = 'title';

        $this->assertNull($this->manager->findClassField($class, $field));
        
        $target = $this->manager->createClassField($class, $field);
        $this->em->flush();
        $this->assertEquals($target, $this->manager->findClassField($class, $field));
    }
    
    public function testCreateObjectField() {
        $this->loadData('post');
        $field = 'title';
        $object = $this->em->getRepository('Test:Post')->findAll()[0];
        
        $classTarget = new ClassTarget();
        $classTarget->setName(get_class($object));
        $objectTarget = new ObjectTarget();
        $objectTarget->setIdentifier($object->getId());
        $objectTarget->setClass($classTarget);
        $target = new ObjectFieldTarget();
        $target->setObject($objectTarget);
        $target->setName($field);
        
        $created = $this->manager->createObjectField($object, $field);
        $this->assertEquals($target, $created);
        $this->em->flush();
        $this->assertNotNull($created->getId());
    }
    
    public function testFindObjectField() {
        $this->loadData('post');
        $this->loadData('post');
        $this->loadData('post');
        
        $field = 'title';
        $object = $this->em->getRepository('Test:Post')->findAll()[1];
        
        $this->assertNull($this->manager->findObjectField($object, $field));
        
        $target = $this->manager->createObjectField($object, $field);
        $this->em->flush();
        $this->assertEquals($target, $this->manager->findObjectField($object, $field));
    }
}