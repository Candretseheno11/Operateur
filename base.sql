PRAGMA foreign_keys = ON;

--------------------------------------------------
-- PREFIXES
--------------------------------------------------
CREATE TABLE
    prefixes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        prefixe TEXT NOT NULL UNIQUE,
        actif INTEGER NOT NULL DEFAULT 1,
        est_autre_operateur INTEGER NOT NULL DEFAULT 0,
        pourcentage_extra NUMERIC DEFAULT 0.0
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
        frais_promotion NUMERIC DEFAULT 0,
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

insert into
    comptes (id_client, solde)
values
    (5, 1000000),
    (6, 2000000),
    (7, 1500000),
    (8, 3000000),
    (9, 5000000),
    (12, 7500000);

create table
    user (
        id integer primary key autoincrement,
        username text not null unique,
        password text not null,
        role text check (role in ('admin', 'user')) default 'user',
        date_creation datetime default current_timestamp
    );

insert into
    user (username, password, role)
values
    ('admin', 'admin123', 'admin');

BEGIN TRANSACTION;

--------------------------------------------------
-- DONNÉES COMPLÉMENTAIRES : CLIENTS
--------------------------------------------------
-- Note : L'ID 1 est défini comme 'operateur' (conformément à ton ALTER TABLE / UPDATE)
INSERT INTO
    clients (id, telephone, nom, role, id_prefixe)
VALUES
    (5, '0333344455', 'Rakoto Jean', 'client', 1),
    (6, '0375566778', 'Rasoa Noro', 'client', 2),
    (7, '0331122334', 'Rabe Paul', 'client', 1),
    (8, '0379988776', 'Randria Soa', 'client', 2),
    (9, '0336677889', 'Andry Tiana', 'client', 1),
    (10, '0374433221', 'Koto Marc', 'client', 2),
    (11, '0338899001', 'Tina Sahondra', 'client', 1),
    (12, '0372233445', 'Hery Lala', 'client', 2);

--------------------------------------------------
-- COMPTES CLIENTS
--------------------------------------------------
-- Insertion des comptes pour les nouveaux clients (5 à 12)
INSERT INTO
    comptes (id_client, solde)
VALUES
    (5, 1000000),
    (6, 2000000),
    (7, 1500000),
    (8, 3000000),
    (9, 5000000),
    (10, 450000),
    (11, 1200000),
    (12, 7500000);

--------------------------------------------------
-- TRANSACTIONS VARIÉES
--------------------------------------------------
-- Types : 1 = Dépôt (compte_source NULL), 2 = Retrait (compte_dest NULL), 3 = Transfert
INSERT INTO
    transactions (
        id_compte_source,
        id_compte_destination,
        id_type_operation,
        montant,
        frais,
        date_transaction
    )
VALUES
    -- Dépôts initiaux / approvisionnements
    (NULL, 5, 1, 500000, 1500, '2026-07-01 08:30:00'),
    (NULL, 6, 1, 1000000, 2500, '2026-07-02 09:15:00'),
    (NULL, 7, 1, 200000, 1500, '2026-07-03 10:00:00'),
    (NULL, 10, 1, 50000, 400, '2026-07-04 14:20:00'),
    -- Retraits d'argent
    (5, NULL, 2, 50000, 400, '2026-07-05 11:45:00'),
    (6, NULL, 2, 200000, 1500, '2026-07-06 16:10:00'),
    (8, NULL, 2, 500000, 2500, '2026-07-07 09:00:00'),
    -- Transferts entre comptes clients
    (5, 6, 3, 20000, 200, '2026-07-08 13:00:00'),
    (6, 7, 3, 150000, 1500, '2026-07-09 15:30:00'),
    (7, 8, 3, 30000, 400, '2026-07-10 10:15:00'),
    (8, 9, 3, 500000, 2500, '2026-07-11 17:45:00'),
    (9, 10, 3, 10000, 100, '2026-07-12 12:00:00'),
    (11, 12, 3, 250000, 1500, '2026-07-13 08:50:00'),
    (12, 5, 3, 1000000, 3000, '2026-07-14 19:10:00'),
    (10, 5, 3, 15000, 200, '2026-07-15 14:05:00');

