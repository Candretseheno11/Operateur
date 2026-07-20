PRAGMA foreign_keys = ON;

--------------------------------------------------
-- PREFIXES
--------------------------------------------------
CREATE TABLE
    prefixes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        prefixe TEXT NOT NULL UNIQUE,
        actif INTEGER NOT NULL DEFAULT 1
    );

--------------------------------------------------
-- CLIENTS
--------------------------------------------------
CREATE TABLE
    clients (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        telephone TEXT NOT NULL UNIQUE,
        nom TEXT,
        role TEXT CHECK (role IN ('client', 'operateur')) DEFAULT 'client',
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        id_prefixe INTEGER NOT NULL,
        FOREIGN KEY (id_prefixe) REFERENCES prefixes (id)
    );

--------------------------------------------------
-- COMPTES
--------------------------------------------------
CREATE TABLE
    comptes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_client INTEGER NOT NULL UNIQUE,
        solde NUMERIC NOT NULL DEFAULT 0,
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_client) REFERENCES clients (id)
    );

--------------------------------------------------
-- TYPES D'OPERATIONS
--------------------------------------------------
CREATE TABLE
    types_operations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        libelle TEXT NOT NULL UNIQUE
    );

--------------------------------------------------
-- BAREMES
--------------------------------------------------
CREATE TABLE
    baremes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_type_operation INTEGER NOT NULL,
        montant_min NUMERIC NOT NULL,
        montant_max NUMERIC NOT NULL,
        frais NUMERIC NOT NULL,
        FOREIGN KEY (id_type_operation) REFERENCES types_operations (id)
    );

--------------------------------------------------
-- TRANSACTIONS
--------------------------------------------------
CREATE TABLE
    transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        id_compte_source INTEGER,
        id_compte_destination INTEGER,
        id_type_operation INTEGER NOT NULL,
        montant NUMERIC NOT NULL,
        frais NUMERIC NOT NULL,
        date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_compte_source) REFERENCES comptes (id),
        FOREIGN KEY (id_compte_destination) REFERENCES comptes (id),
        FOREIGN KEY (id_type_operation) REFERENCES types_operations (id)
    );

--------------------------------------------------
-- ALTER TABLE CLIENTS - Ajouter colonne role
--------------------------------------------------
-- sqlite3 database/mobilemoney.db
ALTER TABLE clients
ADD COLUMN role TEXT CHECK (role IN ('client', 'operateur')) DEFAULT 'client';

UPDATE clients
SET
    role = 'operateur'
WHERE
    id = 1;

--------------------------------------------------
-- INSERT PREFIXES
--------------------------------------------------
INSERT INTO
    prefixes (prefixe)
VALUES
    ('033'),
    ('037');

--------------------------------------------------
-- TYPES D'OPERATIONS
--------------------------------------------------
INSERT INTO
    types_operations (libelle)
VALUES
    ('Depot'),
    ('Retrait'),
    ('Transfert');

--------------------------------------------------
-- BAREME DEPOT
--------------------------------------------------
INSERT INTO
    baremes (
        id_type_operation,
        montant_min,
        montant_max,
        frais
    )
VALUES
    (1, 100, 1000, 50),
    (1, 1001, 5000, 50),
    (1, 5001, 10000, 100),
    (1, 10001, 25000, 200),
    (1, 25001, 50000, 400),
    (1, 50001, 100000, 800),
    (1, 100001, 250000, 1500),
    (1, 250001, 500000, 1500),
    (1, 500001, 1000000, 2500),
    (1, 1000000, 2000000, 3000);

--------------------------------------------------
-- BAREME RETRAIT
--------------------------------------------------
INSERT INTO
    baremes (
        id_type_operation,
        montant_min,
        montant_max,
        frais
    )
VALUES
    (2, 100, 1000, 50),
    (2, 1001, 5000, 50),
    (2, 5001, 10000, 100),
    (2, 10001, 25000, 200),
    (2, 25001, 50000, 400),
    (2, 50001, 100000, 800),
    (2, 100001, 250000, 1500),
    (2, 250001, 500000, 1500),
    (2, 500001, 1000000, 2500),
    (2, 1000000, 2000000, 3000);

--------------------------------------------------
-- BAREME TRANSFERT
--------------------------------------------------
INSERT INTO
    baremes (
        id_type_operation,
        montant_min,
        montant_max,
        frais
    )
VALUES
    (3, 100, 1000, 50),
    (3, 1001, 5000, 50),
    (3, 5001, 10000, 100),
    (3, 10001, 25000, 200),
    (3, 25001, 50000, 400),
    (3, 50001, 100000, 800),
    (3, 100001, 250000, 1500),
    (3, 250001, 500000, 1500),
    (3, 500001, 1000000, 2500),
    (3, 1000000, 2000000, 3000);

--------------------------------------------------
-- CLIENTS
--------------------------------------------------
INSERT INTO
    clients (telephone, nom, id_prefixe)
VALUES
    ('0331234567', 'Marie', 1),
    ('0379876543', 'Alice', 2),
    ('0337654321', 'Bob', 1),
    ('0371234567', 'Charlie', 2);

--------------------------------------------------
-- COMPTES
--------------------------------------------------
INSERT INTO
    comptes (id_client, solde)
VALUES
    (1, 500000),
    (2, 250000),
    (3, 800000),
    (4, 100000);

--------------------------------------------------
-- TRANSACTIONS
--------------------------------------------------
INSERT INTO
    transactions (
        id_compte_source,
        id_compte_destination,
        id_type_operation,
        montant,
        frais
    )
VALUES
    (NULL, 1, 1, 500000, 0),
    (1, NULL, 2, 100000, 1000),
    (1, 2, 3, 50000, 600),
    (3, 4, 3, 100000, 1200);