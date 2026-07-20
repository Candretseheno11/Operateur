<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSoldesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'employe_id' => ['type' => 'INTEGER'],
            'type_conge_id' => ['type' => 'INTEGER'],
            'solde' => ['type' => 'FLOAT', 'default' => 0],
            'annee' => ['type' => 'INTEGER'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('soldes');
    }

    public function down()
    {
        $this->forge->dropTable('soldes');
    }
}
