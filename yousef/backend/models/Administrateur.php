<?php
/**
 * Modèle Administrateur
 * Gère toutes les opérations liées aux administrateurs et à la gestion des stages
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/email.php';

class Administrateur {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Authentifie un administrateur
     * @param string $email Email de l'administrateur
     * @param string $motDePasse Mot de passe en clair
     * @return array|false Données de l'administrateur ou false
     */
    public function authenticate($email, $motDePasse) {
        try {
            $sql = "SELECT * FROM authentification WHERE email = ? AND type_utilisateur = 'admin'";
            $auth = $this->db->queryOne($sql, [$email]);
            
            if ($auth && $auth['mot_de_passe'] === $motDePasse) {
                // Mise à jour de la dernière connexion
                $this->db->execute(
                    "UPDATE authentification SET derniere_connexion = NOW() WHERE id = ?",
                    [$auth['id']]
                );
                
                // Récupération des données complètes de l'administrateur
                $sqlAdmin = "SELECT * FROM administrateurs WHERE email = ?";
                return $this->db->queryOne($sqlAdmin, [$email]);
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de l'authentification admin: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Accepte un étudiant pour un stage
     * @param int $etudiantId ID de l'étudiant
     * @param string $typeStage Type de stage
     * @param string $adminResponsable Nom de l'admin responsable
     * @return bool Succès de l'opération
     */
    public function accepterEtudiant($etudiantId, $typeStage, $adminResponsable) {
        try {
            $this->db->beginTransaction();
            
            // Récupération des données de l'étudiant
            $sql = "SELECT * FROM etudiants WHERE id = ?";
            $etudiant = $this->db->queryOne($sql, [$etudiantId]);
            
            if (!$etudiant) {
                throw new Exception("Étudiant non trouvé");
            }
            
            // Mise à jour du statut de l'étudiant
            $sqlUpdate = "UPDATE etudiants SET statut = 'accepte' WHERE id = ?";
            $this->db->execute($sqlUpdate, [$etudiantId]);
            
            // Insertion dans la table stages_acceptes
            $sqlAccepte = "INSERT INTO stages_acceptes (id_etudiant, nom, prenom, filiere, type_stage) 
                          VALUES (?, ?, ?, ?, ?)";
            $this->db->execute($sqlAccepte, [
                $etudiantId,
                $etudiant['nom'],
                $etudiant['prenom'],
                $etudiant['filiere'],
                $typeStage
            ]);
            
            // Création de la notification
            $message = "Félicitations ! Votre candidature pour le stage a été acceptée. 
                       Type de stage : $typeStage. Vous recevrez bientôt plus de détails.";
            $this->creerNotification($etudiantId, 'acceptation', $message);
            
            // Envoi de l'email de notification (Gmail SMTP)
            $emailConfig = new EmailConfig();
            $nomComplet = $etudiant['prenom'] . ' ' . $etudiant['nom'];
            $emailConfig->envoyerNotificationAcceptation($etudiant['email'], $nomComplet, $typeStage);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Erreur lors de l'acceptation de l'étudiant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Refuse un étudiant
     * @param int $etudiantId ID de l'étudiant
     * @param string $raisonRefus Raison du refus
     * @param string $adminResponsable Nom de l'admin responsable
     * @return bool Succès de l'opération
     */
    public function refuserEtudiant($etudiantId, $raisonRefus, $adminResponsable) {
        try {
            $this->db->beginTransaction();
            
            // Récupération des données de l'étudiant
            $sql = "SELECT * FROM etudiants WHERE id = ?";
            $etudiant = $this->db->queryOne($sql, [$etudiantId]);
            
            if (!$etudiant) {
                throw new Exception("Étudiant non trouvé");
            }
            
            // Mise à jour du statut de l'étudiant
            $sqlUpdate = "UPDATE etudiants SET statut = 'refuse' WHERE id = ?";
            $this->db->execute($sqlUpdate, [$etudiantId]);
            
            // Insertion dans la table etudiants_refuses
            $sqlRefuse = "INSERT INTO etudiants_refuses (id_etudiant, nom, prenom, filiere, raison_refus, admin_responsable) 
                         VALUES (?, ?, ?, ?, ?, ?)";
            $this->db->execute($sqlRefuse, [
                $etudiantId,
                $etudiant['nom'],
                $etudiant['prenom'],
                $etudiant['filiere'],
                $raisonRefus,
                $adminResponsable
            ]);
            
            // Création de la notification
            $message = "Nous regrettons de vous informer que votre candidature pour le stage a été refusée. 
                       Raison : $raisonRefus. N'hésitez pas à postuler à nouveau lors de la prochaine session.";
            $this->creerNotification($etudiantId, 'refus', $message);
            
            // Envoi de l'email de notification (Gmail SMTP)
            $emailConfig = new EmailConfig();
            $nomComplet = $etudiant['prenom'] . ' ' . $etudiant['nom'];
            $emailConfig->envoyerNotificationRefus($etudiant['email'], $nomComplet, 'Stage', $raisonRefus);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Erreur lors du refus de l'étudiant: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère les statistiques globales
     * @return array Statistiques
     */
    public function getStatistiquesGlobales() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_etudiants,
                        SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                        SUM(CASE WHEN statut = 'accepte' THEN 1 ELSE 0 END) as acceptes,
                        SUM(CASE WHEN statut = 'refuse' THEN 1 ELSE 0 END) as refuses,
                        AVG(moyenne) as moyenne_generale
                    FROM etudiants";
            
            return $this->db->queryOne($sql);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des statistiques globales: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les étudiants acceptés
     * @return array Liste des étudiants acceptés
     */
    public function getEtudiantsAcceptes() {
        try {
            $sql = "SELECT sa.*, e.email, e.etablissement, e.moyenne 
                    FROM stages_acceptes sa 
                    JOIN etudiants e ON sa.id_etudiant = e.id 
                    ORDER BY sa.date_acceptation DESC";
            
            return $this->db->query($sql);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des étudiants acceptés: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les étudiants refusés
     * @return array Liste des étudiants refusés
     */
    public function getEtudiantsRefuses() {
        try {
            $sql = "SELECT er.*, e.email, e.etablissement, e.moyenne 
                    FROM etudiants_refuses er 
                    JOIN etudiants e ON er.id_etudiant = e.id 
                    ORDER BY er.date_refus DESC";
            
            return $this->db->query($sql);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des étudiants refusés: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Met à jour les informations d'un stage accepté
     * @param int $stageId ID du stage
     * @param array $data Nouvelles données
     * @return bool Succès de l'opération
     */
    public function updateStageAccepte($stageId, $data) {
        try {
            $sql = "UPDATE stages_acceptes SET 
                        date_debut_stage = ?, 
                        date_fin_stage = ?, 
                        encadrant = ?, 
                        entreprise = ? 
                    WHERE id = ?";
            
            return $this->db->execute($sql, [
                $data['date_debut_stage'],
                $data['date_fin_stage'],
                $data['encadrant'],
                $data['entreprise'],
                $stageId
            ]) > 0;
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du stage: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crée une notification pour un étudiant
     * @param int $etudiantId ID de l'étudiant
     * @param string $type Type de notification
     * @param string $message Message de la notification
     * @return bool Succès de l'opération
     */
    private function creerNotification($etudiantId, $type, $message) {
        try {
            $sql = "INSERT INTO notifications (id_etudiant, type_notification, message) 
                    VALUES (?, ?, ?)";
            
            return $this->db->execute($sql, [$etudiantId, $type, $message]) > 0;
        } catch (Exception $e) {
            error_log("Erreur lors de la création de la notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère les notifications non lues
     * @return array Liste des notifications non lues
     */
    public function getNotificationsNonLues() {
        try {
            $sql = "SELECT n.*, e.nom, e.prenom, e.email 
                    FROM notifications n 
                    JOIN etudiants e ON n.id_etudiant = e.id 
                    WHERE n.lu = FALSE 
                    ORDER BY n.date_envoi DESC";
            
            return $this->db->query($sql);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des notifications: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Recherche des étudiants selon différents critères
     * @param array $criteria Critères de recherche
     * @return array Liste des étudiants correspondants
     */
    public function rechercherEtudiants($criteria) {
        try {
            $sql = "SELECT * FROM etudiants WHERE 1=1";
            $params = [];
            
            if (!empty($criteria['nom'])) {
                $sql .= " AND nom LIKE ?";
                $params[] = '%' . $criteria['nom'] . '%';
            }
            
            if (!empty($criteria['filiere'])) {
                $sql .= " AND filiere = ?";
                $params[] = $criteria['filiere'];
            }
            
            if (!empty($criteria['statut'])) {
                $sql .= " AND statut = ?";
                $params[] = $criteria['statut'];
            }
            
            if (!empty($criteria['moyenne_min'])) {
                $sql .= " AND moyenne >= ?";
                $params[] = $criteria['moyenne_min'];
            }
            
            if (!empty($criteria['moyenne_max'])) {
                $sql .= " AND moyenne <= ?";
                $params[] = $criteria['moyenne_max'];
            }
            
            $sql .= " ORDER BY date_inscription DESC";
            
            return $this->db->query($sql, $params);
        } catch (Exception $e) {
            error_log("Erreur lors de la recherche d'étudiants: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Exporte les données des étudiants en CSV
     * @param string $statut Statut des étudiants à exporter (optionnel)
     * @return string Contenu CSV
     */
    public function exporterCSV($statut = null) {
        try {
            $sql = "SELECT nom, prenom, email, filiere, date_naissance, etablissement, moyenne, statut, date_inscription 
                    FROM etudiants";
            $params = [];
            
            if ($statut) {
                $sql .= " WHERE statut = ?";
                $params[] = $statut;
            }
            
            $sql .= " ORDER BY date_inscription DESC";
            
            $etudiants = $this->db->query($sql, $params);
            
            $csv = "Nom,Prénom,Email,Filière,Date de naissance,Établissement,Moyenne,Statut,Date d'inscription\n";
            
            foreach ($etudiants as $etudiant) {
                $csv .= sprintf(
                    '"%s","%s","%s","%s","%s","%s",%.2f,"%s","%s"' . "\n",
                    $etudiant['nom'],
                    $etudiant['prenom'],
                    $etudiant['email'],
                    $etudiant['filiere'],
                    $etudiant['date_naissance'],
                    $etudiant['etablissement'],
                    $etudiant['moyenne'],
                    $etudiant['statut'],
                    $etudiant['date_inscription']
                );
            }
            
            return $csv;
        } catch (Exception $e) {
            error_log("Erreur lors de l'export CSV: " . $e->getMessage());
            return '';
        }
    }
}
?> 