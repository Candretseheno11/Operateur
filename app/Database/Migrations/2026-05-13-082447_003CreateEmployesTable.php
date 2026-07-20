<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'matricule' => ['type' => 'VARCHAR', 'constraint' => 50],
            'nom' => ['type' => 'VARCHAR', 'constraint' => 100],
            'prenom' => ['type' => 'VARCHAR', 'constraint' => 100],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'employee'],
            'departement_id' => ['type' => 'INTEGER', 'null' => true],
            'date_embauche' => ['type' => 'DATE'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('employes');
    }

    public function down()
    {
        $this->forge->dropTable('employes');
    }
}
