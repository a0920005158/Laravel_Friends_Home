<?php

namespace Predisx\Commands;

class ServerShutdown extends Command {
    public function getId() {
        return 'SHUTDOWN';
    }

    protected function onPrefixKeys(Array $arguments, $prefix) {
        /* NOOP */
    }

    protected function canBeHashed() {
        return false;
    }
}
