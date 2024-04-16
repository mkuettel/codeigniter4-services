<?php

namespace MKU\Services\Library\Data;

use MKU\Services\Entity\ServiceEntity;

/**
 * Implements refresh() based upon get() and ServiceEntity methods for persistence and data provider services.
 *
 * @author Moritz Küttel
 * @experimental
 */
trait RefreshProviderTrait {
    public function refresh(ServiceEntity $entity): bool {
        if (!$entity->hasPrimaryKey()) { return false; }

        $refreshed = $this->get($entity->getPrimaryKeyValue());
        if (!$refreshed) return false;

        $entity->setAttributes($refreshed->toArray());
        return true;
    }

}