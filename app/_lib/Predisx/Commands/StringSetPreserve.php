<?php

namespace Predisx\Commands;

class StringSetPreserve extends Command {
    public function getId() {
        return 'SETNX';
    }

    public function parseResponse($data) {
        return (bool) $data;
    }
}
