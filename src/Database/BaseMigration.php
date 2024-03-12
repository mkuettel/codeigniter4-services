<?php

namespace MKU\Services\Database;

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