<?php
declare(strict_types=1);
namespace MKU\Services\Config;

class Transaction {

    public bool $testMode = false;

    // Whether we should tell the CodeIgniter BaseConnection $db to use strict transaction
    // this will result in better transaction isolation for some databases, if configured correctly,
    // so that other connections cannot see data
    // being modified during an ongoing transaction.
    public bool $strictMode = false;

    // Whether the TransactionService should throw TransactionException when an exception occurs during a
    // transaction. If set to false, all exceptions during transaction will just be caught and logged as an error.
    public bool $throwExceptions = true;

}
