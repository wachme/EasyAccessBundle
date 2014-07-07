<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

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
abstract class Target {
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
     * @param ArrayCollection $children
     */
    public function setChildren($children) {
        $this->children = $children;
    }
    /**
     * @return ArrayCollection
     */
    public function getChildren() {
        return $this->children;
    }
    /**
     * @return ArrayCollection
     */
    public function getAncestors() {
        return $this->ancestors;
    }
    /**
     * @return ArrayCollection
     */
    public function getRules() {
        return $this->rules;
    }
    /**
     * @param Target $target
     */
    public function addChild(Target $target) {
        if($this->getChildren()->contains($target))
            return;
        
        $this->getChildren()->add($target);
    }
    /**
     * @param Target $parent
     */
    public function setParent(Target $parent) {
        $this->parent = $parent;
    }
    /**
     * @return Target
     */
    public function getParent() {
        return $this->parent;
    }
    
    public function __construct() {
        $this->children = new ArrayCollection();
        $this->ancestors = new ArrayCollection();
    }
}
