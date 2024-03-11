<?php

namespace MKU\Services\Model;

use MKU\Services\Entity\ServiceEntity;

trait Storable {
    // updates the given entity object with the
    // assigned id on insert, updates the record
    // when changed
    public function store(ServiceEntity $entity): bool {
        if($entity->getPrimaryKey() !== null && !$entity->getPrimaryKey()) return true;
        $saved = false;

        if ($this->shouldUpdate($entity)) {
            $saved = $this->update($this->getPrimaryKey(), $entity);
        } else {
            $id = $this->insert($entity, true);
            if($id !== false) {
                $saved = true;
                if (is_array($entity->primaryKey)) {
                    foreach ($entity->primaryKey as $key) {
                        if (isset($entity->$key) && $entity->$key === null) {
                            $entity->$key = $id;
                            break;
                        }
                    }
                } else {
                    $key = $entity->primaryKey;
                    $entity->$key = $id;
                }
            }
        }
        return $saved;
    }
}
