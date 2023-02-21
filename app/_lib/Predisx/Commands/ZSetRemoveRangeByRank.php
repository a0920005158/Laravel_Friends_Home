<?php

namespace Predisx\Commands;

class ZSetRemoveRangeByRank extends Command {
    public function getId() {
        return 'ZREMRANGEBYRANK';
    }
}
