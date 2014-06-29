<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Cache\ArrayCache;

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
     * Flat representation of child targets.
     * Contains all nested targets.
     * 
     * @var ArrayCollection
     * 
     * @ORM\ManyToMany(targetEntity="Target", inversedBy="ancestors")
     */
    private $children;
    /**
     * @var ArrayCollection
     * 
     * @ORM\ManyToMany(targetEntity="Target", mappedBy="children")
     */
    private $ancestors;
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="Rule", mappedBy="target")
     */
    private $rules;
    
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
     * @param ArrayCollection $children
     */
    public function setChildren($children) {
        $this->children = $children;
    }
    /**
     * {@inheritdoc}
     */
    public function getChildren() {
        return $this->children;
    }
    /**
     * {@inheritdoc}
     */
    public function getAncestors() {
        return $this->ancestors;
    }
    /**
     * {@inheritdoc}
     */
    public function getRules() {
        return $this->rules;
    }
    
    public function addChild($target) {
        $this->getChildren()->add($target);
        $target->getAncestors()->add($this);
    }
    
    public function __construct() {
        $this->children = new ArrayCollection();
        $this->ancestors = new ArrayCollection();
    }
}
