<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Page extends Seeder {
    public function run() {
        $pages = $this->db->table('pages');
        $page = ['id' => rand(9999, 999999)];
        $pages->insert($page);

        $content_data = [
            'page_id' => $page['id'],
            'language' => 'de',
            'title' => 'Home',
            'description' => 'Home page',
            'tags' => 'home, page, tags',
            'contents' => "# Home\n\nWillkommen!"
        ];

        $this->db->table('page_contents')->insert($content_data);
    }
}
