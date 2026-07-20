<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDepartementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'nom' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('departements');
    }

    public function down()
    {
        $this->forge->dropTable('departements');
    }
}
