<?php

namespace Tests\Support\Models;

use MKU\Services\Model\ServiceModel;
use Tests\Support\Entities\PageContent;

final class PageContentModel extends ServiceModel {

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

	protected $table = 'page_contents';

    protected $with = [
        'pages'
    ];

	protected $allowedFields = [
		'page_id', 'language', 'title', 'slug', 'description', 'tags', 'contents', 'publish_at'
	];
	protected $returnType = PageContent::class;

    const ORDERABLE = [
        0 => 'id',
        1 => 'page_id',
        2 => 'language',
        3 => 'title',
        4 => 'slug',
        5 => 'description',
        6 => 'tags',
        7 => 'contents',
        8 => 'created_at',
        9 => 'updated_at',
        10 => 'deleted_at',
        11 => 'publish_at',
    ];
    protected $validationRules = [
        'title' => 'required|max_length[255]',
        'description' => 'max_length[4096]',
        'tags' => 'max_length[4096]',
        'contents' => 'required',
        'page_id' => 'required',
        'language' => 'required',
    ];
}
