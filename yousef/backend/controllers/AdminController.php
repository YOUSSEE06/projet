<?php
/**
 * Contrôleur Administrateur
 * Gère toutes les actions liées aux administrateurs
 */

require_once __DIR__ . '/../models/Administrateur.php';
require_once __DIR__ . '/../models/Etudiant.php';

class AdminController {
    private $adminModel;
    private $etudiantModel;
    
    public function __construct() {
        $this->adminModel = new Administrateur();
        $this->etudiantModel = new Etudiant();
    }
    
    /**
     * Traite la connexion d'un administrateur
     * @return array Résultat de l'opération
     */
    public function connecter() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée'];
        }
        
        try {
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';
            
            // Validation des données
            if (empty($email) || empty($motDePasse)) {
                return [
                    'success' => false,
                    'message' => 'Email et mot de passe requis'
                ];
            }
            
            // Authentification
            $admin = $this->adminModel->authenticate($email, $motDePasse);
            
            if ($admin) {
                // Création de la session
                session_start();
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['email'] = $admin['email'];
                $_SESSION['nom'] = $admin['nom'];
                $_SESSION['prenom'] = $admin['prenom'];
                $_SESSION['role'] = $admin['role'];
                
                return [
                    'success' => true,
                    'message' => 'Connexion réussie !',
                    'redirect' => 'admin/dashboard.php'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Erreur lors de la connexion admin: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ];
        }
    }
    
    /**
     * Déconnecte un administrateur
     * @return array Résultat de l'opération
     */
    public function deconnecter() {
        session_start();
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Déconnexion réussie',
            'redirect' => '../index.php'
        ];
    }
    
    /**
     * Accepte un étudiant pour un stage
     * @return array Résultat de l'opération
     */
    public function accepterEtudiant() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée'];
        }
        
        try {
            $etudiantId = intval($_POST['etudiant_id'] ?? 0);
            $typeStage = $_POST['type_stage'] ?? '';
            $adminResponsable = $_SESSION['nom'] . ' ' . $_SESSION['prenom'];
            
            // Validation des données
            if ($etudiantId <= 0) {
                return ['success' => false, 'message' => 'ID étudiant invalide'];
            }
            
            $typesStageAutorises = ['PFE', 'Stage d\'été', 'Stage d\'hiver'];
            if (!in_array($typeStage, $typesStageAutorises)) {
                return ['success' => false, 'message' => 'Type de stage invalide'];
            }
            
            // Acceptation de l'étudiant
            $success = $this->adminModel->accepterEtudiant($etudiantId, $typeStage, $adminResponsable);
            
            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Étudiant accepté avec succès'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'acceptation de l\'étudiant'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Erreur lors de l'acceptation: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ];
        }
    }
    
    /**
     * Refuse un étudiant
     * @return array Résultat de l'opération
     */
    public function refuserEtudiant() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée'];
        }
        
        try {
            $etudiantId = intval($_POST['etudiant_id'] ?? 0);
            $raisonRefus = trim($_POST['raison_refus'] ?? '');
            $adminResponsable = $_SESSION['nom'] . ' ' . $_SESSION['prenom'];
            
            // Validation des données
            if ($etudiantId <= 0) {
                return ['success' => false, 'message' => 'ID étudiant invalide'];
            }
            
            if (empty($raisonRefus) || strlen($raisonRefus) > 500) {
                return ['success' => false, 'message' => 'La raison du refus est obligatoire et doit faire moins de 500 caractères'];
            }
            
            // Refus de l'étudiant
            $success = $this->adminModel->refuserEtudiant($etudiantId, $raisonRefus, $adminResponsable);
            
            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Étudiant refusé avec succès'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors du refus de l\'étudiant'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Erreur lors du refus: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ];
        }
    }
    
    /**
     * Récupère les étudiants en attente
     * @return array Liste des étudiants en attente
     */
    public function getEtudiantsEnAttente() {
        try {
            $tri = $_GET['tri'] ?? 'date_inscription';
            $ordre = $_GET['ordre'] ?? 'ASC';
            
            return $this->etudiantModel->getEnAttente($tri, $ordre);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des étudiants en attente: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les statistiques
     * @return array Statistiques
     */
    public function getStatistiques() {
        try {
            return [
                'globales' => $this->adminModel->getStatistiquesGlobales(),
                'par_filiere' => $this->etudiantModel->getStatistiques()
            ];
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les étudiants acceptés
     * @return array Liste des étudiants acceptés
     */
    public function getEtudiantsAcceptes() {
        try {
            return $this->adminModel->getEtudiantsAcceptes();
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
            return $this->adminModel->getEtudiantsRefuses();
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des étudiants refusés: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Met à jour les informations d'un stage
     * @return array Résultat de l'opération
     */
    public function updateStage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée'];
        }
        
        try {
            $stageId = intval($_POST['stage_id'] ?? 0);
            $data = [
                'date_debut_stage' => $_POST['date_debut_stage'] ?? '',
                'date_fin_stage' => $_POST['date_fin_stage'] ?? '',
                'encadrant' => trim($_POST['encadrant'] ?? ''),
                'entreprise' => trim($_POST['entreprise'] ?? '')
            ];
            
            // Validation des données
            if ($stageId <= 0) {
                return ['success' => false, 'message' => 'ID stage invalide'];
            }
            
            if (empty($data['date_debut_stage']) || empty($data['date_fin_stage'])) {
                return ['success' => false, 'message' => 'Les dates de début et fin sont obligatoires'];
            }
            
            // Mise à jour du stage
            $success = $this->adminModel->updateStageAccepte($stageId, $data);
            
            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Stage mis à jour avec succès'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour du stage'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du stage: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ];
        }
    }
    
    /**
     * Recherche des étudiants
     * @return array Liste des étudiants correspondants
     */
    public function rechercherEtudiants() {
        try {
            $criteria = [
                'nom' => $_GET['nom'] ?? '',
                'filiere' => $_GET['filiere'] ?? '',
                'statut' => $_GET['statut'] ?? '',
                'moyenne_min' => floatval($_GET['moyenne_min'] ?? 0),
                'moyenne_max' => floatval($_GET['moyenne_max'] ?? 20)
            ];
            
            return $this->adminModel->rechercherEtudiants($criteria);
        } catch (Exception $e) {
            error_log("Erreur lors de la recherche: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Exporte les données en CSV
     * @return void
     */
    public function exporterCSV() {
        try {
            $statut = $_GET['statut'] ?? null;
            $csv = $this->adminModel->exporterCSV($statut);
            
            $filename = 'etudiants_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            echo $csv;
            exit();
            
        } catch (Exception $e) {
            error_log("Erreur lors de l'export CSV: " . $e->getMessage());
            http_response_code(500);
            echo "Erreur lors de l'export";
        }
    }
    
    /**
     * Vérifie si un administrateur est connecté
     * @return bool True si connecté
     */
    public function estConnecte() {
        session_start();
        return isset($_SESSION['admin_id']) && $_SESSION['user_type'] === 'admin';
    }
    
    /**
     * Redirige si non connecté
     * @param string $redirectUrl URL de redirection
     */
    public function redirigerSiNonConnecte($redirectUrl = '../index.php') {
        if (!$this->estConnecte()) {
            header("Location: $redirectUrl");
            exit();
        }
    }
    
    /**
     * Vérifie les permissions d'administrateur
     * @param string $role Role requis
     * @return bool True si autorisé
     */
    public function aPermission($role = 'admin') {
        session_start();
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Traite une requête AJAX
     * @return void
     */
    public function traiterAjax() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'connexion':
                $resultat = $this->connecter();
                break;
            case 'deconnexion':
                $resultat = $this->deconnecter();
                break;
            case 'accepter_etudiant':
                $resultat = $this->accepterEtudiant();
                break;
            case 'refuser_etudiant':
                $resultat = $this->refuserEtudiant();
                break;
            case 'update_stage':
                $resultat = $this->updateStage();
                break;
            default:
                $resultat = ['success' => false, 'message' => 'Action non reconnue'];
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultat);
    }
}

// Traitement des requêtes AJAX
if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    $controller = new AdminController();
    $controller->traiterAjax();
    exit();
}
?> 