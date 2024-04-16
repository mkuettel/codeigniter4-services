<?php
declare(strict_types=1);
namespace MKU\Services\Entity;

use CodeIgniter\Entity\Entity;

/**
 * This class provides a base implementation for the ServiceEntity interface
 * and a base class to use for your entities in your application.
 *
 * Alternatively you may choose to use the respective traits to implement the ServiceEntity interface in your own entity classes.
 *
 * @author Moritz Küttel
 * @experimental
 */
abstract class ServiceEntityBase extends Entity implements ServiceEntity {
    use CodeIgniterServiceEntityTrait;
}
