<?php

namespace Predisx\Commands;

use Predisx\Helpers;

class PubSubSubscribeByPattern extends PubSubSubscribe {
    public function getId() {
        return 'PSUBSCRIBE';
    }
}
