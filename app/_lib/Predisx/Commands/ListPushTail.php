<?php

namespace Predisx\Commands;

use Predisx\Helpers;

class ListPushTail extends Command {
    public function getId() {
        return 'RPUSH';
    }

    protected function filterArguments(Array $arguments) {
        return Helpers::filterVariadicValues($arguments);
    }
}
