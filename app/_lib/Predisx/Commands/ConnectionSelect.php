<?php

namespace Predisx\Commands;

class ConnectionSelect extends Command {
    public function getId() {
        return 'SELECT';
    }

    protected function onPrefixKeys(Array $arguments, $prefix) {
        /* NOOP */
    }

    protected function canBeHashed() {
        return false;
    }
}
