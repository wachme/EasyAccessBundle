<?php

namespace Wachme\Bundle\EasyAccessBundle\Queue\Strategy;

use Wachme\Bundle\EasyAccessBundle\Entity\Target;

class ObjectFieldQueueStrategy extends QueueStrategy {
    /**
     * {@inheritdoc}
     */
    public function getFlatQueue(Target $target) {
        return [
            $target->getObject()->getClass(),
            $target->getClassField(),
            $target->getObject(),
            $target
        ];
    }
}