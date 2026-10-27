<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSectionHeadingsToSchedulerSessionTalks extends Migration
{
    public function up()
    {
        $columns = [];

        if (!$this->db->fieldExists('item_type', 'scheduler_session_talks')) {
            $columns['item_type'] = [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'talk',
            ];
        }

        if (!$this->db->fieldExists('item_title', 'scheduler_session_talks')) {
            $columns['item_title'] = [
                'type' => 'VARCHAR',
                'constraint' => 555,
                'null' => true,
            ];
        }

        if ($columns) {
            $this->forge->addColumn('scheduler_session_talks', $columns);
        }
    }

    public function down()
    {
        $columns = [];

        if ($this->db->fieldExists('item_type', 'scheduler_session_talks')) {
            $columns[] = 'item_type';
        }

        if ($this->db->fieldExists('item_title', 'scheduler_session_talks')) {
            $columns[] = 'item_title';
        }

        if ($columns) {
            $this->forge->dropColumn('scheduler_session_talks', $columns);
        }
    }
}
