<?php

namespace Predisx\Commands;

use Predisx\Helpers;

class SetAdd extends Command {
    public function getId() {
        return 'SADD';
    }

    protected function filterArguments(Array $arguments) {
        return Helpers::filterVariadicValues($arguments);
    }

    public function parseResponse($data) {
        return (bool) $data;
    }
}
