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

    private function ensurePrimaryKey(): void {
        if (!$this->hasPrimaryKey()) {
            throw new DataException("Trying to access primary key of Entity [" . __CLASS__ . "], but it does not have a primary key defined.");
        }
    }

    private function filterAttributes(array $attributes) {
        return array_filter($attributes, fn($key) => is_string($key) && property_exists($this, $key));
    }

    private function forceSetAttributes(array $attributes) {
        $attributes = $this->filterAttributes($attributes);
        foreach ($attributes as $key) {
            $this->{$key} = $attributes[$key];
        }
    }

    public function getPrimaryKeyValue(): int|string|array|null {
        $this->ensurePrimaryKey();
        $primaryKey = $this->getPrimaryKeyName();

        if (is_array($this->primaryKey)) {
            return array_map(function ($key) {
                return $this->{$key};
            }, $this->filterAttributes($this->primaryKey));
        } else {
            return $this->{$primaryKey};
        }
    }

    public function setPrimaryKeyValue(array|int|string|null $id): void {
        $this->ensurePrimaryKey();
        $primaryKey = $this->getPrimaryKeyName();

        if (is_array($primaryKey)) {
            $pkAttrs = $this->filterAttributes($primaryKey);
            foreach ($pkAttrs as $attr) {
                $this->{$attr} = $id[$attr];
            }
        } else if (is_string($primaryKey)) {
            $this->{$primaryKey} = $id;
        }
    }

    public function hasPrimaryKey(): bool {
        $primaryKey = $this->getPrimaryKeyName();
        return !is_null($primaryKey)
            && (is_string($primaryKey) || (
                    is_array($primaryKey) && !empty($primaryKey)
                    && array_reduce($primaryKey, fn($v, $acc) => $acc && is_string($v), true)));
    }

    public function hasPrimaryKeyValue(): bool {
        if (!$this->hasPrimaryKey()) return false;

        $primaryKey = $this->getPrimaryKeyValue();
        return !is_null($primaryKey)
            && (is_string($primaryKey) || is_int($primaryKey) (
                is_array($primaryKey) && !empty($primaryKey)
                && array_reduce($primaryKey, fn($v, $acc) => $acc && !is_null($v) && $v !== false, true)));
    }

    public function setAttributes(array $attrs): bool {
        $primaryKeyAttrs = $this->getPrimaryKeyName();
        if ($this->hasPrimaryKey()) {
            $attrs = array_filter($attrs,
                fn($key) => is_string($key) && property_exists($this, $key)
                    && $key !== $primaryKeyAttrs && !in_array($key, $primaryKeyAttrs),
                ARRAY_FILTER_USE_KEY
            );
        }

        $updated = false;
        foreach ($attrs as $key => $value) {
            $this->{$key} = $value;
            $updated = true;
        }

        return $updated;
    }

}