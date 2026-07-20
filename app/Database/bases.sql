CREATE DATABASE regime_app 
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;;
USE regime_app;

-- USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    genre ENUM('homme', 'femme'),
    is_gold BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM('utilisateur', 'admin') DEFAULT 'utilisateur'
);

-- SANTE
CREATE TABLE user_health (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    taille FLOAT,
    poids FLOAT,
    imc FLOAT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- OBJECTIFS
CREATE TABLE objectifs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100)
);

-- USER OBJECTIFS
CREATE TABLE user_objectif (
    user_id INT,
    objectif_id INT,
    PRIMARY KEY(user_id, objectif_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (objectif_id) REFERENCES objectifs(id)
);

-- REGIMES
CREATE TABLE regimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT,
    prix DECIMAL(10,2),
    duree INT,
    variation_poids FLOAT,
    pourcentage_viande INT,
    pourcentage_poisson INT,
    pourcentage_volaille INT
);

-- ACTIVITES
CREATE TABLE activites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT,
    calories INT,
    duree INT
);

-- SUGGESTIONS
CREATE TABLE suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    regime_id INT,
    activite_id INT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (regime_id) REFERENCES regimes(id),
    FOREIGN KEY (activite_id) REFERENCES activites(id)
);

-- WALLET
CREATE TABLE wallet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    solde DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- CODES
CREATE TABLE codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50),
    montant DECIMAL(10,2),
    is_used BOOLEAN DEFAULT FALSE
);

-- TRANSACTIONS
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    code_id INT,
    montant DECIMAL(10,2),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (code_id) REFERENCES codes(id)
);


-- ACHATS
CREATE TABLE achats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    suggestion_id INT,
    regime_id INT,
    activite_id INT,
    prix_total DECIMAL(10,2),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (suggestion_id) REFERENCES suggestions(id),
    FOREIGN KEY (regime_id) REFERENCES regimes(id),
    FOREIGN KEY (activite_id) REFERENCES activites(id)
);


CREATE TABLE passage_gold (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    statut ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE TABLE prix_passage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prix DECIMAL(10,2) NOT NULL
);