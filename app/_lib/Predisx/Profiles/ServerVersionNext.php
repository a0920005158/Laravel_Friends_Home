<?php

namespace Predisx\Profiles;

class ServerVersionNext extends ServerVersion24 {
    public function getVersion() { return '2.6'; }
    public function getSupportedCommands() {
        return array_merge(parent::getSupportedCommands(), array(
            'eval'                      => '\Predisx\Commands\ServerEval',
            'evalsha'                   => '\Predisx\Commands\ServerEvalSHA',
        ));
    }
}
