<?php

namespace MKU\Services\Database;

use CodeIgniter\Database\Migration;
use Config\Services;

abstract class TransactionalMigration extends Migration {
    public function up(): void {
        Services::transaction(null, $this->db)->transact(function () {
            $this->transactionalUp();
        }, true);
    }


    public function down(): void
    {
        Services::transaction(null, $this->db)->transact(function () {
            $this->transactionalDown();
        }, true);
    }

    abstract function transactionalUp(): void;
    abstract function transactionalDown(): void;

}