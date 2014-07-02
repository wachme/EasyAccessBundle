<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wachme\Bundle\EasyAccessBundle\Entity\Target;
use Wachme\Bundle\EasyAccessBundle\Entity\Subject;

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
     * @ORM\ManyToOne(targetEntity="Target", inversedBy="rules")
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
    private $allowMask = 0;
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $denyMask = 0;

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
    public function setTarget(Target $target) {
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
    public function setSubject(Subject $subject) {
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
    public function setAllowMask($allowMask) {
        $this->allowMask = $allowMask;
    }
    /**
     * @return integer
     */
    public function getAllowMask() {
        return $this->allowMask;
    }
    
    /**
     * @param integer $mask
     */
    public function setDenyMask($denyMask) {
        $this->denyMask = $denyMask;
    }
    /**
     * @return integer
     */
    public function getDenyMask() {
        return $this->denyMask;
    }
}
