cd /home/davida/Documents/Exam-Rojo/Operateur && sqlite3 writable/mobilemoney.db <<SQL
-- Add operator 2 with 037 prefix
INSERT INTO clients (telephone, nom, role, date_creation, id_prefixe) 
VALUES ('0370000000', 'Opérateur 2', 'operateur', CURRENT_TIMESTAMP, 2);

-- Add account for operator 2
WITH new_client AS (SELECT id FROM clients WHERE telephone = '0370000000')
INSERT INTO comptes (id_client, solde, date_creation)
SELECT id, 0, CURRENT_TIMESTAMP FROM new_client;

-- Verify all operators
SELECT * FROM clients WHERE role = 'operateur';
SQL