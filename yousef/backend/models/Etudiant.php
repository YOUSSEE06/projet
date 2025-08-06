<?php
/**
 * Modèle Etudiant
 * Gère toutes les opérations liées aux étudiants
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/email.php';

class Etudiant {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Crée un nouvel étudiant
     * @param array $data Données de l'étudiant
     * @return int|false ID de l'étudiant créé ou false
     */
    public function create($data) {
        try {
            // Validation des données
            $this->validateData($data);
            
            // Insertion dans la table étudiants
            $sql = "INSERT INTO etudiants (nom, prenom, email, filiere, date_naissance, 
                    etablissement, moyenne, mot_de_passe) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['nom'],
                $data['prenom'],
                $data['email'],
                $data['filiere'],
                $data['date_naissance'],
                $data['etablissement'],
                $data['moyenne'],
                $data['mot_de_passe']
            ];
            
            $this->db->execute($sql, $params);
            $etudiantId = $this->db->lastInsertId();
            
            // Insertion dans la table authentification
            $sqlAuth = "INSERT INTO authentification (email, mot_de_passe, type_utilisateur) 
                       VALUES (?, ?, 'etudiant')";
            $this->db->execute($sqlAuth, [$data['email'], $data['mot_de_passe']]);
            
            // Envoi de l'email de bienvenue automatique
            $this->envoyerEmailBienvenue($data);
            
            return $etudiantId;
            
        } catch (Exception $e) {
            error_log("Erreur lors de la création de l'étudiant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère un étudiant par son email
     * @param string $email Email de l'étudiant
     * @return array|false Données de l'étudiant ou false
     */
    public function getByEmail($email) {
        try {
            $sql = "SELECT * FROM etudiants WHERE email = ?";
            return $this->db->queryOne($sql, [$email]);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération de l'étudiant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère un étudiant par son ID
     * @param int $id ID de l'étudiant
     * @return array|false Données de l'étudiant ou false
     */
    public function getById($id) {
        try {
            $sql = "SELECT * FROM etudiants WHERE id = ?";
            return $this->db->queryOne($sql, [$id]);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération de l'étudiant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour le statut d'un étudiant
     * @param int $id ID de l'étudiant
     * @param string $statut Nouveau statut
     * @return bool Succès de l'opération
     */
    public function updateStatut($id, $statut) {
        try {
            $sql = "UPDATE etudiants SET statut = ? WHERE id = ?";
            return $this->db->execute($sql, [$statut, $id]) > 0;
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du statut: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère tous les étudiants en attente
     * @param string $tri Champ de tri (nom, moyenne, date_inscription)
     * @param string $ordre Ordre de tri (ASC, DESC)
     * @return array Liste des étudiants en attente
     */
    public function getEnAttente($tri = 'date_inscription', $ordre = 'ASC') {
        try {
            $champsAutorises = ['nom', 'moyenne', 'date_inscription', 'filiere'];
            $ordresAutorises = ['ASC', 'DESC'];
            
            if (!in_array($tri, $champsAutorises)) {
                $tri = 'date_inscription';
            }
            if (!in_array(strtoupper($ordre), $ordresAutorises)) {
                $ordre = 'ASC';
            }
            
            $sql = "SELECT e.*, fa.priorite, fa.notes 
                    FROM etudiants e 
                    JOIN file_attente fa ON e.id = fa.id_etudiant 
                    WHERE e.statut = 'en_attente' 
                    ORDER BY e.$tri $ordre";
            
            return $this->db->query($sql);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des étudiants en attente: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les statistiques par filière
     * @return array Statistiques
     */
    public function getStatistiques() {
        try {
            $sql = "SELECT * FROM vue_statistiques";
            return $this->db->query($sql);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Vérifie si un email existe déjà
     * @param string $email Email à vérifier
     * @return bool True si l'email existe
     */
    public function emailExists($email) {
        try {
            $sql = "SELECT COUNT(*) as count FROM etudiants WHERE email = ?";
            $result = $this->db->queryOne($sql, [$email]);
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Erreur lors de la vérification de l'email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Valide les données d'un étudiant
     * @param array $data Données à valider
     * @throws Exception Si les données sont invalides
     */
    private function validateData($data) {
        $errors = [];
        
        // Validation du nom
        if (empty($data['nom']) || strlen($data['nom']) > 100) {
            $errors[] = "Le nom est obligatoire et doit faire moins de 100 caractères";
        }
        
        // Validation du prénom
        if (empty($data['prenom']) || strlen($data['prenom']) > 100) {
            $errors[] = "Le prénom est obligatoire et doit faire moins de 100 caractères";
        }
        
        // Validation de l'email
        if (empty($data['email']) || !validateEmail($data['email'])) {
            $errors[] = "L'email est obligatoire et doit être valide";
        }
        
        // Vérification si l'email existe déjà
        if ($this->emailExists($data['email'])) {
            $errors[] = "Cet email est déjà utilisé";
        }
        
        // Validation de la filière
        $filieresAutorisees = ['Génie Info', 'Génie Électrique', 'Technique de Management'];
        if (!in_array($data['filiere'], $filieresAutorisees)) {
            $errors[] = "La filière sélectionnée n'est pas valide";
        }
        
        // Validation de la date de naissance
        if (empty($data['date_naissance']) || !validateDate($data['date_naissance'])) {
            $errors[] = "La date de naissance est obligatoire et doit être valide";
        }
        
        // Validation de l'établissement
        if (empty($data['etablissement']) || strlen($data['etablissement']) > 255) {
            $errors[] = "L'établissement est obligatoire et doit faire moins de 255 caractères";
        }
        
        // Validation de la moyenne
        if (!validateMoyenne($data['moyenne'])) {
            $errors[] = "La moyenne doit être un nombre entre 0 et 20";
        }
        
        // Validation du mot de passe
        if (empty($data['mot_de_passe']) || strlen($data['mot_de_passe']) < 6) {
            $errors[] = "Le mot de passe est obligatoire et doit faire au moins 6 caractères";
        }
        
        if (!empty($errors)) {
            throw new Exception(implode(", ", $errors));
        }
    }
    
    /**
     * Authentifie un étudiant
     * @param string $email Email de l'étudiant
     * @param string $motDePasse Mot de passe en clair
     * @return array|false Données de l'étudiant ou false
     */
    public function authenticate($email, $motDePasse) {
        try {
            $sql = "SELECT * FROM authentification WHERE email = ? AND type_utilisateur = 'etudiant'";
            $auth = $this->db->queryOne($sql, [$email]);
            
            if ($auth && $auth['mot_de_passe'] === $motDePasse) {
                // Mise à jour de la dernière connexion
                $this->db->execute(
                    "UPDATE authentification SET derniere_connexion = NOW() WHERE id = ?",
                    [$auth['id']]
                );
                
                // Récupération des données complètes de l'étudiant
                return $this->getByEmail($email);
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de l'authentification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère les notifications d'un étudiant
     * @param int $etudiantId ID de l'étudiant
     * @return array Liste des notifications
     */
    public function getNotifications($etudiantId) {
        try {
            $sql = "SELECT * FROM notifications WHERE id_etudiant = ? ORDER BY date_envoi DESC";
            return $this->db->query($sql, [$etudiantId]);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des notifications: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Marque une notification comme lue
     * @param int $notificationId ID de la notification
     * @return bool Succès de l'opération
     */
    public function marquerNotificationLue($notificationId) {
        try {
            $sql = "UPDATE notifications SET lu = TRUE WHERE id = ?";
            return $this->db->execute($sql, [$notificationId]) > 0;
        } catch (Exception $e) {
            error_log("Erreur lors du marquage de la notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoie un email de bienvenue lors de l'inscription
     * @param array $data Données de l'étudiant inscrit
     */
    private function envoyerEmailBienvenue($data) {
        try {
            $emailConfig = new EmailConfig();
            $nomComplet = $data['prenom'] . ' ' . $data['nom'];
            
            // Envoi de l'email de bienvenue
            $emailConfig->envoyerEmailBienvenue(
                $data['email'], 
                $nomComplet, 
                $data['filiere']
            );
            
        } catch (Exception $e) {
            // Log l'erreur mais ne fait pas échouer l'inscription
            error_log("Erreur lors de l'envoi de l'email de bienvenue: " . $e->getMessage());
        }
    }
}
?> 