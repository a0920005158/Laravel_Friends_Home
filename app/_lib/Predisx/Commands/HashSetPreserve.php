<?php

namespace Predisx\Commands;

class HashSetPreserve extends Command {
    public function getId() {
        return 'HSETNX';
    }

    public function parseResponse($data) {
        return (bool) $data;
    }
}
