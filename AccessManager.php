<?php

namespace Wachme\Bundle\EasyAccessBundle;
use Wachme\Bundle\EasyAccessBundle\Model\TargetManagerInterface;
use Wachme\Bundle\EasyAccessBundle\Model\SubjectManagerInterface;
use Wachme\Bundle\EasyAccessBundle\Model\RuleManagerInterface;
use Wachme\Bundle\EasyAccessBundle\Manager\TargetExistsException;
use Wachme\Bundle\EasyAccessBundle\Manager\SubjectExistsException;
use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;
use Wachme\Bundle\EasyAccessBundle\Model\SubjectInterface;

class AccessManager {
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
     * @param string|array|object $element
     * @throws \InvalidArgumentException
     * @return TargetInterface
     */
    private function getTarget($element) {
        switch(gettype($element)) {
        	case 'string':
        	    try {
        	        return $this->targetManager->createClass($element);
        	    } catch(TargetExistsException $e) {
        	        return $this->targetManager->findByClass($element, false);
        	    }
        	    break;
        	case 'array':
        	    if(count($element) != 2)
        	        throw new \InvalidArgumentException();
        	     
        	    if(is_string($element[0])) {
        	        if(is_string($element[1])) {
        	            try {
        	                return $this->targetManager->createClassField($element[0], $element[1]);
        	            } catch(TargetExistsException $e) {
        	                return $this->targetManager->findByClassField($element[0], $element[1], false);
        	            }
        	        }
        	        else
        	            throw new \InvalidArgumentException();
        	    }
        	    elseif(is_object($element[0])) {
        	        if(is_string($element[1])) {
        	            try {
        	                return $this->targetManager->createObjectField($element[0], $element[1]);
        	            } catch(TargetExistsException $e) {
        	                return $this->targetManager->findByObjectField($element[0], $element[1], false);
        	            }
        	        }
        	        else
        	            throw new \InvalidArgumentException();
        	    }
        	    else
        	        throw new \InvalidArgumentException();
        	    break;
        	case 'object':
        	    try {
        	        return $this->targetManager->createObject($element);
        	    } catch(TargetExistsException $e) {
        	        return $this->targetManager->findByObject($element, false);
        	    }
        	    break;
        	default:
        	    throw new \InvalidArgumentException();
        }
    }
    /**
     * @param object $user
     * @return SubjectInterface
     */
    private function getSubject($user) {
        try {
            return $this->subjectManager->createUser($user);
        } catch(SubjectExistsException $e) {
            return $this->subjectManager->findByUser($user);
        }
    }
    
    /**
     * @param TargetManagerInterface $targetManager
     * @param SubjectManagerInterface $subjectManager
     * @param RuleManagerInterface $ruleManager
     */
    public function __construct(TargetManagerInterface $targetManager, SubjectManagerInterface $subjectManager, RuleManagerInterface $ruleManager) {
        $this->targetManager = $targetManager;
        $this->subjectManager = $subjectManager;
        $this->ruleManager = $ruleManager;
    }
    /**
     * @param string|array|object $element
     * @param object $user
     * @param array $attributes
     */
    public function grant($element, $user, $attributes) {
        $target = $this->getTarget($element);
        $subject = $this->getSubject($user);
    }
}