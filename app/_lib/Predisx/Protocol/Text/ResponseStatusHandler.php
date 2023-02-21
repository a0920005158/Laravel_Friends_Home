<?php

namespace Predisx\Protocol\Text;

use Predisx\ResponseQueued;
use Predisx\Protocol\IResponseHandler;
use Predisx\Network\IConnectionComposable;

class ResponseStatusHandler implements IResponseHandler {
    public function handle(IConnectionComposable $connection, $status) {
        switch ($status) {
            case 'OK':
                return true;
            case 'QUEUED':
                return new ResponseQueued();
            default:
                return $status;
        }
    }
}
