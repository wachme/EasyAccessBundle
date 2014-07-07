<?php

namespace Wachme\Bundle\EasyAccessBundle\Queue\Strategy;

class ClassQueueStrategy extends QueueStrategy {
    public function getFlatQueue($target) {
        return [$target];
    }
}