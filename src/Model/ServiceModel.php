<?php
declare(strict_types=1);
namespace MKU\Services\Model;

use CodeIgniter\Model;

abstract class ServiceModel extends Model {
    use Storable;
}