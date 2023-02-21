<?php

namespace Predisx\Commands;

class KeyKeysV12x extends KeyKeys {
    public function parseResponse($data) {
        return explode(' ', $data);
    }
}
