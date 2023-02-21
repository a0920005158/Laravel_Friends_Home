<?php

namespace Predisx\Commands;

class ListPopLast extends Command {
    public function getId() {
        return 'RPOP';
    }
}
