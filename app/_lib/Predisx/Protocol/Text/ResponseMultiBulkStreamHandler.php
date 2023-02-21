<?php

namespace Predisx\Protocol\Text;

use Predisx\Helpers;
use Predisx\Protocol\IResponseHandler;
use Predisx\Protocol\ProtocolException;
use Predisx\Network\IConnectionComposable;
use Predisx\Iterators\MultiBulkResponseSimple;

class ResponseMultiBulkStreamHandler implements IResponseHandler {
    public function handle(IConnectionComposable $connection, $lengthString) {
        $length = (int) $lengthString;
        if ($length != $lengthString) {
            Helpers::onCommunicationException(new ProtocolException(
                $connection, "Cannot parse '$length' as data length"
            ));
        }
        return new MultiBulkResponseSimple($connection, $length);
    }
}
