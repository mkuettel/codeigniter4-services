<?php
declare(strict_types=1);
namespace MKU\Services\Entity;

interface ServiceEntity {
    public function getPrimaryKeyName(): int|string|array|null;
    public function getPrimaryKeyValue(): int|string|array|null;
    public function setPrimaryKeyValue(int|string|null $val): void;
}