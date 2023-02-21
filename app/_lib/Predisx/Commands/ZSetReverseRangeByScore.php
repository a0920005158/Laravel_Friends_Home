<?php

namespace Predisx\Commands;

class ZSetReverseRangeByScore extends ZSetRangeByScore {
    public function getId() {
        return 'ZREVRANGEBYSCORE';
    }
}
