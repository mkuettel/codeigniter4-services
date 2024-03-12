<?php

declare(strict_types=1);


namespace Tests\Support\Config;

use CodeIgniter\Config\BaseService;
use MKU\Services\TransactionService;
use Tests\Support\Models\PageContentModel;
use Tests\Support\Models\PageModel;
use Tests\Support\Services\PageService;

class Services extends BaseService {
    /**
     * The TransactionService class
     */
    public static function pages(TransactionService $transactions = null, PageModel $page_model = null, PageContentModel $page_content_model = null, bool $getShared = true): PageService {
        if ($getShared) {
            return self::getSharedInstance('pages', $transactions, $page_model, $page_content_model);
        }

        return new PageService(
            $transactions ?? \MKU\Services\Config\Services::transaction(),
            $page_model ?? model(PageModel::class),
            $page_content_model ?? model(PageContentModel::class),
        );
    }
}
