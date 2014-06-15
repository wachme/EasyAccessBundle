<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClassTarget
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ClassTarget extends Target {
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     */
	private $name;
	
	public function setName($name) {
	    $this->name = $name;
	}
	
	public function getName() {
	    return $this->name;
	}
}