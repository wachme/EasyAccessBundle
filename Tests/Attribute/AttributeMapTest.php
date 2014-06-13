<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests\Attribute;

use Wachme\Bundle\EasyAccessBundle\Attribute\AttributeMap;

class AttributeMapTest extends \PHPUnit_Framework_TestCase {
    private $map;
    
    protected function setUp() {
        $this->map = new AttributeMap();
    }
    
    /** @dataProvider maskProvider */
    public function testGetMask($mask, $attrs) {
        $this->assertEquals($mask, $this->map->getMask($attrs));
    }
    
    /** @expectedException Wachme\Bundle\EasyAccessBundle\Attribute\UnknownAttributeException */
    public function testGetMaskException() {
        $this->map->getMask(['view', 'unknown', 'edit']);
    }
    
    /** @dataProvider attributeProvider */
    public function testGetAttributes($attrs, $mask) {
        $this->assertEquals($attrs, $this->map->getAttributes($mask));
    }
    
    public function maskProvider() {
        return [
            [0, []],
	        [0b00000001, ['view']],
	        [0b00000010, ['CrEATe']],
	        [0b00000011, ['view', 'create']],
	        [0b00011111, ['view', 'create', 'edit', 'delete', 'undelete']],
	        [0b00111111, ['edit', 'operator']],
	        [0b11111111, ['owner']]
        ];
    }
    
    public function attributeProvider() {
        return [
            [[], 0],
            [['view'], 0b00000001],
            [['view', 'create'], 0b00000011],
            [['view', 'create', 'edit', 'delete', 'undelete'], 0b00011111],
            [['view', 'create', 'edit', 'delete', 'undelete', 'operator'], 0b00111111],
            [['view', 'create', 'edit', 'delete', 'undelete', 'operator', 'master', 'owner'], 0b11111111]
        ];
    }
}