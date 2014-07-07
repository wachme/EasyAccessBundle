<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests\Queue;

use Wachme\Bundle\EasyAccessBundle\Tests\DbTestCase;
use Wachme\Bundle\EasyAccessBundle\Queue\TargetQueue;
use Wachme\Bundle\EasyAccessBundle\Queue\Strategy\ClassQueueStrategy;
use Wachme\Bundle\EasyAccessBundle\Queue\Strategy\ObjectQueueStrategy;
use Wachme\Bundle\EasyAccessBundle\Queue\Strategy\ClassFieldQueueStrategy;
use Wachme\Bundle\EasyAccessBundle\Queue\Strategy\ObjectFieldQueueStrategy;
use Wachme\Bundle\EasyAccessBundle\Manager\TargetManager;

class TargetQueueTest extends DbTestCase {
    /**
     * @var TargetQueue
     */
    private $targetQueue;
    /**
     * @var TargetManager
     */
    private $targetManager;
    
    protected function setUp() {
        parent::setUp();
        
        $this->targetQueue = new TargetQueue(
	        new ClassQueueStrategy(),
            new ObjectQueueStrategy(),
            new ClassFieldQueueStrategy(),
            new ObjectFieldQueueStrategy()
        );
        $this->targetManager = new TargetManager($this->em);
    }
    
    public function test() {
        $this->loadData('post');
        $this->loadData('post');
        
        $object = $this->em->getRepository('Test:Post')->findAll()[0];
        $object2 = $this->em->getRepository('Test:Post')->findAll()[1];
        
        $target = $this->targetManager->createObjectField($object, 'title');
        $parent = $this->targetManager->createObjectField($object2, 'content');
        $target->getClassField()->setParent($parent);
        
        $queue = $this->targetQueue->getQueue($target);
        var_dump($queue);
    }
}