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

    private \ReflectionClass $refl;

    private bool $testMode;
    private bool $strictMode;
    private bool $throwExceptions;


    public function __construct(TransactionConfig $config, BaseConnection $db) {
        $this->db = $db;
        $this->refl = new \ReflectionClass($this->db);
        $this->configure($config);
    }


    public function shortname(): string {
        return 'transaction';
    }

    protected function applyConfig($_ = null, TransactionConfig $config = null): void {
        if ($config->disableTransactions) {
            $this->db->transOff();
        } else {
            $this->db->transEnabled or throw new ServiceException("You've enabled Transactions for the TransactionService, but the database BaseConnection, already had transactions turned off. Either don't disable them on the database connection, or disable Transactions (which will still try to run the transaction Closures).");
        }

        $this->testMode = $config->testMode;
        $this->strictMode = $config->strictMode;
        $this->throwExceptions = $config->throwExceptions;


    }

    private function hack(?bool $DBDebug, int $transDepth): array {
        $DBDebugProperty = $this->refl->getProperty('DBDebug');
        $DBDebugProperty->setAccessible(true);
        $transDepthProperty = $this->refl->getProperty('transDepth');
        $transDepthProperty->setAccessible(true);

        $saveVars = [$DBDebugProperty->getValue($this->db), $transDepthProperty->getValue($this->db)];

        // hack: enable debugging for the database connection, for more database methods, like query() to throw exceptions
        // method is used on the BaseConnection. We'll catch these in order to know then to roll back,
        // and wrap them in another transaction exception.
        $DBDebug === null or $DBDebugProperty->setValue($this->db, $DBDebug);

        // bad hack: copy the transactionDepth from the database connection,
        // so we can set transDepth to 0. This is necessary because the CodeIgniter BaseConnection,
        // will rollback all transaction layers when  a DatabaseException is thrown and DBDebug is set.
        // This will make the BaseConnection think that the code is not being run in a transaction,
        // so it wont rollback on a DatabaseException, but still re-throw the exception, because
        // DBDebug is true.
        $transDepthProperty->setValue($this->db, $transDepth);

        return $saveVars;
    }

    private function failTransaction(): void
    {
        $transStatus = $this->refl->getProperty('transStatus');
        $transStatus->setAccessible(true);
        $transStatus->setValue($this->db, false);
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
     * @throws TransactionException false if an exception was thrown and a rollback occurred, and throwExceptions is enabled, else the return value of the given Closure function.
     */
    public function transact(\Closure $func, bool $testMode = false): mixed {
        $this->db->transException(true);
        $this->db->transStrict($this->strictMode);

        $DBDebug = null;
        $depth = null;
        $transactionStarted = false;
        try {
            $transactionStarted = $this->db->transEnabled && $this->db->transStart($testMode || $this->testMode);
            if ($transactionStarted) {
                [$DBDebug, $depth] = $this->hack(true, 0);

                // run the given function which can now do database operations in this transaction
                return $func($this->db);
            } else if (!$this->db->transEnabled) {
                log_message('warning', 'Transactions are disabled, running without transaction.', ['function' => __METHOD__]);
                return $func($this->db);
            }

            return false;
            // any exception which the given closure function doesn't catch is
        } catch (\Throwable $throwable) {
            $this->failTransaction();

            if ($this->throwExceptions) {
                log_message('warning', 'Exception during transaction, rolled back. Passing on the exception: ' . $throwable->getMessage(), ['function' => __METHOD__, 'throwable' => $throwable]);
                throw new TransactionException("Exception during transaction, rolled back", $throwable->getCode(), $throwable);
            } else {
                log_message('error', 'Exception during transaction, rolled back: ' . $throwable->getMessage(), ['function' => __METHOD__, 'throwable' => $throwable]);
            }

            return false;
        } finally {
            if ($this->db->transEnabled) {
                if ($transactionStarted) {
                    // because of our bad hack this should be 0, else another transaction was started in the closure
                    // but not rolled back.
                    if ($this->db->transDepth > 0) {
                        log_message('warning',
                            'It seems like you started transaction inside the given closure function. This is not recommended, as it can lead to unexpected behavior.',
                            ['function' => __METHOD__, 'caller' => debug_backtrace()[1]['function'] ?? 'unknown']
                        );
                        
                        while ($this->db->transDepth > 0) {
                            $depth = $this->db->transDepth;
                            $this->db->transComplete();
                            if ($depth === $this->db->transDepth) {
                                throw new ServiceException("Could not rollback bogus inner transaction.");
                            }
                        }
                    }

                    $this->db->transDepth >= 0 or throw new ServiceException("BUG: Transaction depth is negative, this should not happen.");
                    $depth !== null or throw new ServiceException("BUG: Transaction depth was not set to a value, when transaction was started, this should not happen.");


                    // reset our bad hack above after we start transaction & act as if nothing happened ...
                    $this->hack($DBDebug, $depth);

                    // done, and everything seems fine, so lets complete the transaction, if there is still a strans
                    $this->db->transComplete();
                } else {
                    throw new ServiceException("Could not start transaction");
                }
            }
        }
    }
}
