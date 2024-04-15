<?php

namespace MKU\Services\Database;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;
use Config\Services;
use MKU\Services\TransactionService;

/**
 * Runs the database migrations inside a transaction.
 *
 * @author Moritz Küttel
 */
abstract class TransactionalMigration extends Migration {

    protected TransactionService $transaction;

    public function __construct(Forge $forge = null) {
        parent::__construct($forge);
        $this->transaction = Services::transaction(null, $this->db);
    }

    public function up(): void {
        $this->transaction->transact(function () {
            $this->transactionalUp();
        });
    }


    public function down(): void {
        $this->transaction->transact(function () {
            $this->transactionalDown();
        });
    }

    abstract function transactionalUp(): void;
    abstract function transactionalDown(): void;

}