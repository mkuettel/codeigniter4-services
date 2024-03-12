<?php

namespace Tests\Support\Database\Migrations;

use MKU\Services\Database\BaseMigration;
use CodeIgniter\Database\RawSql;

class CreatePages extends BaseMigration {
    protected $table = 'pages';

    public function transactionalUp(): void {
        $this->forge->addField([
           'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'slug' => [
                'type'=> 'TINYTEXT',
                'null' => true,
            ],
            'description' => [
                'type' => 'TINYTEXT',
                'null' => true,
            ],
            'tags' => [
                'type' => 'TINYTEXT',
                'null' => true,
            ],
            'contents' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'on_update' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'publish_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable($this->table, false, $this->tableOptions());
    }

    public function transactionalDown(): void {
        $this->forge->dropTable($this->table, true);
    }
}
