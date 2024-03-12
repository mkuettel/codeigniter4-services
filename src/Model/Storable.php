<?php

namespace MKU\Services\Model;

use MKU\Services\Entity\ServiceEntity;

trait Storable {
    // updates the given entity object with the
    // assigned id on insert, updates the record
    // when changed
    public function store(ServiceEntity $entity): bool {
        if($entity->getPrimaryKeyValue() !== null && !$entity->getPrimaryKeyName()) return true;
        $saved = false;

        if ($this->shouldUpdate($entity)) {
            $saved = $this->update($entity->getPrimaryKeyValue(), $entity);
        } else {
            $id = $this->insert($entity, true);
            if($id !== false && $entity->getPrimaryKeyName() !== null) {
                $saved = true;
                $entity->setPrimaryKeyValue($id);
            }
        }
        return $saved;
    }
}
