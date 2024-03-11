<?php
declare(strict_types=1);
namespace MKU\Services\Entity;

use CodeIgniter\Entity\Entity;
use MKU\Services\Model\Storable;

abstract class ServiceEntityBase extends Entity implements ServiceEntity {
    public function getPrimaryKey(): int|string|array|null {
        if (is_array($this->primaryKey)) {
            return array_map(function($key) {
                return $this->$key;
            }, $this->primaryKey);
        } else {
            $key = $this->primaryKey;
            return $this->$key;
        }
    }
}
