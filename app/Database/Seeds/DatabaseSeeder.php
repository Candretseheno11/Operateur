<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {

        $this->db->query('PRAGMA foreign_keys = OFF;'); // Pour SQLite
        // $this->db->query('SET FOREIGN_KEY_CHECKS = 0;'); // Décommentez ceci si vous utilisez MySQL

        $this->db->table('conges')->emptyTable();
        $this->db->table('soldes')->emptyTable();
        $this->db->table('employes')->emptyTable();
        $this->db->table('types_conge')->emptyTable();
        $this->db->table('departements')->emptyTable();

        $this->db->query('PRAGMA foreign_keys = ON;'); // Réactiver pour SQLite
        // $this->db->query('SET FOREIGN_KEY_CHECKS = 1;'); // Réactiver pour MySQL
        // 1. Départements
        $departements = [
            ['nom' => 'Informatique', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Ressources Humaines', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Commercial', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Marketing', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Finance', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Direction', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('departements')->insertBatch($departements);

        // 2. Types de congé
        $types = [
            ['nom' => 'Congés payés', 'jours_annuels' => 25, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nom' => 'RTT', 'jours_annuels' => 12, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Congés sans solde', 'jours_annuels' => 0, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Maladie', 'jours_annuels' => 10, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Congés exceptionnels', 'jours_annuels' => 5, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Congés maternité', 'jours_annuels' => 112, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Congés paternité', 'jours_annuels' => 14, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('types_conge')->insertBatch($types);

        // 3. Employés
        $password = password_hash('password123', PASSWORD_DEFAULT);

        // Admin
        $this->db->table('employes')->insert([
            'matricule' => 'ADMIN001',
            'nom' => 'Administrateur',
            'prenom' => 'System',
            'email' => 'admin@conges.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'date_embauche' => '2020-01-01',
            'departement_id' => 6,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // RH
        $this->db->table('employes')->insert([
            'matricule' => 'RH001',
            'nom' => 'Dupont',
            'prenom' => 'Sophie',
            'email' => 'rh@conges.com',
            'password' => password_hash('rh123', PASSWORD_DEFAULT),
            'role' => 'rh',
            'date_embauche' => '2021-01-15',
            'departement_id' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Employés (IDs: 3 à 10)
        $employes = [
            [
                'matricule' => 'EMP001',
                'nom' => 'Martin',
                'prenom' => 'Jean',
                'email' => 'jean.martin@conges.com',
                'role' => 'employee',
                'date_embauche' => '2022-06-01',
                'departement_id' => 1,
            ],
            [
                'matricule' => 'EMP002',
                'nom' => 'Bernard',
                'prenom' => 'Sophie',
                'email' => 'sophie.bernard@conges.com',
                'role' => 'employee',
                'date_embauche' => '2022-08-15',
                'departement_id' => 3,
            ],
            [
                'matricule' => 'EMP003',
                'nom' => 'Dubois',
                'prenom' => 'Thomas',
                'email' => 'thomas.dubois@conges.com',
                'role' => 'employee',
                'date_embauche' => '2023-01-10',
                'departement_id' => 4,
            ],
            [
                'matricule' => 'EMP004',
                'nom' => 'Petit',
                'prenom' => 'Claire',
                'email' => 'claire.petit@conges.com',
                'role' => 'employee',
                'date_embauche' => '2023-03-20',
                'departement_id' => 5,
            ],
            [
                'matricule' => 'EMP005',
                'nom' => 'Leroy',
                'prenom' => 'Nicolas',
                'email' => 'nicolas.leroy@conges.com',
                'role' => 'employee',
                'date_embauche' => '2023-06-10',
                'departement_id' => 1,
            ],
            [
                'matricule' => 'EMP006',
                'nom' => 'Moreau',
                'prenom' => 'Julie',
                'email' => 'julie.moreau@conges.com',
                'role' => 'employee',
                'date_embauche' => '2023-09-05',
                'departement_id' => 2,
            ],
            [
                'matricule' => 'EMP007',
                'nom' => 'Simon',
                'prenom' => 'Pierre',
                'email' => 'pierre.simon@conges.com',
                'role' => 'employee',
                'date_embauche' => '2024-01-15',
                'departement_id' => 3,
            ],
            [
                'matricule' => 'EMP008',
                'nom' => 'Laurent',
                'prenom' => 'Céline',
                'email' => 'celine.laurent@conges.com',
                'role' => 'employee',
                'date_embauche' => '2024-02-20',
                'departement_id' => 4,
            ],
        ];

        foreach ($employes as $emp) {
            $emp['password'] = $password;
            $emp['created_at'] = date('Y-m-d H:i:s');
            $emp['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('employes')->insert($emp);
        }

        // 4. Soldes initialisés (employés ID 3 à 10)
        for ($i = 3; $i <= 10; $i++) {
            // Congés payés (type 1)
            $this->db->table('soldes')->insert([
                'employe_id' => $i,
                'type_conge_id' => 1,
                'solde' => 25,
                'annee' => date('Y'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // RTT (type 2)
            $this->db->table('soldes')->insert([
                'employe_id' => $i,
                'type_conge_id' => 2,
                'solde' => 12,
                'annee' => date('Y'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Maladie (type 4)
            $this->db->table('soldes')->insert([
                'employe_id' => $i,
                'type_conge_id' => 4,
                'solde' => 10,
                'annee' => date('Y'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // 5. Demandes de congés
        $conges = [
            // Jean Martin (ID 3)
            ['employe_id' => 3, 'type_conge_id' => 1, 'date_debut' => '2025-06-23', 'date_fin' => '2025-06-27', 'nb_jours' => 5, 'statut' => 'en_attente', 'motif' => 'Vacances d\'été', 'created_at' => '2025-05-10 10:00:00'],
            ['employe_id' => 3, 'type_conge_id' => 4, 'date_debut' => '2025-06-02', 'date_fin' => '2025-06-03', 'nb_jours' => 2, 'statut' => 'approuve', 'motif' => 'Arrêt maladie', 'commentaire' => 'Certificat médical fourni', 'created_at' => '2025-05-25 14:30:00'],
            ['employe_id' => 3, 'type_conge_id' => 1, 'date_debut' => '2025-05-12', 'date_fin' => '2025-05-16', 'nb_jours' => 5, 'statut' => 'approuve', 'motif' => 'Congés printemps', 'commentaire' => 'Validé par RH', 'created_at' => '2025-04-01 11:00:00'],
            ['employe_id' => 3, 'type_conge_id' => 5, 'date_debut' => '2025-04-05', 'date_fin' => '2025-04-05', 'nb_jours' => 1, 'statut' => 'refuse', 'motif' => 'Événement familial', 'commentaire' => 'Chevauchement avec période bloquée', 'created_at' => '2025-03-20 09:00:00'],
            ['employe_id' => 3, 'type_conge_id' => 3, 'date_debut' => '2025-03-10', 'date_fin' => '2025-03-12', 'nb_jours' => 3, 'statut' => 'annule', 'motif' => 'Raisons personnelles', 'commentaire' => 'Annulé par l\'employé', 'created_at' => '2025-02-15 08:00:00'],

            // Sophie Bernard (ID 4)
            ['employe_id' => 4, 'type_conge_id' => 1, 'date_debut' => '2025-07-10', 'date_fin' => '2025-07-20', 'nb_jours' => 11, 'statut' => 'en_attente', 'motif' => 'Grandes vacances', 'created_at' => '2025-06-01 13:00:00'],
            ['employe_id' => 4, 'type_conge_id' => 2, 'date_debut' => '2025-05-19', 'date_fin' => '2025-05-20', 'nb_jours' => 2, 'statut' => 'approuve', 'motif' => 'RTT', 'commentaire' => 'Approuvé', 'created_at' => '2025-04-25 10:00:00'],
            ['employe_id' => 4, 'type_conge_id' => 4, 'date_debut' => '2025-04-15', 'date_fin' => '2025-04-18', 'nb_jours' => 4, 'statut' => 'approuve', 'motif' => 'Grippe', 'commentaire' => 'Arrêt de travail', 'created_at' => '2025-04-14 08:30:00'],

            // Thomas Dubois (ID 5)
            ['employe_id' => 5, 'type_conge_id' => 1, 'date_debut' => '2025-08-01', 'date_fin' => '2025-08-15', 'nb_jours' => 15, 'statut' => 'en_attente', 'motif' => 'Vacances estivales', 'created_at' => '2025-06-15 09:00:00'],
            ['employe_id' => 5, 'type_conge_id' => 2, 'date_debut' => '2025-05-05', 'date_fin' => '2025-05-06', 'nb_jours' => 2, 'statut' => 'approuve', 'motif' => 'RTT', 'commentaire' => 'OK', 'created_at' => '2025-04-20 14:00:00'],

            // Claire Petit (ID 6)
            ['employe_id' => 6, 'type_conge_id' => 7, 'date_debut' => '2025-09-01', 'date_fin' => '2025-09-14', 'nb_jours' => 14, 'statut' => 'approuve', 'motif' => 'Congés paternité', 'commentaire' => 'Félicitations !', 'created_at' => '2025-07-01 10:00:00'],

            // Nicolas Leroy (ID 7)
            ['employe_id' => 7, 'type_conge_id' => 1, 'date_debut' => '2025-06-10', 'date_fin' => '2025-06-20', 'nb_jours' => 11, 'statut' => 'en_attente', 'motif' => 'Vacances', 'created_at' => '2025-05-05 11:00:00'],

            // Julie Moreau (ID 8)
            ['employe_id' => 8, 'type_conge_id' => 4, 'date_debut' => '2025-05-25', 'date_fin' => '2025-05-28', 'nb_jours' => 4, 'statut' => 'approuve', 'motif' => 'Maladie', 'commentaire' => 'Arrêt de travail', 'created_at' => '2025-05-24 08:00:00'],
        ];

        foreach ($conges as $conge) {
            if (!isset($conge['updated_at'])) {
                $conge['updated_at'] = $conge['created_at'];
            }
            $this->db->table('conges')->insert($conge);
        }

        echo "\n✅ Base de données initialisée avec succès !\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📊 Comptes créés :\n";
        echo "   👑 Admin: admin@conges.com / admin123\n";
        echo "   👤 RH: rh@conges.com / rh123\n";
        echo "   👥 Employés (mot de passe: password123):\n";
        echo "      - jean.martin@conges.com (Informatique)\n";
        echo "      - sophie.bernard@conges.com (Commercial)\n";
        echo "      - thomas.dubois@conges.com (Marketing)\n";
        echo "      - claire.petit@conges.com (Finance)\n";
        echo "      - nicolas.leroy@conges.com (Informatique)\n";
        echo "      - julie.moreau@conges.com (RH)\n";
        echo "      - pierre.simon@conges.com (Commercial)\n";
        echo "      - celine.laurent@conges.com (Marketing)\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📅 Demandes de congés ajoutées :\n";
        echo "   - En attente: 3 demandes\n";
        echo "   - Approuvées: 5 demandes\n";
        echo "   - Refusées: 1 demande\n";
        echo "   - Annulées: 1 demande\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    }

}