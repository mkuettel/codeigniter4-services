<?php

namespace Tests\Support\Models;

use MKU\Services\Model\ServiceModel;
use MKU\Services\Model\Storable;
use Tests\Support\Entities\Page;

final class PageModel extends ServiceModel {
    use Storable;

	protected $table = 'pages';

    protected $allowedFields = [ 'id' ];

    protected $with = [ 'page_contents' ];

    protected $returnType = Page::class;
    protected $useSoftDeletes = false;

}