COMMIT;

INSERT INTO
    clients (telephone, nom, role, id_prefixe)
VALUES
    -- Clients sur 034 (id_prefixe = 3)
    ('0341112233', 'Soa Fanjava', 'client', 3),
    ('0344455667', 'Koto Faneva', 'client', 3),
    -- Clients sur 038 (id_prefixe = 4)
    ('0387788990', 'Bako Nivo', 'client', 4),
    ('0382233441', 'Haja Nirina', 'client', 4),
    -- Un client supplémentaire sur 033 (id_prefixe = 1)
    ('0339900112', 'Vola Tahina', 'client', 1);

INSERT INTO
    comptes (id_client, solde)
VALUES
    (13, 800000), -- Client 034 (Soa)
    (14, 1500000), -- Client 034 (Koto)
    (15, 600000), -- Client 038 (Bako)
    (16, 2000000), -- Client 038 (Haja)
    (17, 3000000);

-- Client 033 (Vola)
INSERT INTO
    transactions (
        id_compte_source,
        id_compte_destination,
        id_type_operation,
        montant,
        frais,
        frais_promotion,
        date_transaction
    )
VALUES
    -- --- Dépôts & Retraits sur 034 et 038 ---
    (NULL, 13, 1, 500000, 1500, 0, '2026-07-16 08:00:00'), -- Dépôt vers 034
    (NULL, 15, 1, 300000, 1500, 0, '2026-07-16 09:30:00'), -- Dépôt vers 038
    (14, NULL, 2, 100000, 800, 0, '2026-07-16 11:15:00'), -- Retrait depuis 034
    (16, NULL, 2, 250000, 1500, 0, '2026-07-16 14:00:00'), -- Retrait depuis 038
    -- --- Transactions depuis 033 vers 034 et 038 ---
    (5, 13, 3, 50000, 400, 0, '2026-07-17 10:00:00'), -- 033 (Jean) -> 034 (Soa)
    (5, 15, 3, 100000, 800, 0, '2026-07-17 10:30:00'), -- 033 (Jean) -> 038 (Bako)
    (17, 14, 3, 200000, 1500, 0, '2026-07-17 11:45:00'), -- 033 (Vola) -> 034 (Koto)
    (17, 16, 3, 500000, 2500, 0, '2026-07-17 15:20:00'), -- 033 (Vola) -> 038 (Haja)
    -- --- Transactions internes à 034 (034 <-> 034) ---
    (13, 14, 3, 30000, 400, 0, '2026-07-18 09:10:00'), -- 034 (Soa) -> 034 (Koto)
    (14, 13, 3, 15000, 200, 0, '2026-07-18 16:00:00'), -- 034 (Koto) -> 034 (Soa)
    -- --- Transactions internes à 038 (038 <-> 038) ---
    (15, 16, 3, 80000, 800, 0, '2026-07-19 08:45:00'), -- 038 (Bako) -> 038 (Haja)
    (16, 15, 3, 40000, 400, 0, '2026-07-19 13:30:00'), -- 038 (Haja) -> 038 (Bako)
    -- --- Transactions croisées entre 034 et 038 ---
    (13, 15, 3, 20000, 200, 0, '2026-07-20 10:05:00'), -- 034 (Soa) -> 038 (Bako)
    (16, 14, 3, 150000, 1500, 0, '2026-07-20 14:50:00');

-- 038 (Haja) -> 034 (Koto)

-- ALTER TABLE TRANSACTIONS - Ajouter colonne frais_promotion
-- sqlite3 database/mobilemoney.db
-- ALTER TABLE transactions ADD COLUMN frais_promotion NUMERIC DEFAULT 0;