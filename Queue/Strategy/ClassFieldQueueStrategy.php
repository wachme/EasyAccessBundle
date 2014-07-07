<?php

namespace Wachme\Bundle\EasyAccessBundle\Queue\Strategy;

class ClassFieldQueueStrategy extends QueueStrategy {
    public function getFlatQueue($target) {
        return [$target->getClass(), $target];
    }
}