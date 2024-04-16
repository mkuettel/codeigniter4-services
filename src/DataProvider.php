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
interface DataProvider extends Service {
    /**
     * retrieve an entity from persistent storage by its primary key, usually its id
     * @param PK $id primary key of the entity
     */
    public function get($id): ?ServiceEntity;

    /**
     * Refresh an entity from persistent storage
     * @param ServiceEntity $entity the entity object to refresh with the latest data from persistent storage
     */
    public function refresh(ServiceEntity $entity): bool;

    /**
     * Check if an entity exists in persistent storage.
     *
     * Note: you can use the DataExistsProviderTrait to implement this method automatically,
     * based of get()
     *
     * @param PK $id the entity object to check for existence
     * @return ServiceEntity|null the entity object if it exists, null otherwise
     */
    public function exists($id): bool;
}