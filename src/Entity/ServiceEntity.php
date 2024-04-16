<?php
declare(strict_types=1);
namespace MKU\Services\Entity;

/**
 * All entities that are used in the in conjunction with data provider or persistence services must implement this interface.
 *
 * A ServiceEntity class that implements this interface doesn't have to be much more than a plain data object,
 * but it may also extend from the Entity class provided CodeIgniter or other libraries,
 * but it should be avoided returning database connections or result objects from the data provider or persistence services,
 * as such things should not be accessible from the controller or and surely not from the view layer.
 *
 * The implementations of getPrimaryKeyValue() and setPrimaryKeyValue() must be compatible with each other and access
 * and modify the same field(s) and attributes(s) as returned by getPrimaryKeyName().
 * Additionally they return and accept the same values of the same amount in the same order and typing as specified by
 * the value returned by getPrimaryKeyName(). This should writing more strongly typed code and avoid errors.
 *
 * Note: You can use PHPStan's template annotations to specify the type of the primary key field(s) or attribute(s).
 *
 * @template KeyName of string
 * @template PKValueTypes of string|int
 * @template PrimaryKey of null|KeyName|array<KeyName>
 * @template PrimaryKeyValue of null|PKValueTypes|array<PKValueTypes>
 * @author Moritz Küttel
 * @experimental
 */
interface ServiceEntity {
    /**
     * Get the name of the primary key field or attribute of the entity.
     *
     * When using auto increment this is usually 'id'.
     * You can return null, if your entity doesn't have a primary key
     * You may also return an array, and specify a list of primary key fields or attributes: e.g. ['x', 'y', 'z']
     * You must _always_ return the same value for the same object, even if fields or attributes are modified.
     *
     * @return PrimaryKey the name(s) of the primary key field(s) or attribute(s) of the entity
     */
    public function getPrimaryKeyName(): string|array|null;

    /**
     * Get the primary key value(s)
     *
     * You may return null, but then it is assumed that the entity is not persisted yet.
     * You may return an array of fields or attributes, if your entity has a composite primary key.
     *
     * The returned value(s) must be compatible with the type(s) of the primary key field(s) and attribute(s) as
     * returned by getPrimaryKeyName().
     *
     * @return PrimaryKeyValue the value(s) or the primary key field(s) or attribute(s)
     */
    public function getPrimaryKeyValue(): int|string|array|null;

    /**
     * Set the primary key values(s)
     *
     * The implementation is only expected to accept values compatible with the type(s) of the primary key field(s)
     * and attribute(s) as returned by getPrimaryKeyName().
     * This function usually gets called after the entity has been saved to persistent storage.
     *
     * This should be a no-op if the entity doesn't have a primary key, but may throw an error if something else
     * than null is passed in this case.
     *
     * @param PrimaryKeyValue $val the new value(s) for the primary key field(s) and attribute(s) of this entity
     * @return void
     */
    public function setPrimaryKeyValue(int|string|null $val): void;
}