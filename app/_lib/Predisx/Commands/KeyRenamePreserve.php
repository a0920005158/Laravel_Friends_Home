<?php

namespace Predisx\Commands;

class KeyRenamePreserve extends KeyRename {
    public function getId() {
        return 'RENAMENX';
    }

    public function parseResponse($data) {
        return (bool) $data;
    }
}
