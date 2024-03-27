<?php
declare(strict_types=1);
namespace MKU\Services\Entity;

use CodeIgniter\Entity\Entity;

abstract class ServiceEntityBase extends Entity implements ServiceEntity {

    public function getPrimaryKeyName(): int|string|array|null {
        return $this->primaryKey;
    }
    public function getPrimaryKeyValue(): int|string|array|null {
        if (is_array($this->primaryKey)) {
            return array_map(function($key) {
                return $this->{$key};
            }, $this->primaryKey);
        } else {
            $key = $this->primaryKey;
            return $this->{$key};
        }
    }

    public function setPrimaryKeyValue(array|int|string|null $id): void {
        if (is_array($this->primaryKey)) {
            foreach ($this->primaryKey as $key) {
                if (isset($this->{$key}) && $this->{$key} === null) {
                    $this->{$key} = $id;
                    break;
                }
            }
        } else {
            $key = $this->primaryKey;
            $this->{$key} = $id;
        }
    }
}
