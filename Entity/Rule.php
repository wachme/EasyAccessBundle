<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Wachme\Bundle\EasyAccessBundle\Model\RuleInterface;
use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Wachme\Bundle\EasyAccessBundle\Model\SubjectInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Rule
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Rule implements RuleInterface
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param TargetInterface $target
     */
    public function setTarget(TargetInterface $target) {
        $this->target = $target;
    }
    /**
     * @return TargetInterface
     */
    public function getTarget() {
        return $this->target;
    }
    
    /**
     * @param SubjectInterface $subject
     */
    public function setSubject(SubjectInterface $subject) {
        $this->subject = $subject;
    }
    /**
     * @return SubjectInterface
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
}
