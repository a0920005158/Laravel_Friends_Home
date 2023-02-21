<?php

namespace Predisx\Commands;

class PubSubPublish extends Command {
    public function getId() {
        return 'PUBLISH';
    }

    protected function canBeHashed() {
        return false;
    }
}
