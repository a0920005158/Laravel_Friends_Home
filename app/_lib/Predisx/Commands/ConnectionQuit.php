<?php

namespace Predisx\Commands;

class ConnectionQuit extends Command {
    public function getId() {
        return 'QUIT';
    }

    protected function onPrefixKeys(Array $arguments, $prefix) {
        /* NOOP */
    }

    protected function canBeHashed() {
        return false;
    }
}
