<?php

namespace Predisx\Protocol\Text;

use Predisx\Helpers;
use Predisx\Protocol\IResponseHandler;
use Predisx\Protocol\ProtocolException;
use Predisx\Network\IConnectionComposable;

class ResponseBulkHandler implements IResponseHandler {
    public function handle(IConnectionComposable $connection, $lengthString) {
        $length = (int) $lengthString;
        if ($length != $lengthString) {
            Helpers::onCommunicationException(new ProtocolException(
                $connection, "Cannot parse '$length' as data length"
            ));
        }
        if ($length >= 0) {
            return substr($connection->readBytes($length + 2), 0, -2);
        }
        if ($length == -1) {
            return null;
        }
    }
}
