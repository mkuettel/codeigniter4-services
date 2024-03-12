<?php

namespace Tests\Support\Entities;

use MKU\Services\Entity\ServiceEntityBase;
use Tatter\Relations\Traits\EntityTrait;

class Page extends ServiceEntityBase {
    use EntityTrait;

    protected $table = 'pages';
    protected $primaryKey = 'id';


    protected $attributes = [
        'id' => null,
        'page_contents' => [],
    ];

    public function setPageContents(array $contents) {
        $this->attributes['contents'] = array_map(function($content) {
            if ($content instanceof \stdClass) {
                return new PageContent(get_object_vars($content));
            } else if (is_array($content)) {
                return new PageContent($content);
            } else if ($content instanceof PageContent) {
                return $content;
            } else {
                throw new \InvalidArgumentException('$page->contents needs to be an array of objects or data rows so they can be converted to the correct entity');
            }
        }, $contents);
    }

    public function getPageContent(string $lang): ?PageContent
    {
        foreach($this->contents ?? [] as $page_content) {
            if($page_content->lang === $lang) {
                return $page_content;
            }
        }
        return null;
    }
}
