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
        $this->markTestSkipped('Not implemented yet');
    }
}