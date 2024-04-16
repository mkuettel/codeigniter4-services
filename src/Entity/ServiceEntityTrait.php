<?php
declare(strict_types=1);
namespace MKU\Services\Entity;

use CodeIgniter\Database\Exceptions\DataException;

/**
 * This trait provides default implementations to implement the ServiceEntity interface.
 *
 * @author Moritz Küttel
 * @experimental
 */
trait ServiceEntityTrait {
    public function getPrimaryKeyName(): string|array|null {
        return $this->primaryKey;
    }

    public function getPrimaryKeyValue(): int|string|array|null {
        if (is_array($this->primaryKey)) {
            return array_map(function ($key) {
                return $this->{$key};
            }, $this->primaryKey);
        } else {
            $key = $this->primaryKey;
            return $this->{$key};
        }
    }

    public function setPrimaryKeyValue(array|int|string|null $id): void {
        if (!$this->hasPrimaryKey()) {
            throw new DataException("Trying to set primary key of Entity [" . __CLASS__ . "], but it does have a primary key defined.");
        }

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

    public function hasPrimaryKey(): bool {
        $primaryKey = $this->getPrimaryKeyName();
        return !is_null($primaryKey)
            && (is_string($primaryKey) || (
                    is_array($primaryKey) && array_reduce($primaryKey, fn($v, $acc) => $acc && is_string($v), true)
                )
            );
    }

    public function hasPrimaryKeyValue(): bool {
        if (!$this->hasPrimaryKey()) return false;

        $primaryKey = $this->getPrimaryKeyValue();
        return !is_null($primaryKey)
            && (is_string($primaryKey) || is_array($primaryKey)
                && array_reduce($primaryKey, fn($v, $acc) => $acc && !is_null($v) && $v !== false, true)
            );
    }

    public function setAttributes(array $attrs): void {
        $primaryKeyAttrs = $this->getPrimaryKeyName();
        $attrs = array_filter($attrs,
            fn($key) => property_exists($this, $key)
                && $key !== $primaryKeyAttrs && !in_array($key, $primaryKeyAttrs),
            ARRAY_FILTER_USE_KEY
        );

        foreach ($attrs as $key => $value) {
            $this->{$key} = $value;
        }
    }

}