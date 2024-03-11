<?php
declare(strict_types=1);
namespace MKU\Services\Entity;

interface ServiceEntity {
    public function getPrimaryKey(): int|string|array|null;
}