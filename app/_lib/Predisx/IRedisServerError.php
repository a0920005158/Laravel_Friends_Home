<?php

namespace Predisx;

interface IRedisServerError extends IReplyObject {
    public function getMessage();
    public function getErrorType();
}
