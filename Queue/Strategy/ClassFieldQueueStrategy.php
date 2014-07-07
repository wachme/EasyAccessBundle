<?php

namespace Wachme\Bundle\EasyAccessBundle\Queue\Strategy;

use Wachme\Bundle\EasyAccessBundle\Entity\Target;

class ClassFieldQueueStrategy extends QueueStrategy {
    /**
     * {@inheritdoc}
     */
    public function getFlatQueue(Target $target) {
        return [$target->getClass(), $target];
    }
}