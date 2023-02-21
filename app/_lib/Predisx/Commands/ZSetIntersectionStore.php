<?php

namespace Predisx\Commands;

class ZSetIntersectionStore extends ZSetUnionStore {
    public function getId() {
        return 'ZINTERSTORE';
    }
}
