<?php

namespace lib\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Entities\Page;
use Tests\Support\Entities\PageContent;
use Tests\Support\Models\PageContentModel;
use Tests\Support\Models\PageModel;

class PageContentTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = true;

    protected $namespace = null;

    private ?PageContentModel $model;

    private ?Page $page;

    public function setUp(): void {
        parent::setUp();

        $this->model = model(PageContentModel::class);
        $this->page = new Page();
        $this->page->id = model(PageModel::class)->insert($this->page, true);
    }

    public function tearDown(): void {
        $this->model = null;
        $this->page = null;
        parent::tearDown();
    }

    public function newPageContent(): PageContent {
        return new PageContent([
            'page_id' => $this->page->id,
            'lang' => 'en',
            'title' => 'TestPageContent',
            'slug' => 'test-page_content',
            'description' => 'A Test page_content to test the PageContentModel class',
            'tags' => 'test,page_content',
            'contents' => 'these are test contents for the test page_content'
        ]);
    }


    public function testAttributes(): void {
        $page_content = $this->newPageContent();
        $this->assertNull($page_content->id);
        $this->assertIsString($page_content->title);
        $this->assertIsString($page_content->description);
        $this->assertIsString($page_content->slug);
        $this->assertIsString($page_content->tags);
        $this->assertIsString($page_content->contents);
        $this->assertNull($page_content->menu_id);
        $this->assertNull($page_content->created_at);
        $this->assertNull($page_content->updated_at);
        $this->assertNull($page_content->deleted_at);
        $this->assertNull($page_content->publish_at);
    }

    public function testCreate(): void {
        $page_content = $this->newPageContent();
        $id = $this->model->insert($page_content, true);
        $this->assertIsInt($id);

    }

    public function testFind(): void {
        $page_content = $this->newPageContent();
        $id = $this->model->insert($page_content, true);
        $this->assertIsInt($id);

        $found = $this->model->find($id);
        self::assertEquals($id, $found->id);
        $this->assertEquals($page_content->title, $found->title);
        $this->assertEquals($page_content->descrption, $found->descrption);
        $this->assertEquals($page_content->tags, $found->tags);
        $this->assertEquals($page_content->contents, $found->contents);
        $this->assertEquals($page_content->menu_id, $found->menu_id);
        $this->assertGreaterThanOrEqual(time(), $page_content->created_at);
        $this->assertGreaterThanOrEqual(time(), $page_content->updated_at);
        $this->assertNull($page_content->deleted_at);
        $this->assertNull($page_content->publish_at);
    }

    public function testUpdate(): void {
        $page_content = $this->newPageContent();
        $id = $this->model->insert($page_content, true);

        $found = $this->model->find($id);
        $found->title = 'updated title';
        $found->description = 'updated description';
        $this->model->update($found->id, $found);


        $updated = $this->model->find($id);
        $this->assertNotEquals($page_content->title, $updated->title);
        $this->assertEquals($found->title, $updated->title);
        $this->assertNotEquals($page_content->description, $updated->description);
        $this->assertEquals($found->description, $updated->description);
    }
}