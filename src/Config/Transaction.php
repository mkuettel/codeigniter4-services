<?php
declare(strict_types=1);
namespace MKU\Services\Config;

class Transaction {

    // Whether the TransactionService should run in test mode. In test mode all transactions will be rolled back, even
    // if they are committed. This is useful for testing, to ensure that the database is always in a clean state.
    // https://codeigniter.com/user_guide/database/transactions.html#strict-mode
    public bool $testMode = false;

    // Whether we should tell the CodeIgniter BaseConnection $db to use strict mode for transactions.
    // https://codeigniter.com/user_guide/database/transactions.html#strict-mode
    public bool $strictMode = true;

    // Whether the TransactionService should throw TransactionException when an exception occurs during a
    // transaction. If set to false, all exceptions during transaction will just be caught and logged as an error.
    // Note the underlying database connection will still always be configured throw to throw DatabaseExceptions if some query fails fails,
    // but, they will be caught by the TransactionService.
    public bool $throwExceptions = true;

}
