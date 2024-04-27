<?php

declare(strict_types=1);
namespace MKU\Services\Database\Transactional\SQLite3;

use CodeIgniter\Database\SQLite3\Connection as BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;
use SQLite3;
use SQLite3Result;
use stdClass;

/**
 * Connection for SQLite3 with support for nested transactions using savepoints.
 *
 * @extends BaseConnection<SQLite3, SQLite3Result>
 */
class Connection extends BaseConnection
{
    private int $savepointLevel = 0;


    private function _savepoint($savepoint, string $action = ''): string {
        return ($action === '' ? '' : $action . ' ') . 'SAVEPOINT `savepoint_mkuci4s_' . $savepoint . '`';
    }


    protected function _transBegin(): bool
    {
        $created = false;
        $savepoint = $this->savepointLevel;
        try {

            return $created = ($this->savepointLevel === 0
                ? parent::_transBegin()
                : $this->connID->exec($this->_savepoint($savepoint + 1)));
        } finally {
            if ($created) {
                $this->savepointLevel++;
            }
        }
    }

    /**
     * Commit Transaction
     */
    protected function _transCommit(): bool
    {
        if ($this->savepointLevel === 0) {
            return false;
        }

        $committed = false;
        $savepoint = $this->savepointLevel;
        try {
            return $committed = ($this->savepointLevel <= 1
                ? parent::_transCommit()
                : $this->connID->exec($this->_savepoint($savepoint, 'RELEASE')));
        } finally {
            if ($committed) {
                $this->savepointLevel = max($this->savepointLevel - 1, 0);
            }

        }
    }

    /**
     * Rollback Transaction
     */
    protected function _transRollback(): bool
    {
        if ($this->savepointLevel === 0) {
            return false;
        }
        $rolledBack = false;
        $savepoint = $this->savepointLevel;
        try {
            return $rolledBack = ($this->savepointLevel <= 1
                ? parent::_transRollback()
                : $this->connID->exec($this->_savepoint($savepoint, 'ROLLBACK TO')));
        } finally {
            if ($rolledBack) {
                $this->savepointLevel = max($this->savepointLevel - 1, 0);
            }
        }
    }
}

