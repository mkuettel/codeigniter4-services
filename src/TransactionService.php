<?php

namespace MKU\Services;

use CodeIgniter\Database\BaseConnection;
use MKU\Services\Config\Transaction as TransactionConfig;

class TransactionService implements ServiceInterface {
    private TransactionConfig $config;
    private BaseConnection $db;

    public function __construct(TransactionConfig $config, BaseConnection $db) {
        $this->config = $config;
        $this->db = $db;
    }

    /**
     * @param \Closure $func
     * @param bool $testMode
     * @return mixed
     */
    public function transact(\Closure $func, bool $testMode = false): mixed {
        $this->db->transException(true);
//        $this->db->transStrict(true);
        try {
            $this->db->transStart($testMode);
            $result = $func();
            $this->db->transCommit();
        } catch (\Throwable $throwable) {
            $this->db->transRollback();
            throw new TransactionException("Exception during transaction, rolled back", 1, $throwable);
        } finally {
            $this->db->transComplete();
        }

        return $result;
    }

}
