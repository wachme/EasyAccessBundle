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
    
    public function testFindClassSet() {
        $this->loadData('user');
        $user = $this->em->getRepository('Test:User')->findAll()[0];
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post';
        
        $this->assertNull($this->manager->findClassSet($class, $user));
        
        $target = $this->manager->createClass($class);
        $this->em->flush();
        
        $this->assertEquals($target, $this->manager->findClassSet($class, $user));
    }
    
    public function testFindObjectSet() {
        $this->loadData('post');
        $this->loadData('user');
    
        $object = $this->em->getRepository('Test:Post')->findAll()[0];
        $user = $this->em->getRepository('Test:User')->findAll()[0];
        $class = get_class($object);
        
        $this->assertNull($this->manager->findObjectSet($object, $user));
        
        $classTarget = $this->manager->createClass($class);
        $this->em->flush();
        
        $found = $this->manager->findObjectSet($object, $user);
        $this->assertEquals($classTarget, $found);
        
        $target = $this->manager->createObject($object);
        $this->em->flush();
        $this->em->clear();
        
        $found = $this->manager->findObjectSet($object, $user);
        $this->assertInstanceOf(get_class($target), $found);
        $this->assertEquals($target->getId(), $found->getId());
    }
    
    public function testFindClassFieldSet() {
        $this->loadData('user');
        $user = $this->em->getRepository('Test:User')->findAll()[0];
        $class = 'Wachme\Bundle\EasyAccessBundle\Tests\Fixtures\Entity\Post';
        $field = 'title';
        
        $this->assertNull($this->manager->findClassFieldSet($class, $field, $user));
        
        $classTarget = $this->manager->createClass($class);
        $this->em->flush();
        
        $found = $this->manager->findClassFieldSet($class, $field, $user);
        $this->assertEquals($classTarget, $found);
        
        $target = $this->manager->createClassField($class, $field);
        $this->em->flush();
        $this->em->clear();
        
        $found = $this->manager->findClassFieldSet($class, $field, $user);
        $this->assertInstanceOf(get_class($target), $found);
        $this->assertEquals($target->getId(), $found->getId());
    }
    
    public function testFindObjectFieldSet() {
        $this->loadData('user');
        $this->loadData('post');
        $user = $this->em->getRepository('Test:User')->findAll()[0];
        $object = $this->em->getRepository('Test:Post')->findAll()[0];
        $class = get_class($object);
        $field = 'title';
        
        $this->assertNull($this->manager->findObjectFieldSet($object, $field, $user));
        
        $classTarget = $this->manager->createClass($class);
        $this->em->flush();
        
        $found = $this->manager->findObjectFieldSet($object, $field, $user);
        $this->assertEquals($classTarget, $found);
        
        $classFieldTarget = $this->manager->createClassField($class, $field);
        $this->em->flush();
        $this->em->clear();
        
        $found = $this->manager->findObjectFieldSet($object, $field, $user);
        $this->assertInstanceOf(get_class($classFieldTarget), $found);
        $this->assertEquals($classFieldTarget->getId(), $found->getId());
        
        $objectTarget = $this->manager->createObject($object);
        $this->em->flush();
        $this->em->clear();
        
        $found = $this->manager->findObjectFieldSet($object, $field, $user);
        $this->assertInstanceOf(get_class($objectTarget), $found);
        $this->assertEquals($objectTarget->getId(), $found->getId());
        
        $target = $this->manager->createObjectField($object, $field);
        $this->em->flush();
        $this->em->clear();
        
        $found = $this->manager->findObjectFieldSet($object, $field, $user);
        $this->assertInstanceOf(get_class($target), $found);
        $this->assertEquals($target->getId(), $found->getId());
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