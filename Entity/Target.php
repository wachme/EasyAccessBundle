<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Target
 *
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"class" = "ClassTarget", "object" = "ObjectTarget", "field" = "FieldTarget"})
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var Target
     * 
     * @ORM\ManyToOne(targetEntity="Target")
     */
    private $parent;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Target
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param Target $parent
     */
    public function setParent($parent) {
        $this->parent = $parent;
    }
    /**
     * @return Target
     */
    public function getParent() {
        return $this->parent;
    }
}
