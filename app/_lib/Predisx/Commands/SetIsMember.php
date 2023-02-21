<?php

namespace Predisx\Commands;

class SetIsMember extends Command {
    public function getId() {
        return 'SISMEMBER';
    }

    public function parseResponse($data) {
        return (bool) $data;
    }
}
