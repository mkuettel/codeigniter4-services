<?php

namespace Tests\Database;

use MKU\Services\ServiceException;
use MKU\Services\TransactionException;
use Tests\Support\Entities\Page;
use Tests\Support\Entities\PageContent;
use Tests\Support\Models\PageContentModel;
use Tests\Support\Models\PageModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use MKU\Services\TransactionService;
use MKU\Services\Config\Transaction as TransactionConfig;
use Tests\Support\Services\PageService;

/**
 *
 * @author Moritz Küttel
 */
class TransactionServiceTest extends CIUnitTestCase {
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;

    protected $namespace = 'Tests\Support';

    protected $seed = \Tests\Support\Database\Seeds\Page::class;

    private ?TransactionService $transactions;

    private ?PageContentModel $contents;

    private ?PageModel $pages;

    public function newPage(): Page {
        $page = new Page([
            'contents' => [
                new PageContent([
                    'title' => 'TestTransactionService',
                    'language' => 'en',
                    'slug' => 'test-transaction-service',
                    'description' => 'A Test transaction to test the TestTransactionService class',
                    'tags' => 'test,transaction',
                    'contents' => 'these are test contents for the test transaction'
                ]),
            ]
        ]);
        return $page;
    }
    public function newTransactionService(): TransactionService {
        return new TransactionService($this->config, $this->db);
    }

    public function setUp(): void {
        parent::setUp();

        $this->config = new TransactionConfig();
        $this->transactions = $this->newTransactionService();
        $this->pages = model(PageModel::class, false, $this->db);
        $this->contents = model(PageContentModel::class, false, $this->db);
    }

    public function tearDown(): void {
        $this->transactions = null;
        $this->pages = null;
        $this->contents = null;
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function testInitialConfig(): void {
        $config = $this->transactions->getConfig();
        $this->assertFalse($config->testMode);
        $this->assertFalse($config->strictMode);
    }

    public function testGettersSetters(): void {

        $this->config = new TransactionConfig();
        $this->config->testMode = true;
        $this->config->strictMode = false;
        $this->config->throwExceptions = false;

        $this->transactions->configure($this->config);

        $config = $this->transactions->getConfig();

        $this->assertTrue($config->testMode);
        $this->assertFalse($config->strictMode);
        $this->assertFalse($config->throwExceptions);
    }

    public function testInstantiateUsingServicesClass(): void {
        $transactionService = $this->newTransactionService();

        // if $useShared is true, we should get the exact same instance
        $this->assertEquals($transactionService, $this->newTransactionService());
    }

    public function testTransactCompletes(): void {
        $page = $this->newPage();
        $id = $this->transactions->transact(fn():bool|int => $this->pages->insert($page, true) );
        $this->assertIsInt($id);
        $this->assertEquals($id, $this->pages->find($id)->id);
    }

    public function testTransactThrows(): void {
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage("Exception during transaction, rolled back");

        $pages = $this->pages;
        $page = $this->newPage();
        $this->transactions->transact(function() use ($pages, $page) {
            $pages->insert($page, true);
            throw new ServiceException("The service irrevocably failed.");
        });
    }

    public function testTransactDoesntThrowWhenThrowsExceptionIsFalse() {
        $config = new TransactionConfig();
        $config->throwExceptions = false;
        $this->transactions->configure($config);

        $pages = $this->pages;
        $page = $this->newPage();
        try {
            $result = $this->transactions->transact(function () use ($pages, $page) {
                $pages->insert($page, true);
                throw new ServiceException("The service irrevocably failed.");
                return true;
            }, false);

            $this->assertFalse($result, "transact should return false, when an exception occurs in the inner function.");
        } catch(\Throwable $ex) {
            $this->fail("TransactionService should not have thrown an exception when throwExceptions is false.");
        }
    }

    public function testTransactRollsBackOnThrow(): void {
        $pages = $this->pages;
        $page = $this->newPage();
        try {
            $this->transactions->transact(function($db) use ($pages, $page) {
                $id = $pages->insert($page, true);
                $this->assertIsInt($id, 'couldnt insert test page');
                throw new ServiceException($id);
            });
        } catch (TransactionException $ex) {
            $id = (int)$ex->getPrevious()->getMessage();
            $this->assertGreaterThan(0, $id);

            $this->assertNull($pages->find($id), "page was saved even though the transaction should have rolled back.");
        }
    }

    public function testTransactionsRollbackInTestMode(): void {
        $this->markTestSkipped('testMode doesnt seem to work properly...');
        $page = $this->newPage();
        list($pages, $id) = $this->transactions->transact(function($db) use ($page) {
            $pages = model(PageModel::class, false, $db);
            $id = $pages->insert($page, true);
            $this->assertIsInt($id, "couldn't insert test page");
            return [$pages, $id];
        }, true);
        $this->assertNull($pages->find($id), "page was saved even though the transaction should have rolled back.");

        $config = new TransactionConfig();
        $config->testMode = true;
        $this->transactions->configure($config);

        $id = $this->transactions->transact(function() use ($pages, $page) {
            $id = $pages->insert($page, true);
            $this->assertIsInt($id, "couldn't insert test page");
            return $id;
        });
        $this->assertNull($pages->find($id), "page was saved even though the transaction should have rolled back.");
    }

    public function testFutureTransactionsFailInStrictMode(): void {
        $config = new TransactionConfig();
        $config->strictMode = true;
        $config->throwExceptions = false;
        $this->transactions->configure($config);


        $page = $this->newPage();

        // other connections should not see data from ongoing connections
        $other_conn = db_connect(config('Config\Database')->tests, false);
        //        $other_conn = db_connect($this->db, false);

        $test = $this; // capture this test case for closure
        $id = $this->transactions->transact(function() use($test, $page, $other_conn) {
            $id = $test->pages->insert($page, true);
            $test->assertIsInt($id);
            $test->assertEquals($id, $test->pages->find($id)->id);
            $test->markTestSkipped('I dont know whether this is supported by SQLite ...');

            $result = $other_conn->newQuery()->select('*')->from('pages')->where('id', $id)->get()->getResult('array');
            $test->assertEmpty($result, "The other connection should not see the page #$id, because the transaction is not committed yet. Result: ". var_export($result, true));

            return $id;
        });
        $test->markTestSkipped('I dont know whether this is supported by SQLite ...');

        $result = $other_conn->newQuery()->select('title')->from('pages')->where('id', $id)->get()->getResult('array');
        $this->assertEquals($page->id, $result['id'], "The other connection should see the page, because the transaction is committed.");
    }

//    public function testTransactionNests(): void {
//    }

//    public function testTransactionIsolation(): void {
//    }
}
