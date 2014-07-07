<?php

namespace Wachme\Bundle\EasyAccessBundle\Queue\Strategy;

class ObjectFieldQueueStrategy extends QueueStrategy {
    public function getFlatQueue($target) {
        return [$target->getObject()->getClass(), $target->getClassField(), $target->getObject(), $target];
    }
}