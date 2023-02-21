<?php

namespace Predisx\Protocol;

use Predisx\Network\IConnectionComposable;

interface IResponseHandler {
    function handle(IConnectionComposable $connection, $payload);
}
