<?php

namespace Predisx\Options;

use Predisx\Commands\Processors\KeyPrefixProcessor;

class ClientPrefix extends Option {
    public function validate($value) {
        return new KeyPrefixProcessor($value);
    }
}
