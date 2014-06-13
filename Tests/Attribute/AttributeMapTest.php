<?php

namespace Wachme\Bundle\EasyAccessBundle\Tests\Attribute;

use Wachme\Bundle\EasyAccessBundle\Attribute\AttributeMap;

class AttributeMapTest extends \PHPUnit_Framework_TestCase {
    private $map;
    
    protected function setUp() {
        $this->map = new AttributeMap();
    }
    
    /** @dataProvider provider */
    public function testGetMask($mask, $attr) {
        $this->assertEquals($mask, $this->map->getMask($attr));
    }
    
    /** @expectedException Wachme\Bundle\EasyAccessBundle\Attribute\UnknownAttributeException */
    public function testGetMaskException() {
        $this->map->getMask(['view', 'unknown', 'edit']);
    }
    
    public function provider() {
        return [
            [0, []],
	        [0b00000001, ['view']],
	        [0b00000010, ['CrEATe']],
	        [0b00000011, ['view', 'create']],
	        [0b00011111, ['edit', 'operator']]
        ];
    }
}