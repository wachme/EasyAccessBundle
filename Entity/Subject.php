<?php

namespace Wachme\Bundle\EasyAccessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subject
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Subject
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
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $type;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $identifier;

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
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }
    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier) {
        $this->identifier = $identifier;
    }
    /**
     * @return string
     */
    public function getIdentifier() {
        return $this->identifier;
    }
}
