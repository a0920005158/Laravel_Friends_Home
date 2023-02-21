<?php

namespace Predisx\Commands;

use Predisx\Helpers;

class HashGetMultiple extends Command {
    public function getId() {
        return 'HMGET';
    }

    protected function filterArguments(Array $arguments) {
        return Helpers::filterVariadicValues($arguments);
    }
}
