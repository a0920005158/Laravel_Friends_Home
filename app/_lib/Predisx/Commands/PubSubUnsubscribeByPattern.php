<?php

namespace Predisx\Commands;

class PubSubUnsubscribeByPattern extends PubSubUnsubscribe {
    public function getId() {
        return 'PUNSUBSCRIBE';
    }
}
