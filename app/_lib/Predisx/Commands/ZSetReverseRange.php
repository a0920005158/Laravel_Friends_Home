<?php

namespace Predisx\Commands;

class ZSetReverseRange extends ZSetRange {
    public function getId() {
        return 'ZREVRANGE';
    }
}
