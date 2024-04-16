<?php
declare(strict_types=1);
namespace MKU\Services\Entity;

use CodeIgniter\Database\Exceptions\DataException;
use MKU\Services\ServiceException;

/**
 * This trait provides default implementations to implement the ServiceEntity interface.
 *
 * @author Moritz Küttel
 * @experimental
 */
trait ServiceEntityTrait {

    private function ensurePrimaryKey(): void {
        if (!$this->hasPrimaryKey()) {
            throw new DataException("Trying to access primary key of Entity [" . __CLASS__ . "], but it does not have a primary key defined.");
        }
    }

    private function filterAttributes(array $attributes, $func = 'properties_exist'): array {
        $funcs = [
            'properties_exist' => fn($value) => is_string($value) && property_exists($this, $value),
            'key_properties_exist' => fn($value, $key) => is_string($key) && property_exists($this, $key),
            'only_pk_attrs' =>
                fn($value, $key) => ($primaryKeyAttrs = $this->getPrimaryKeyName())
                    && $funcs['key_properties_exist']($value, $key)
                    && in_array($key, $primaryKeyAttrs),
            'without_pk_attrs' =>
                fn($value, $key) => ($primaryKeyAttrs = $this->getPrimaryKeyName())
                    && $funcs['key_properties_exist']($value, $key)
                    && $key !== $primaryKeyAttrs && !in_array($key, $primaryKeyAttrs),
        ];

        if(!isset($funcs[$func])) throw new \InvalidArgumentException(
            "Invalid filter function [$func], available are" . (implode(", ", array_keys($funcs)))
        );

        return array_filter($attributes, $funcs[$func], ARRAY_FILTER_USE_BOTH);
    }

    private function forceSetAttributes(array $attributes): bool {
        $attributes = $this->filterAttributes($attributes, 'key_properties_exist');
        $updated = false;
        foreach ($attributes as $key => $value) {
            $this->{$key} = $attributes[$key];
            $updated = true;
        }
        return $updated;
    }

    public function getPrimaryKeyValue(): int|string|array|null {
        $this->ensurePrimaryKey();
        $primaryKey = $this->getPrimaryKeyName();

        if (is_array($primaryKey)) {
            return array_map(function ($key) {
                return $this->{$key};
            },$this->filterAttributes($primaryKey));
        } else {
            return $this->{$primaryKey};
        }
    }

    public function setPrimaryKeyValue(array|int|string|null $pkValue): void {
        $this->ensurePrimaryKey();
        $primaryKey = $this->getPrimaryKeyName();

        if (is_array($primaryKey)) {
            $pkValue = $this->filterAttributes($pkValue, 'key_properties_exist');
            $pkValue = $this->filterAttributes($pkValue, 'only_pk_attrs');

            if (count($pkValue) != count($primaryKey)) {
                throw new ServiceException("Invalid number of primary key values " . count($pkValue) . ", required " . count($primaryKey) . " for Entity [" . __CLASS__ . "a]");
            }

            if (!$this->forceSetAttributes($pkValue)) {
                throw new ServiceException("Failed to set primary key value(s) [" . implode(", ", $pkValue) . "] for Entity [" . __CLASS__ . "]");
            }
        } else if (is_string($primaryKey)) {
            $this->{$primaryKey} = $pkValue;
        }
    }

    private static function isValidCompositeKeyName($key): bool {
        return is_array($key) && !empty($key)
            && array_reduce($key, fn($v, $acc) => $acc && is_string($v), true);
    }

    public function hasPrimaryKey(): bool {
        $primaryKey = $this->getPrimaryKeyName();
        return !is_null($primaryKey)
            && (is_string($primaryKey) || self::isValidCompositeKeyName($primaryKey));
    }


    private static function isValidCompositeKeyValue($key): bool {
        return is_array($key) && !empty($key)
            && array_reduce($key, fn($v, $acc) => $acc && !is_null($v) && $v !== false, true);
    }


    public function hasPrimaryKeyValue(): bool {
        if (!$this->hasPrimaryKey()) return false;

        $primaryKey = $this->getPrimaryKeyValue();
        return !is_null($primaryKey)
            && (is_string($primaryKey)
            || is_int($primaryKey)
            || self::isValidCompositeKeyValue($primaryKey));
    }

    public function setAttributes(array $attrs): bool {
        if ($this->hasPrimaryKey()) {
            $attrs = $this->filterAttributes($attrs, 'without_pk_attrs');
        }

        return $this->forceSetAttributes($attrs);
    }

}