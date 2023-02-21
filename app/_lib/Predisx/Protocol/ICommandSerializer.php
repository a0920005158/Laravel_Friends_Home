<?php

namespace Predisx\Protocol;

use Predisx\Commands\ICommand;

interface ICommandSerializer {
    public function serialize(ICommand $command);
}
