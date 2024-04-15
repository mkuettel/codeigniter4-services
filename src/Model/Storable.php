<?php

namespace MKU\Services\Model;

use MKU\Services\Entity\ServiceEntity;
use MKU\Services\ServiceException;

trait Storable {
    /**
     * Store an entity in the database, either by updating or inserting it if required.
     *
     * @param ServiceEntity $entity
     * @return mixed false when the given entity could not be stored, else the primary key value of the entity is returned.
     */
    public function store(ServiceEntity $entity): mixed {
        if (empty($entity->getPrimaryKeyName())) throw new ServiceException("Can't store entity without a defined primary key, please implement getPrimaryKeyName() on the given Entity");

        $saved = false;
        if ($this->shouldUpdate($entity)) {
            if (!$entity->hasChanged()) return $entity->getPrimaryKeyValue();

            $saved = $this->update($entity->getPrimaryKeyValue(), $entity);
            $id = $entity->getPrimaryKeyValue();
        } else {
            $id = $this->insert($entity, true);
            $saved = $id !== false;
        }
        $entity->setPrimaryKeyValue($id);
        return $saved ? $id : false;
    }
}
