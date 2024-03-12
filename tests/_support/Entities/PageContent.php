<?php

namespace Tests\Support\Entities;

use MKU\Services\Entity\ServiceEntityBase;
use Tatter\Relations\Traits\EntityTrait;

class PageContent extends ServiceEntityBase {
    protected $primaryKey = 'id';
    protected $table = 'page_contents';

    protected $attributes = [
        'id' => null,
        'page_id' => null,
        'language' => null,
        'title' => null,
        'slug' => null,
        'description' => null,
        'tags' => null,
        'contents' => null,
        'created_at' => null,
        'updated_at' => null,
        'deleted_at' => null,
        'publish_at' => null,
        'page' => null,
    ];
}
