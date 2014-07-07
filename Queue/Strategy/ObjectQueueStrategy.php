<?php

namespace Wachme\Bundle\EasyAccessBundle\Queue\Strategy;

use Wachme\Bundle\EasyAccessBundle\Entity\Target;

class ObjectQueueStrategy extends QueueStrategy {
    /**
     * {@inheritdoc}
     */
    public function getFlatQueue(Target $target) {
        return [$target->getClass(), $target];
    }
}