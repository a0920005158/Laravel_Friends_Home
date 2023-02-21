<?php

namespace Predisx\Protocol;

use Predisx\Network\IConnectionComposable;

interface IResponseReader {
    public function read(IConnectionComposable $connection);
}
