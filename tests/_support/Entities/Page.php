<?php

namespace Tests\Support\Entities;

use MKU\Services\Entity\ServiceEntityBase;
use Tatter\Relations\Traits\EntityTrait;

class Page extends ServiceEntityBase {
    public $primaryKey = 'id';
    protected $table = 'pages';

    protected $attributes = [
        'id' => null,
        'page_contents' => [],
    ];

    public function setPageContents(array $page_contents) {
        $this->attributes['page_contents'] = array_map(function($content) {
            if ($content instanceof \stdClass) {
                return new PageContent(get_object_vars($content));
            } else if (is_array($content)) {
                return new PageContent($content);
            } else if ($content instanceof PageContent) {
                return $content;
            } else {
                throw new \InvalidArgumentException('$page->page_contents needs to be an array of objects or data rows so they can be converted to the correct entity');
            }
        }, $page_contents);
    }


    public function getPageContent(string $lang): ?PageContent {
        $contents = $this->attributes['page_contents'];
        if (!$contents) return null;

        foreach($contents as $page_content) {
            if($page_content->language === $lang) {
                return $page_content;
            }
        }

        return null;
    }
}
