<?php

namespace MKU\Services;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use MKU\Services\Config\Transaction as TransactionConfig;
use MKU\Services\Library\Config\Configurable;
use MKU\Services\Library\Config\ConfigurableTrait;

/**
 * The TransactionService provides a functional API to run database transactions.
 * It wraps the CodeIgniter 4 database transaction functionality and adds some
 * nicer syntax to use closures for the transactional code.
 *
 * @author Moritz Küttel
 * @experimental This class is experimental and may change in the future.
 */
class TransactionService implements Service, Configurable {
    use ConfigurableTrait;
    private BaseConnection $db;

    private bool $testMode;
    private bool $strictMode;
    private bool $throwExceptions;


    public function __construct(TransactionConfig $config, BaseConnection $db) {
        $this->db = $db;
        $this->configure($config);
    }


    public function shortname(): string {
        return 'transaction';
    }

    protected function applyConfig($_ = null, TransactionConfig $config = null): void {
        if ($config->disableTransactions) {
            $this->db->transOff();
        } else {
            $this->db->transEnabled = false;
        }

        $this->testMode = $config->testMode;
        $this->strictMode = $config->strictMode;
        $this->throwExceptions = $config->throwExceptions;
    }

    /**
     * Wraps the given function into a database transaction.
     *
     * The given function is run and all modifications to the database
     * are only committed if the function returns without throwing an exception.
     * If the function throws an exception, the transaction will be rolled back.
     *
     * The transaction is run in strict mode (if enabled in the configuration or via setter method).
     *
     * If transaction given function throws an exception, any exception at all, the
     * the transaction will be rolled back.
     *
     * If exceptions are enabled, the exception will be wrapped into a TransactionException and thrown.
     *
     * All Exceptions during transactions are logged.
     *
     * @param \Closure $func closure which will be run inside the transaction, and can do database operations. A BaseConnection is passed as the first parameter.
     * @param bool $testMode whether to start a test transaction which will be rolled back in any case.
     * @return mixed on success when what the given function returned, or false if an exception was thrown and a rollback occurred.
     * @throws TransactionException
     */
    public function transact(\Closure $func, bool $testMode = false): mixed {
        $this->db->transException(true);
        $this->db->transStrict($this->strictMode);

        try {
            $this->db->transStart($testMode || $this->testMode);

            // run the given function which can now do database operations in this transaction
            $result = $func($this->db);

            // done, and everything seems fine, so lets commit this to the database
            $this->db->transComplete();

            return $result;
            // any exception which the given closure function doesn't catch is
        } catch (\Throwable $throwable) {
            // the rollback already occurred when a query failed if DBDebug is on and a DatabaseException was thrown,
            // so there's no rollback required in that case.
            // See section "Throwing Exceptions": https://codeigniter.com/user_guide/database/transactions.html#id5
            if ((!$this->db->DBDebug) || !($throwable instanceof DatabaseException)) {
                // roll back the transaction on any exception, even when it's not database related.
                // this should be useful prevent inconsistencies with other sub-systems as well.
                $this->db->transRollback();
            }



            if ($this->throwExceptions) {
                log_message('warning', 'Exception during transaction, rolled back. Passing on the exception: ' . $throwable->getMessage(), ['function' => __METHOD__, 'throwable' => $throwable]);
                throw new TransactionException("Exception during transaction, rolled back", $throwable->getCode(), $throwable);
            } else {
                log_message('error', 'Exception during transaction, rolled back: ' . $throwable->getMessage(), ['function' => __METHOD__, 'throwable' => $throwable]);
            }

            return false;
        }
    }
}
