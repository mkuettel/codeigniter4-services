<?php

namespace MKU\Services;

final class TransactionException extends ServiceException {
    final public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
