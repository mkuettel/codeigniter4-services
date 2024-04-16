<?php

namespace MKU\Services;

use MKU\Services\Entity\ServiceEntity;
use Prewk\Result;

/**
 * @author Moritz Küttel
 * @template T of ServiceEntity
 * @template PK of string|int|array
 * @experimental This interface is experimental and may change in the future.
 */
interface PersistenceService extends DataProvider {
    /**
     * Save an entity to persistent storage.
     * Note that the given entity object might be changed by saving it,
     * e.g. its primary key might be set and its timestamps might be updated.
     *
     * @param ServiceEntity $entity the entity to save, might be mutated
     * @retrun Result<ServiceEntity, array>
     */
    public function save(ServiceEntity $entity): Result;


    /**
     * Delete an entity from persistent storage
     *
     * @param PK $id primary key of the entity, usually its id
     * @return bool whether the entity was deleted
     */
    public function delete($id): ServiceEntity|bool;
}