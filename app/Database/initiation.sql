INSERT INTO users (nom, email, password, genre, is_gold, role) VALUES
('Admin ', 'admin@itu.mg', 'admin123', 'homme', FALSE, 'admin'),
('Jean Dupont', 'jean@gmail.com', 'password123', 'homme', TRUE, 'utilisateur'),
('Marie Rakoto', 'marie@gmail.com', 'password123', 'femme', FALSE, 'utilisateur'),
('Lucie Chen', 'lucie@yahoo.fr', 'password123', 'femme', FALSE, 'utilisateur'),
('Marc Smith', 'marc@outlook.com', 'password123', 'homme', FALSE, 'utilisateur');

INSERT INTO objectifs (libelle) VALUES 
('Augmenter son poids'),
('Réduire son poids'),
('Atteindre son IMC idéal');


INSERT INTO user_health (user_id, taille, poids, imc) VALUES
(2, 175, 80.0, 26.1),
(3, 160, 55.0, 21.5),
(4, 165, 75.0, 27.5),
(5, 180, 95.0, 29.3);


INSERT INTO codes (code, montant, is_used) VALUES
('CODE001', 5000, FALSE),
('CODE002', 10000, FALSE),
('CODE003', 15000, FALSE),
('CODE004', 20000, FALSE),
('CODE005', 25000, FALSE),
('CODE006', 30000, FALSE),
('CODE007', 35000, FALSE),
('CODE008', 40000, FALSE),
('CODE009', 45000, FALSE),
('CODE010', 50000, FALSE),
('CODE011', 55000, FALSE),
('CODE012', 60000, FALSE),
('CODE013', 65000, FALSE),
('CODE014', 70000, FALSE),
('CODE015', 75000, FALSE);


INSERT INTO regimes (nom, description, prix, duree, variation_poids, pourcentage_viande, pourcentage_poisson, pourcentage_volaille) VALUES
('Regime Minceur', 'Perte de poids rapide', 20000, 7, -3.5, 30, 40, 30),
('Regime Equilibre', 'Equilibre alimentaire quotidien', 15000, 14, -1.5, 25, 25, 50),
('Regime Sportif', 'Adapté aux sportifs', 30000, 10, 2.0, 40, 30, 30),
('Regime Detox', 'Nettoyage du corps', 18000, 5, -2.0, 10, 60, 30),
('Regime Prise de masse', 'Gain de poids musculaire', 35000, 21, 4.0, 50, 20, 30);


INSERT INTO activites (nom, description, calories, duree) VALUES
('Course à pied', 'Endurance et cardio', 500, 45),
('Natation', 'Renforcement complet', 400, 60),
('Musculation', 'Prise de masse musculaire', 300, 90),
('Yoga', 'Souplesse et récupération', 150, 60),
('Cyclisme', 'Brûle-graisse intensif', 600, 60);

INSERT INTO wallet (user_id, solde) VALUES

(2, 40000),
(3, 15000),
(4, 60000),
(5, 10000);


INSERT INTO prix_passage (prix) VALUES (5000);