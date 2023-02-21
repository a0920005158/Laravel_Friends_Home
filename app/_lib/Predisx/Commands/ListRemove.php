<?php

namespace Predisx\Commands;

class ListRemove extends Command {
    public function getId() {
        return 'LREM';
    }
}
