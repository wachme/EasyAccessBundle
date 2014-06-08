<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

class DbTestCase extends KernelTestCase {
    /**
     * @var EntityManager
     */
    protected $em;
    
    protected function setUp() {
        static::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->refresh();
    }
    
    protected function loadData($dataName) {
        $sql = file_get_contents(__DIR__ . "/Fixtures/data/{$dataName}.sql");
        $this->em->getConnection()->exec($sql);
    }
    
    protected function refresh() {
        $this->em->clear();
        $tool = new SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropDatabase();
        $tool->createSchema($classes);
    }
}