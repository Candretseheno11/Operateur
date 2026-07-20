<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCongesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'employe_id' => ['type' => 'INTEGER'],
            'type_conge_id' => ['type' => 'INTEGER'],
            'date_debut' => ['type' => 'DATE'],
            'date_fin' => ['type' => 'DATE'],
            'nb_jours' => ['type' => 'FLOAT'],
            'statut' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'en_attente'],
            'motif' => ['type' => 'TEXT', 'null' => true],
            'commentaire' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('conges');
    }

    public function down()
    {
        $this->forge->dropTable('conges');
    }
}
