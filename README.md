# Entreprise
...existing code...

# Entreprise — Gestion des congés (CodeIgniter 4, SQLite)

Description
---
Application MVC (CodeIgniter 4) pour gérer les congés : types de congés, demandes, soldes et workflow d'approbation.

Prérequis
---
- PHP 7.4+ (8.x recommandé)
- Composer
- SQLite3 (binaires disponibles sur la machine)
- Extensions PHP : sqlite3, pdo_sqlite, mbstring, json, intl
- (Optionnel) Node.js / npm pour assets

Installation (locale)
---
1. Cloner le dépôt et se placer dans le répertoire :
   ```
   git clone <url> "/home/christon/Code/Entreprise (Copie)"
   cd "/home/christon/Code/Entreprise (Copie)"
   ```

2. Installer les dépendances PHP :
   ```
   composer install
   ```

3. Copier et éditer le fichier d’environnement :
   ```
   cp env .env
   ```
   Dans `.env` définir `app.baseURL` et config DB (exemple ci‑dessous).

Configuration SQLite (exemple .env)
---
Mettre le chemin absolu vers le fichier SQLite (recommandé dans `writable/` ou `app/Database/`).

Exemple minimal à coller dans `.env` :
```
app.baseURL = 'http://localhost:8080'

database.default.DBDriver = SQLite3
database.default.database = /home/christon/Code/Entreprise (Copie)/writable/database.sqlite
database.default.DBPrefix =
database.default.charset = utf8
database.default.DBDebug = true
```

Créer le fichier SQLite (si non présent) :
```
mkdir -p writable
touch "writable/database.sqlite"
chmod 664 "writable/database.sqlite"
```
(Assurez-vous que l’utilisateur web peut écrire sur writable/)

Migrations et seeders
---
Si le projet contient des migrations/seeders :
```
php spark migrate
php spark db:seed DatabaseSeeder
```
Le seeder insère les types de congés et comptes demo. Si vous utilisez une DB vierge, exécuter ces commandes après avoir créé le fichier SQLite.

Démarrage en développement
---
- Lancer le serveur de développement CodeIgniter :
  ```
  php spark serve
  ```
  Ou :
  ```
  php -S localhost:8080 -t public
  ```

Tests
---
- PHPUnit (si configuré) :
  ```
  ./vendor/bin/phpunit --configuration phpunit.xml.dist
  ```
- Ou via spark (si utilisé) :
  ```
  php spark test
  ```

Structure importante
---
- public/ — point d’entrée (index.php)
- app/Controllers/ — contrôleurs (ex : Admin.php, Suggestion.php)
- app/Models/ — modèles (CongeModel, SoldeModel, TypeCongeModel)
- app/Views/ — vues (Auth/login.php, Admin/soldes.php)
- app/Database/Seeds/ — seeders (DatabaseSeeder)
- writable/ — logs, fichiers runtime (ici le fichier SQLite recommandé)
- spark — helper CLI
- LICENSE — MIT

Endpoints usuels
---
- / — tableau de bord (Admin::index)
- /auth/login — connexion
- /admin/soldes — affichage des soldes
- /admin/types — gestion types de congés
- /conges — CRUD demandes de congé
- /suggestions — soumissions utilisateurs

Bonnes pratiques
---
- Utiliser un chemin absolu pour SQLite dans .env pour éviter erreurs de chemin.
- Ne pas stocker la DB de production dans le repo.
- Sauvegarder régulièrement le fichier .sqlite.
- Mettre writable/ accessible en écriture pour l’utilisateur du serveur web.

Dépannage rapide
---
- Erreur DB : vérifier le chemin dans `.env` et les permissions du fichier `.sqlite`.
- Logs : consulter `writable/logs/`.
- Migrations échouent : supprimer/renommer le fichier SQLite et relancer `php spark migrate`.

Licence
---
MIT — voir fichier LICENSE.# Operateur
