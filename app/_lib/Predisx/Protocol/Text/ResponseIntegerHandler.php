<?php

namespace Predisx\Protocol\Text;

use Predisx\Helpers;
use Predisx\Protocol\IResponseHandler;
use Predisx\Protocol\ProtocolException;
use Predisx\Network\IConnectionComposable;

class ResponseIntegerHandler implements IResponseHandler {
    public function handle(IConnectionComposable $connection, $number) {
        if (is_numeric($number)) {
            return (int) $number;
        }
        if ($number !== 'nil') {
            Helpers::onCommunicationException(new ProtocolException(
                $connection, "Cannot parse '$number' as numeric response"
            ));
        }
        return null;
    }
}
