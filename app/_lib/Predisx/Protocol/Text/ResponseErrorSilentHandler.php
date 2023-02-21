<?php

namespace Predisx\Protocol\Text;

use Predisx\ResponseError;
use Predisx\Protocol\IResponseHandler;
use Predisx\Network\IConnectionComposable;

class ResponseErrorSilentHandler implements IResponseHandler {
    public function handle(IConnectionComposable $connection, $errorMessage) {
        return new ResponseError($errorMessage);
    }
}
