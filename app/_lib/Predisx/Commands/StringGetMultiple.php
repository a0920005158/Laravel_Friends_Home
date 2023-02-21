<?php

namespace Predisx\Commands;

use Predisx\Helpers;

class StringGetMultiple extends Command {
    public function getId() {
        return 'MGET';
    }

    protected function filterArguments(Array $arguments) {
        return Helpers::filterArrayArguments($arguments);
    }

    protected function onPrefixKeys(Array $arguments, $prefix) {
        return PrefixHelpers::multipleKeys($arguments, $prefix);
    }

    protected function canBeHashed() {
        return $this->checkSameHashForKeys($this->getArguments());
    }
}
