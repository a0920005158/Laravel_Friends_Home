<?php

namespace Predisx\Commands;

class ListPushHead extends ListPushTail {
    public function getId() {
        return 'LPUSH';
    }
}
