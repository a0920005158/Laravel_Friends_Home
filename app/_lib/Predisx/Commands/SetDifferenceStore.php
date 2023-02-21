<?php

namespace Predisx\Commands;

class SetDifferenceStore extends SetIntersectionStore {
    public function getId() {
        return 'SDIFFSTORE';
    }
}
