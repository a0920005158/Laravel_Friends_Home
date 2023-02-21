<?php

namespace Predisx\Commands;

use Predisx\Helpers;

class ServerObject extends Command {
    public function getId() {
        return 'OBJECT';
    }

    protected function onPrefixKeys(Array $arguments, $prefix) {
        /* NOOP */
    }

    protected function canBeHashed() {
        return false;
    }
}
