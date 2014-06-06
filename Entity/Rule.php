<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rule
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Rule
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Target
     * 
     * @ORM\ManyToOne(targetEntity="Target")
     * @ORM\JoinColumn(nullable=false)
     */
    private $target;
    
    /**
     * @var Subject
     * 
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subject;
    
    /**
     * @var integer
     * 
     * @ORM\Column(type="integer")
     */
    private $mask;
    
    /**
     * @var Rule
     * 
     * @ORM\ManyToOne(targetEntity="Rule")
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
     * @param Target $target
     */
    public function setTarget($target) {
        $this->target = $target;
    }
    /**
     * @return Target
     */
    public function getTarget() {
        return $this->target;
    }
    
    /**
     * @param Subject $subject
     */
    public function setSubject($subject) {
        $this->subject = $subject;
    }
    /**
     * @return Subject
     */
    public function getSubject() {
        return $this->subject;
    }
    
    /**
     * @param integer $mask
     */
    public function setMask($mask) {
        $this->mask = $mask;
    }
    /**
     * @return integer
     */
    public function getMask() {
        return $this->mask;
    }
    
    /**
     * @param Rule $parent
     */
    public function setParent($parent) {
        $this->parent = $parent;
    }
    /**
     * @return Rule
     */
    public function getParent() {
        return $this->parent;
    }
}
