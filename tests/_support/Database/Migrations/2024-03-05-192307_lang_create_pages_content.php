<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\RawSql;
use MKU\Services\Database\BaseMigration;

class LangCreatePagesContent extends BaseMigration {

    protected string $table = 'page_contents';

    public function transactionalUp(): void {
        $this->forge->dropTable($this->table, true);
        $this->forge->renameTable('pages', $this->table);

        $this->forge->addField([
            'id' => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pages');

        $this->forge->addColumn($this->table, [
            'page_id' => [
                'type' => 'int',
                'unsigned' => true,
                'null' => false,
                'after' => 'id'
            ],
            'language' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'null' => false,
                'after' => 'page_id'
            ],
        ]);



        $this->db->table($this->table)->update([
            'page_id' => new RawSql('id'),
            'language' => 'en',
        ]);

        $pages = $this->db->table($this->table)->select('page_id as id')->distinct()->get()->getResultArray();
        if(count($pages) > 0) $this->db->table('pages')->insertBatch($pages);

        $this->forge->addForeignKey('page_id', 'pages', 'id', '', 'CASCADE');
        $this->forge->addUniqueKey(['language', 'page_id']);

    }

    public function transactionalDown(): void {
        $this->forge->dropColumn($this->table, ['lang', 'page_id']);
        $this->forge->dropTable('pages');
        $this->forge->renameTable($this->table, 'pages');
    }
}
