<?php

namespace MKU\Services\Database;

/**
 * @experimental This class is experimental and may change in the future.
 */
abstract class BaseMigration extends TransactionalMigration {
    public function tableOptions(): array {
        if (str_contains($this->db->DBDriver, 'MySQL')) {
            return [
                'engine' => 'InnoDB',
            ];
        }
        return [];
    }
}