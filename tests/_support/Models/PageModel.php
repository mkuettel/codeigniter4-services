<?php

namespace Tests\Support\Models;

use MKU\Services\Model\ServiceModel;
use MKU\Services\Model\Storable;
use Tatter\Relations\Traits\ModelTrait;
use Tests\Support\Entities\Page;

final class PageModel extends ServiceModel {
    use Storable;
    use ModelTrait;

	protected $table = 'pages';

    protected $allowedFields = [ 'id' ];

    protected $with = [ 'page_contents' ];

    protected $returnType = Page::class;
    protected $useSoftDeletes = false;

}
