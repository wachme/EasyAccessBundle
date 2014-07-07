<?php

namespace Wachme\Bundle\EasyAccessBundle\Queue;

use Wachme\Bundle\EasyAccessBundle\Queue\Strategy\QueueStrategy;
use Wachme\Bundle\EasyAccessBundle\Entity\Target;

class TargetQueue {
    /**
     * @var array
     */
    private $strategies;
    
    /**
     * @param QueueStrategy $classStrategy
     * @param QueueStrategy $objectStrategy
     * @param QueueStrategy $classFieldStrategy
     * @param QueueStrategy $objectFieldStrategy
     */
    public function __construct(QueueStrategy $classStrategy, QueueStrategy $objectStrategy, QueueStrategy $classFieldStrategy, QueueStrategy $objectFieldStrategy) {
        $this->strategies = [
            'Wachme\\Bundle\\EasyAccessBundle\\Entity\\ClassTarget' => $classStrategy,
            'Wachme\\Bundle\\EasyAccessBundle\\Entity\\ObjectTarget' => $objectStrategy,
            'Wachme\\Bundle\\EasyAccessBundle\\Entity\\ClassFieldTarget' => $classFieldStrategy,
            'Wachme\\Bundle\\EasyAccessBundle\\Entity\\ObjectFieldTarget' => $objectFieldStrategy
        ];
    }
    /**
     * @param Target $target
     * @return array
     */
    public function getParentQueue(Target $target) {
        if($parent = $target->getParent())
            return array_merge($this->getQueue($parent), [$target]);
        return [$target];
    }
    /**
     * @param Target $target
     * @return array
     */
    public function getQueue(Target $target) {
        $class = get_class($target);
        return $this->strategies[$class]->getQueue($target, $this);
    }
}