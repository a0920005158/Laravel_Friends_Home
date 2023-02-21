<?php

namespace Predisx\Commands;

class SetUnionStore extends SetIntersectionStore {
    public function getId() {
        return 'SUNIONSTORE';
    }
}
