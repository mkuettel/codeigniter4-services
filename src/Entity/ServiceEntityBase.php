<?php
declare(strict_types=1);
namespace MKU\Services\Entity;

use CodeIgniter\Entity\Entity;

abstract class ServiceEntityBase extends Entity implements ServiceEntity {
    use ServiceEntityTrait;
}
