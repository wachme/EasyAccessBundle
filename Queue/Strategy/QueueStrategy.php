<?php

namespace Wachme\Bundle\EasyAccessBundle\Queue\Strategy;

use Wachme\Bundle\EasyAccessBundle\Queue\TargetQueue;
use Wachme\Bundle\EasyAccessBundle\Entity\Target;

abstract class QueueStrategy {
    /**
     * @param Target $target
     * @return array
     */
    abstract public function getFlatQueue($target);

    public function getQueue($target, TargetQueue $targetQueue) {
        return call_user_func_array('array_merge', array_map(function($t) use ($targetQueue) {
            return $targetQueue->getParentQueue($t);
        }, $this->getFlatQueue($target)));
    }
}