<?php

declare(strict_types=1);


namespace MKU\Services\Config;

use MKU\Services\TransactionService;
use CodeIgniter\Config\BaseService;
use MKU\Services\Config\Transaction as TransactionConfig;

class Services extends BaseService
{
    /**
     * The TransactionService class
     */
    public static function transaction(bool $getShared = true): TransactionService
    {
        if ($getShared) {
            return self::getSharedInstance('transaction');
        }

        /** @var TransactionConfig $config */
        $config = config('transaction');

        return new TransactionService($config, db_connect());
    }
}
