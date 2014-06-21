<?php

namespace Wachme\Bundle\EasyAccessBundle;

use Doctrine\ORM\EntityManager;
use Wachme\Bundle\EasyAccessBundle\Model\TargetManagerInterface;
use Wachme\Bundle\EasyAccessBundle\Model\SubjectManagerInterface;
use Wachme\Bundle\EasyAccessBundle\Model\RuleManagerInterface;
use Wachme\Bundle\EasyAccessBundle\Attribute\AttributeMap;
use Wachme\Bundle\EasyAccessBundle\Model\TargetInterface;

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
     * @param callable $classFn
     * @param callable $objectFn
     * @param callable $classFieldFn
     * @param callable $objectFieldFn
     * @throws \InvalidArgumentException
     * @return mixed
     */
    private function resolveTarget($element, callable $classFn, callable $objectFn, callable $classFieldFn, callable $objectFieldFn) {
        switch(gettype($element)) {
        	case 'string':
        	    return $classFn($element);
        	    
        	case 'array':
        	    if(count($element) != 2)
        	        throw new \InvalidArgumentException();
        	     
        	    if(is_string($element[0])) {
        	        if(is_string($element[1]))
        	            return $classFieldFn($element[0], $element[1]);
        	        else
        	            throw new \InvalidArgumentException();
        	    }
        	    elseif(is_object($element[0])) {
        	        if(is_string($element[1]))
        	            return $objectFieldFn($element[0], $element[1]);
        	        else
        	            throw new \InvalidArgumentException();
        	    }
        	    else
        	        throw new \InvalidArgumentException();
        	    break;
        	    
        	case 'object':
        	    return $objectFn($element);
        	    
        	default:
        	    throw new \InvalidArgumentException();
        }
    }
    /**
     * @param string|array|object $element
     * @return TargetInterface
     */
    private function findOrCreateTarget($element) {
        return $this->resolveTarget($element,
            [$this->targetManager, 'findOrCreateClass'],
            [$this->targetManager, 'findOrCreateObject'],
            [$this->targetManager, 'findOrCreateClassField'],
            [$this->targetManager, 'findOrCreateObjectField']);
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
        
        $target = $this->findOrCreateTarget($element);
        
        $subject = $this->subjectManager->findOrCreateUser($user);
        $rule = $this->ruleManager->findOrCreate($target, $subject);
        $mask = $this->attributeMap->getMask($attributes);
        $rule->setMask($mask);
        
        $this->em->flush();
    }
    /**
     * @param string|array|object $element
     * @param object $user
     * @param string|array $attributes
     * @return boolean
     */
    public function isAllowed($element, $user, $attributes) {
        if(!is_array($attributes))
            $attributes = [$attributes];
        
	    $target = $this->resolveTarget($element,
            function($class) use ($user) {
                return $this->targetManager->findClassSet($class, $user);
            },
            function($object) use ($user) {
                return $this->targetManager->findObjectSet($object, $user);
            },
            function($class, $field) use ($user) {
                return $this->targetManager->findClassFieldSet($class, $field, $user);
            },
            function($object, $field) use ($user) {
                return $this->targetManager->findObjectFieldSet($object, $field, $user);
            }
        );
	    
	    // TODO: make access decision
    }
    /**
     * @param string|array|object $element
     * @param string|array|object $parentElement
     */
    public function setParent($element, $parentElement) {
        $target = $this->findOrCreateTarget($element);
        $parentTarget = $this->findOrCreateTarget($parentElement);
        
        // TODO: use TargetManager's method
        
        $parentTarget->getChildren()->add($target);
        
        $this->em->flush();
    }
}