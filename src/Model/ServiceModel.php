<?php
declare(strict_types=1);
namespace MKU\Services\Model;

use CodeIgniter\Model;
use Tatter\Relations\Traits\ModelTrait;

abstract class ServiceModel extends Model {
    use Storable;
    use ModelTrait;
}