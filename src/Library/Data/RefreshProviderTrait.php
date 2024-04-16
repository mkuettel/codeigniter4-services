<?php

namespace MKU\Services\Library\Data;

use MKU\Services\Entity\ServiceEntity;

/**
 * @author Moritz Küttel
 * @experimental
 */
trait RefreshProviderTrait {
    public function refresh(ServiceEntity $entity): bool {
        $refreshed = $this->get($entity->getPrimaryKeyValue();
        if (!$refreshed) return false;

        $entity->setAttributes($refreshed->toArray());
        return true;
    }

}