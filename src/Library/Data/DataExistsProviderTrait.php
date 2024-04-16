<?php

namespace MKU\Services\Library\Data;

trait DataExistsProviderTrait {
    public function exists($id): bool {
        return $this->get($id) !== null;
    }

}