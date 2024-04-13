<?php

namespace MKU\Services\Model;

use MKU\Services\Entity\ServiceEntity;

trait Storable {
    /**
     * Store an entity in the database, either by updating or inserting it if required.
     *
     * @param ServiceEntity $entity
     * @return bool whether the entity got stored in the database
     */
    public function store(ServiceEntity $entity): bool {
        if($entity->getPrimaryKeyValue() !== null && !$entity->getPrimaryKeyName()) return true;
        $saved = false;

        if ($this->shouldUpdate($entity)) {
            $saved = !$this->hasChanged($entity) || $this->update($entity->getPrimaryKeyValue(), $entity);
        } else {
            $id = $this->insert($entity, true);
            $saved = $id !== false && $entity->getPrimaryKeyName() !== null;
            if ($saved) {
                $entity->setPrimaryKeyValue($id);
            }
        }
        return $saved;
    }
}
