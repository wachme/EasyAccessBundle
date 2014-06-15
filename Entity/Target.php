<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Target
 *
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"class" = "ClassTarget", "object" = "ObjectTarget", "class_field" = "ClassFieldTarget", "object_field" = "ObjectFieldTarget"})
 */
class Target implements TargetInterface {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var ArrayCollection
     * 
     * @ORM\ManyToMany(targetEntity="Target")
     */
    private $children;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function setChildren($children) {
        $this->children = $children;
    }
    
    public function getChildren() {
        return $this->children;
    }
}
