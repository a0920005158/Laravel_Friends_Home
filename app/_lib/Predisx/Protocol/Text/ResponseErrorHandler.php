<?php

namespace Predisx\Protocol\Text;

use Predisx\ServerException;
use Predisx\Protocol\IResponseHandler;
use Predisx\Network\IConnectionComposable;

class ResponseErrorHandler implements IResponseHandler {
    public function handle(IConnectionComposable $connection, $errorMessage) {
        throw new ServerException($errorMessage);
    }
}
