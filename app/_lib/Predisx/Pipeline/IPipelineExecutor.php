<?php

namespace Predisx\Pipeline;

use Predisx\Network\IConnection;

interface IPipelineExecutor {
    public function execute(IConnection $connection, &$commands);
}
