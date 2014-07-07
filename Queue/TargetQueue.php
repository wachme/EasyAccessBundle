<?php

namespace Wachme\Bundle\EasyAccessBundle\Queue;

class TargetQueue {
    private $strategies;
    
    public function __construct($classStrategy, $objectStrategy, $classFieldStrategy, $objectFieldStrategy) {
        $this->strategies = [
            'Wachme\\Bundle\\EasyAccessBundle\\Entity\\ClassTarget' => $classStrategy,
            'Wachme\\Bundle\\EasyAccessBundle\\Entity\\ObjectTarget' => $objectStrategy,
            'Wachme\\Bundle\\EasyAccessBundle\\Entity\\ClassFieldTarget' => $classFieldStrategy,
            'Wachme\\Bundle\\EasyAccessBundle\\Entity\\ObjectFieldTarget' => $objectFieldStrategy
        ];
    }
    
    public function getParentQueue($target) {
        if($parent = $target->getParent())
            return array_merge($this->getQueue($parent), [$target]);
        return [$target];
    }
    
    public function getQueue($target) {
        $class = get_class($target);
        return $this->strategies[$class]->getQueue($target, $this);
    }
}