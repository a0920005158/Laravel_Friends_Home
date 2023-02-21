<?php

namespace Predisx\Commands;

class ZSetCardinality extends Command {
    public function getId() {
        return 'ZCARD';
    }
}
