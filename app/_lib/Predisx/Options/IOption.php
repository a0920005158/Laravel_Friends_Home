<?php

namespace Predisx\Options;

interface IOption {
    public function validate($value);
    public function getDefault();
    public function __invoke($value);
}
