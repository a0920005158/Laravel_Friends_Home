<?php

namespace Predisx;

interface IConnectionParameters {
    public function __isset($parameter);
    public function __get($parameter);
    public function toArray();
}
