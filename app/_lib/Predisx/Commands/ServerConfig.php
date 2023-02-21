<?php

namespace Predisx\Commands;

class ServerConfig extends Command {
    public function getId() {
        return 'CONFIG';
    }

    protected function onPrefixKeys(Array $arguments, $prefix) {
        /* NOOP */
    }

    protected function canBeHashed() {
        return false;
    }
}
