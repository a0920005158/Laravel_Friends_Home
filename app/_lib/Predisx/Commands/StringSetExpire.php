<?php

namespace Predisx\Commands;

class StringSetExpire extends Command {
    public function getId() {
        return 'SETEX';
    }
}
