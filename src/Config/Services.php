<?php

declare(strict_types=1);


namespace MKU\Services\Config;

use CodeIgniter\Database\BaseConnection;
use MKU\Services\TransactionService;
use CodeIgniter\Config\BaseService;
use MKU\Services\Config\Transaction as TransactionConfig;

class Services extends BaseService {
    /**
     * The TransactionService class
     */
    public static function transaction(TransactionConfig $config = null, BaseConnection $db = null, bool $getShared = true): TransactionService {
        if ($getShared) {
            return self::getSharedInstance('transaction', $config, $db);
        }

        /** @var TransactionConfig $config */
        $config = $config ?? config('Transaction');

        return new TransactionService($config, $db ?? db_connect());
    }
}
