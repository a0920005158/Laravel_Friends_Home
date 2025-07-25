<?php

namespace Predisx\Commands;

use Predisx\Helpers;

class ZSetAdd extends Command {
    public function getId() {
        return 'ZADD';
    }

    protected function filterArguments(Array $arguments) {
        return Helpers::filterVariadicValues($arguments);
    }

    public function parseResponse($data) {
        return (bool) $data;
    }
}
