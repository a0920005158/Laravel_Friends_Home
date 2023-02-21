<?php

namespace Predisx\Commands;

class ListPopLastBlocking extends ListPopFirstBlocking {
    public function getId() {
        return 'BRPOP';
    }
}
