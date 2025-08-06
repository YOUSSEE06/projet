-- =====================================================
-- SCHÉMA DE BASE DE DONNÉES - GESTION DES STAGES
-- =====================================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS gestion_stages CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_stages;

-- =====================================================
-- TABLE: etudiants
-- Stocke les informations principales des étudiants
-- =====================================================
CREATE TABLE etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    filiere ENUM('Génie Info', 'Génie Électrique', 'Technique de Management') NOT NULL,
    date_naissance DATE NOT NULL,
    etablissement VARCHAR(255) NOT NULL,
    moyenne DECIMAL(4,2) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_statut (statut),
    INDEX idx_filiere (filiere),
    INDEX idx_moyenne (moyenne)
);

-- =====================================================
-- TABLE: authentification
-- Gestion des identifiants de connexion
-- =====================================================
CREATE TABLE authentification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    type_utilisateur ENUM('etudiant', 'admin') DEFAULT 'etudiant',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_type (type_utilisateur)
);

-- =====================================================
-- TABLE: file_attente
-- Suivi des inscriptions en attente de traitement
-- =====================================================
CREATE TABLE file_attente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    priorite INT DEFAULT 0,
    notes TEXT,
    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id) ON DELETE CASCADE,
    INDEX idx_date_inscription (date_inscription),
    INDEX idx_priorite (priorite)
);

-- =====================================================
-- TABLE: stages_acceptes
-- Étudiants dont le stage a été accepté
-- =====================================================
CREATE TABLE stages_acceptes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    filiere ENUM('Génie Info', 'Génie Électrique', 'Technique de Management') NOT NULL,
    type_stage ENUM('PFE', 'Stage d''été', 'Stage d''hiver') NOT NULL,
    date_acceptation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_debut_stage DATE,
    date_fin_stage DATE,
    encadrant VARCHAR(255),
    entreprise VARCHAR(255),
    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id) ON DELETE CASCADE,
    INDEX idx_filiere (filiere),
    INDEX idx_type_stage (type_stage),
    INDEX idx_date_acceptation (date_acceptation)
);

-- =====================================================
-- TABLE: etudiants_refuses
-- Étudiants dont la candidature a été refusée
-- =====================================================
CREATE TABLE etudiants_refuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    filiere ENUM('Génie Info', 'Génie Électrique', 'Technique de Management') NOT NULL,
    raison_refus TEXT NOT NULL,
    date_refus TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    admin_responsable VARCHAR(100),
    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id) ON DELETE CASCADE,
    INDEX idx_date_refus (date_refus),
    INDEX idx_filiere (filiere)
);

-- =====================================================
-- TABLE: notifications
-- Historique des notifications envoyées
-- =====================================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    type_notification ENUM('acceptation', 'refus', 'en_attente') NOT NULL,
    message TEXT NOT NULL,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id) ON DELETE CASCADE,
    INDEX idx_type_notification (type_notification),
    INDEX idx_date_envoi (date_envoi),
    INDEX idx_lu (lu)
);

-- =====================================================
-- TABLE: administrateurs
-- Gestion des comptes administrateurs
-- =====================================================
CREATE TABLE administrateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion TIMESTAMP NULL,
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- =====================================================
-- INSERTION DES DONNÉES INITIALES
-- =====================================================

-- Insertion du compte administrateur par défaut
-- Mot de passe: admin123 (en clair)
INSERT INTO administrateurs (nom, prenom, email, mot_de_passe, role) VALUES 
('Administrateur', 'Principal', 'admin@stages.com', 'admin123', 'super_admin');

-- Insertion dans la table authentification pour l'admin
INSERT INTO authentification (email, mot_de_passe, type_utilisateur) VALUES 
('admin@stages.com', 'admin123', 'admin');

-- =====================================================
-- VUES UTILES POUR L'APPLICATION
-- =====================================================

-- Vue pour les étudiants en attente avec leurs informations
CREATE VIEW vue_etudiants_attente AS
SELECT 
    e.id,
    e.nom,
    e.prenom,
    e.email,
    e.filiere,
    e.date_naissance,
    e.etablissement,
    e.moyenne,
    e.date_inscription,
    fa.priorite,
    fa.notes
FROM etudiants e
JOIN file_attente fa ON e.id = fa.id_etudiant
WHERE e.statut = 'en_attente'
ORDER BY fa.priorite DESC, e.date_inscription ASC;

-- Vue pour les statistiques
CREATE VIEW vue_statistiques AS
SELECT 
    filiere,
    COUNT(*) as total_inscriptions,
    SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
    SUM(CASE WHEN statut = 'accepte' THEN 1 ELSE 0 END) as acceptes,
    SUM(CASE WHEN statut = 'refuse' THEN 1 ELSE 0 END) as refuses,
    AVG(moyenne) as moyenne_generale
FROM etudiants
GROUP BY filiere;

-- =====================================================
-- TRIGGERS POUR LA COHÉRENCE DES DONNÉES
-- =====================================================

-- Trigger pour ajouter automatiquement un étudiant à la file d'attente
DELIMITER //
CREATE TRIGGER ajouter_file_attente
AFTER INSERT ON etudiants
FOR EACH ROW
BEGIN
    INSERT INTO file_attente (id_etudiant) VALUES (NEW.id);
END//
DELIMITER ;

-- Trigger pour supprimer de la file d'attente quand accepté/refusé
DELIMITER //
CREATE TRIGGER supprimer_file_attente
AFTER UPDATE ON etudiants
FOR EACH ROW
BEGIN
    IF NEW.statut != 'en_attente' AND OLD.statut = 'en_attente' THEN
        DELETE FROM file_attente WHERE id_etudiant = NEW.id;
    END IF;
END//
DELIMITER ;

-- =====================================================
-- INDEX SUPPLÉMENTAIRES POUR LES PERFORMANCES
-- =====================================================

-- Index composites pour les requêtes fréquentes
CREATE INDEX idx_etudiant_statut_date ON etudiants(statut, date_inscription);
CREATE INDEX idx_etudiant_filiere_moyenne ON etudiants(filiere, moyenne);
CREATE INDEX idx_notification_etudiant_lu ON notifications(id_etudiant, lu);

-- =====================================================
-- COMMENTAIRES FINAUX
-- =====================================================

/*
Ce schéma de base de données est conçu pour une application de gestion des stages étudiants.
Il inclut :
- Gestion complète des étudiants et de leurs candidatures
- Système d'authentification sécurisé
- Suivi des statuts (en attente, accepté, refusé)
- Historique des décisions administratives
- Système de notifications
- Vues pour faciliter les requêtes fréquentes
- Triggers pour maintenir la cohérence des données

Pour utiliser ce schéma :
1. Exécuter ce script dans MySQL
2. Configurer les paramètres de connexion dans config/database.php
3. L'application sera prête à utiliser
*/ 