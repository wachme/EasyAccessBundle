<?php

namespace Wachme\Bundle\EasyAccessBundle;

use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Model\TargetManagerInterface;
use Wachme\Bundle\EasyAccessBundle\Model\SubjectManagerInterface;
use Wachme\Bundle\EasyAccessBundle\Model\RuleManagerInterface;
use Wachme\Bundle\EasyAccessBundle\Attribute\AttributeMap;
use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Wachme\Bundle\EasyAccessBundle\Model\SubjectInterface;
use Wachme\Bundle\EasyAccessBundle\Model\RuleInterface;

class AccessManager {
    
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var TargetManagerInterface
     */
    private $targetManager;
    /**
     * @var SubjectManagerInterface
     */
    private $subjectManager;
    /**
     * @var RuleManagerInterface
     */
    private $ruleManager;
    /**
     * @var AttributeMap
     */
    private $attributeMap;

    /**
     * @param string|array|object $element
     * @param boolean $recursive
     * @param boolean $create
     * @throws \InvalidArgumentException
     * @return TargetInterface|null
     */
    private function getTarget($element, $recursive=true, $create=false) {
        switch(gettype($element)) {
        	case 'string':
        	    if($target = $this->targetManager->findByClass($element, $recursive))
        	        return $target;
        	    if($create)
        	        return $this->targetManager->createClass($element);
        	    break;
        	    
        	case 'array':
        	    if(count($element) != 2)
        	        throw new \InvalidArgumentException();
        	     
        	    if(is_string($element[0])) {
        	        if(is_string($element[1])) {
        	            if($target = $this->targetManager->findByClassField($element[0], $element[1], $recursive))
        	                return $target;
        	            if($create)
        	                return $this->targetManager->createClassField($element[0], $element[1]);
        	        }
        	        else
        	            throw new \InvalidArgumentException();
        	    }
        	    elseif(is_object($element[0])) {
        	        if(is_string($element[1])) {
        	            if($target = $this->targetManager->findByObjectField($element[0], $element[1], $recursive))
        	                return $target;
        	            if($create)
        	                return $this->targetManager->createObjectField($element[0], $element[1]);
        	        }
        	        else
        	            throw new \InvalidArgumentException();
        	    }
        	    else
        	        throw new \InvalidArgumentException();
        	    break;
        	    
        	case 'object':
        	    if($target = $this->targetManager->findByObject($element, $recursive))
        	        return $target;
        	    if($create)
        	        return $this->targetManager->createObject($element);
        	    break;
        	    
        	default:
        	    throw new \InvalidArgumentException();
        }
    }
    /**
     * @param object $user
     * @param boolean $create
     * @return SubjectInterface|null
     */
    private function getSubject($user, $create=false) {
        if($subject = $this->subjectManager->findByUser($user))
            return $subject;
        if($create)
            return $this->subjectManager->createUser($user);
    }
    /**
     * 
     * @param TargetInterface $target
     * @param SubjectInterface $subject
     * @param boolean $create
     * @return RuleInterface|null
     */
    private function getRule(TargetInterface $target, SubjectInterface $subject, $create=false) {
        if($rule = $this->ruleManager->find($target, $subject))
            return $rule;
        if($create)
            return $this->ruleManager->create($target, $subject);
    }

    /**
     * @param TargetManagerInterface $targetManager
     * @param SubjectManagerInterface $subjectManager
     * @param RuleManagerInterface $ruleManager
     * @param AttributeMap $attributeMap
     */
    public function __construct(EntityManager $em, TargetManagerInterface $targetManager, SubjectManagerInterface $subjectManager, RuleManagerInterface $ruleManager, AttributeMap $attributeMap) {
        $this->em = $em;
        $this->targetManager = $targetManager;
        $this->subjectManager = $subjectManager;
        $this->ruleManager = $ruleManager;
        $this->attributeMap = $attributeMap;
    }
    /**
     * @param string|array|object $element
     * @param object $user
     * @param string|array $attributes
     */
    public function allow($element, $user, $attributes) {
        if(!is_array($attributes))
            $attributes = [$attributes];
        
        $target = $this->getTarget($element, false, true);
        $subject = $this->getSubject($user, true);
        $rule = $this->getRule($target, $subject, true);
        
        $mask = $this->attributeMap->getMask($attributes);
        $rule->setMask($mask);
        
        $this->em->flush();
    }
}