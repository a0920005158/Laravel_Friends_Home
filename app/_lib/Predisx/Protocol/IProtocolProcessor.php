<?php

namespace Predisx\Protocol;

use Predisx\Commands\ICommand;
use Predisx\Network\IConnectionComposable;

interface IProtocolProcessor extends IResponseReader {
    public function write(IConnectionComposable $connection, ICommand $command);
    public function setOption($option, $value);
}
