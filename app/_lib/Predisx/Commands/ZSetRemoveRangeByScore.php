<?php

namespace Predisx\Commands;

class ZSetRemoveRangeByScore extends Command {
    public function getId() {
        return 'ZREMRANGEBYSCORE';
    }
}
