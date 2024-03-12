<?php

namespace Tests\Support\Services;

use CodeIgniter\Database\ResultInterface;
use MKU\Services\ServiceException;
use MKU\Services\Service;
use MKU\Services\TransactionService;
use Prewk\Result;
use Tests\Support\Entities\Page;
use Tests\Support\Models\PageContentModel;
use Tests\Support\Models\PageModel;

final class PageService implements Service {
    private TransactionService $transaction;
    private PageModel $pages;
    private PageContentModel $page_contents;

    public function __construct(TransactionService $transaction, PageModel $pages, PageContentModel $page_contents) {
        $this->transaction = $transaction;
        $this->pages = $pages;
        $this->page_contents = $page_contents;
    }

    public function save(Page $page): Result {
        return $this->transaction->transact(function () use (&$page) {
            if (!$this->pages->validate($page)) {
                return err($this->pages->errors());
            }

            foreach($page->page_contents ?? [] as $page_content) {
                if(!$this->page_contents->validate($page_content)) {
                    return err($this->page_contents->errors());
                }
            }

            if (!$this->pages->store($page)) {
                throw new ServiceException("Couldn't store page: " . json_encode($this->pages->errors()));
            }

            foreach($page->page_contents as $page_content) {
                $page_content->page_id = $page->id;
                if(!$this->page_contents->store($page_content)) {
                    throw new ServiceException("Couldn't store page content: " . json_encode($this->page_contents->errors()));
                }
            }

            return ok($page);
        });
    }

    public function get(int $id): ?Page {
        $result = $this->pages->find($id);
        if (is_object($result) && (int)$result->id === $id) {
            return $result;
        }

        return null;
    }

    public function search(array $filters = []): ResultInterface {
        $q = $this->pages->with('website_page_page_contents')->builder()->select();
        if(isset($filters['title'])) {
            $q = $q->whereIn(
                'id',
                $this->page_contents->builder()->select('page_id')->like('title', $filters['title'])
            );
        }
        // TODO: implement some more filters

        if(isset($filters['limit'])) {
            $q = $q->limit($filters['limit']);
        }
        return $q->get();
    }

    public function delete(int $id): false|Page {
        $page =  $this->get($id);
        if($page === null) {
            return false;
        }

        $result = $this->pages->delete($id);
        if(!$result) {
            return false;
        }

        return $page;
    }
}