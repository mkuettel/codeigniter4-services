<?php

namespace Tests\Support\Entities;

use Tests\Support\Models\PageContentModel;
use MKU\Services\Entity\ServiceEntityBase;
use Tatter\Relations\Traits\EntityTrait;

class Page extends ServiceEntityBase {
    public $primaryKey = 'id';
    protected $table = 'pages';

    protected $attributes = [
        'id' => null,
        'page_contents' => [],
    ];
    
    private $contents_loaded = false;
    
    public function hasPageContents(): bool {
        return isset($this->attributes['page_contents']) && is_array($this->attributes['page_contents']) && count($this->attributes) > 0;
    }

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

    private function _loadPageContents() {
        $contents = model(PageContentModel::class);
        $query = $contents->builder()->select()->where('page_id', $this->id);
        $this->attributes['page_contents'] = $query->get()->getResult(PageContent::class);
    }
    public function getPageContent(string $lang): ?PageContent {
        if(!$this->hasPageContents() && !$this->contents_loaded) $this->_loadPageContents();
        if (!$this->hasPageContents()) return null;
        $contents = $this->attributes['page_contents'];

        foreach($contents as $page_content) {
            if($page_content->language === $lang) {
                return $page_content;
            }
        }

        return null;
    }
}
