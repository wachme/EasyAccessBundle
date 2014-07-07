<?php

namespace Wachme\Bundle\EasyAccessBundle\Queue\Strategy;

use Wachme\Bundle\EasyAccessBundle\Entity\Target;

class ClassQueueStrategy extends QueueStrategy {
    /**
     * {@inheritdoc}
     */
    public function getFlatQueue(Target $target) {
        return [$target];
    }
}