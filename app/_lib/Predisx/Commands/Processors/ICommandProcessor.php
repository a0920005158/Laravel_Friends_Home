<?php

namespace Predisx\Commands\Processors;

use Predisx\Commands\ICommand;

interface ICommandProcessor {
    public function process(ICommand $command);
}
