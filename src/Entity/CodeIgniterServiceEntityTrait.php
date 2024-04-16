<?php

namespace MKU\Services\Entity;

/**
 * @author Moritz Küttel
 * @experimental
 */
trait CodeIgniterServiceEntityTrait {
    use ServiceEntityTrait;
    public function getPrimaryKeyName(): string|array|null {
        return $this->primaryKey;
    }
}