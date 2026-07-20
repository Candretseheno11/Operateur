# Ooperateur Mobile 
...existing code...

# Operateur Mobile (CodeIgniter 4, SQLite)

Description
---
Application MVC (CodeIgniter 4) pour gérer un operateur mobile : transfert, depot 

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
   git clone https://github.com/Candretseheno11/Operateur.git
  
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
Pour creer la base de donnee 
```
mkdir database
touch database/mobilemoney.db

sqlite3 database/mobilemoney.db < base.sql
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
