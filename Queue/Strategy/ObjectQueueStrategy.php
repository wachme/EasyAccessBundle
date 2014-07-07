<?php

namespace Wachme\Bundle\EasyAccessBundle\Queue\Strategy;

class ObjectQueueStrategy extends QueueStrategy {
    public function getFlatQueue($target) {
        return [$target->getClass(), $target];
    }
}