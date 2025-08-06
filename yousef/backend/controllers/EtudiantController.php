<?php
/**
 * Contrôleur Etudiant
 * Gère toutes les actions liées aux étudiants
 */

require_once __DIR__ . '/../models/Etudiant.php';

class EtudiantController {
    private $etudiantModel;
    
    public function __construct() {
        $this->etudiantModel = new Etudiant();
    }
    
    /**
     * Traite l'inscription d'un étudiant
     * @return array Résultat de l'opération
     */
    public function inscrire() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Méthode non autorisée'];
        }
        
        try {
            // Récupération et nettoyage des données
            $data = [
                'nom' => trim($_POST['nom'] ?? ''),
                'prenom' => trim($_POST['prenom'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'filiere' => $_POST['filiere'] ?? '',
                'date_naissance' => $_POST['date_naissance'] ?? '',
                'etablissement' => trim($_POST['etablissement'] ?? ''),
                'moyenne' => floatval($_POST['moyenne'] ?? 0),
                'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
                'confirmation_mot_de_passe' => $_POST['confirmation_mot_de_passe'] ?? ''
            ];
            
            // Validation côté serveur
            $validation = $this->validerDonneesInscription($data);
            if (!$validation['success']) {
                return $validation;
            }
            
            // Création de l'étudiant
            $etudiantId = $this->etudiantModel->create($data);
            
            if ($etudiantId) {
                // Création de la session
                session_start();
                $_SESSION['etudiant_id'] = $etudiantId;
                $_SESSION['user_type'] = 'etudiant';
                $_SESSION['email'] = $data['email'];
                
                return [
                    'success' => true,
                    'message' => 'Inscription réussie ! Votre candidature est en cours d\'examen.',
                    'redirect' => 'dashboard.php'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'inscription. Veuillez réessayer.'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Erreur lors de l'inscription: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ];
        }
    }
    
    /**
     * Traite la connexion d'un étudiant
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
            $etudiant = $this->etudiantModel->authenticate($email, $motDePasse);
            
            if ($etudiant) {
                // Création de la session
                session_start();
                $_SESSION['etudiant_id'] = $etudiant['id'];
                $_SESSION['user_type'] = 'etudiant';
                $_SESSION['email'] = $etudiant['email'];
                $_SESSION['nom'] = $etudiant['nom'];
                $_SESSION['prenom'] = $etudiant['prenom'];
                
                // Redirection selon le statut
                switch ($etudiant['statut']) {
                    case 'accepte':
                        return [
                            'success' => true,
                            'message' => 'Connexion réussie !',
                            'redirect' => 'dashboard.php'
                        ];
                    case 'refuse':
                        return [
                            'success' => true,
                            'message' => 'Connexion réussie !',
                            'redirect' => 'refuse.php'
                        ];
                    case 'en_attente':
                        return [
                            'success' => true,
                            'message' => 'Connexion réussie !',
                            'redirect' => 'attente.php'
                        ];
                    default:
                        return [
                            'success' => true,
                            'message' => 'Connexion réussie !',
                            'redirect' => 'dashboard.php'
                        ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Erreur lors de la connexion: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ];
        }
    }
    
    /**
     * Déconnecte un étudiant
     * @return array Résultat de l'opération
     */
    public function deconnecter() {
        session_start();
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Déconnexion réussie',
            'redirect' => 'index.php'
        ];
    }
    
    /**
     * Récupère les informations d'un étudiant connecté
     * @return array|false Données de l'étudiant ou false
     */
    public function getEtudiantConnecte() {
        
        
        if (!isset($_SESSION['etudiant_id']) || $_SESSION['user_type'] !== 'etudiant') {
            return false;
        }
        
        return $this->etudiantModel->getById($_SESSION['etudiant_id']);
    }
    
    /**
     * Récupère les notifications d'un étudiant
     * @return array Liste des notifications
     */
    public function getNotifications() {
        
        
        if (!isset($_SESSION['etudiant_id']) || $_SESSION['user_type'] !== 'etudiant') {
            return [];
        }
        
        return $this->etudiantModel->getNotifications($_SESSION['etudiant_id']);
    }
    
    /**
     * Marque une notification comme lue
     * @param int $notificationId ID de la notification
     * @return array Résultat de l'opération
     */
    public function marquerNotificationLue($notificationId) {
        session_start();
        
        if (!isset($_SESSION['etudiant_id']) || $_SESSION['user_type'] !== 'etudiant') {
            return ['success' => false, 'message' => 'Non autorisé'];
        }
        
        $success = $this->etudiantModel->marquerNotificationLue($notificationId);
        
        return [
            'success' => $success,
            'message' => $success ? 'Notification marquée comme lue' : 'Erreur lors du marquage'
        ];
    }
    
    /**
     * Valide les données d'inscription
     * @param array $data Données à valider
     * @return array Résultat de la validation
     */
    private function validerDonneesInscription($data) {
        $errors = [];
        
        // Validation du nom
        if (empty($data['nom']) || strlen($data['nom']) < 2 || strlen($data['nom']) > 100) {
            $errors[] = "Le nom doit contenir entre 2 et 100 caractères";
        }
        
        // Validation du prénom
        if (empty($data['prenom']) || strlen($data['prenom']) < 2 || strlen($data['prenom']) > 100) {
            $errors[] = "Le prénom doit contenir entre 2 et 100 caractères";
        }
        
        // Validation de l'email
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email doit être valide";
        }
        
        // Vérification si l'email existe déjà
        if ($this->etudiantModel->emailExists($data['email'])) {
            $errors[] = "Cet email est déjà utilisé";
        }
        
        // Validation de la filière
        $filieresAutorisees = ['Génie Info', 'Génie Électrique', 'Technique de Management'];
        if (!in_array($data['filiere'], $filieresAutorisees)) {
            $errors[] = "Veuillez sélectionner une filière valide";
        }
        
        // Validation de la date de naissance
        if (empty($data['date_naissance'])) {
            $errors[] = "La date de naissance est obligatoire";
        } else {
            $dateNaissance = DateTime::createFromFormat('Y-m-d', $data['date_naissance']);
            $aujourdhui = new DateTime();
            $age = $aujourdhui->diff($dateNaissance)->y;
            
            if (!$dateNaissance || $age < 16 || $age > 100) {
                $errors[] = "La date de naissance doit être valide (âge entre 16 et 100 ans)";
            }
        }
        
        // Validation de l'établissement
        if (empty($data['etablissement']) || strlen($data['etablissement']) > 255) {
            $errors[] = "L'établissement est obligatoire et doit faire moins de 255 caractères";
        }
        
        // Validation de la moyenne
        if (!is_numeric($data['moyenne']) || $data['moyenne'] < 0 || $data['moyenne'] > 20) {
            $errors[] = "La moyenne doit être un nombre entre 0 et 20";
        }
        
        // Validation du mot de passe
        if (empty($data['mot_de_passe']) || strlen($data['mot_de_passe']) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
        }
        
        // Validation de la confirmation du mot de passe
        if ($data['mot_de_passe'] !== $data['confirmation_mot_de_passe']) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => implode(', ', $errors)
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * Vérifie si un étudiant est connecté
     * @return bool True si connecté
     */
    public function estConnecte() {
        session_start();
        return isset($_SESSION['etudiant_id']) && $_SESSION['user_type'] === 'etudiant';
    }
    
    /**
     * Redirige si non connecté
     * @param string $redirectUrl URL de redirection
     */
    public function redirigerSiNonConnecte($redirectUrl = 'index.php') {
        if (!$this->estConnecte()) {
            header("Location: $redirectUrl");
            exit();
        }
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
            case 'inscription':
                $resultat = $this->inscrire();
                break;
            case 'connexion':
                $resultat = $this->connecter();
                break;
            case 'deconnexion':
                $resultat = $this->deconnecter();
                break;
            case 'marquer_notification_lue':
                $notificationId = intval($_POST['notification_id'] ?? 0);
                $resultat = $this->marquerNotificationLue($notificationId);
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
    $controller = new EtudiantController();
    $controller->traiterAjax();
    exit();
}
?> 