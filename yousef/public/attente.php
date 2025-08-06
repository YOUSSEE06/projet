<?php
/**
 * Page d'attente - Gestion des Stages
 * Page pour les étudiants dont la candidature est en cours d'examen
 */

require_once __DIR__ . '/../backend/controllers/EtudiantController.php';

$controller = new EtudiantController();

// Vérifier si l'étudiant est connecté
$controller->redirigerSiNonConnecte();

// Récupérer les informations de l'étudiant
$etudiant = $controller->getEtudiantConnecte();

// Vérifier que l'étudiant est en attente
if ($etudiant['statut'] !== 'en_attente') {
    switch ($etudiant['statut']) {
        case 'accepte':
            header('Location: dashboard.php');
            exit();
        case 'refuse':
            header('Location: refuse.php');
            exit();
    }
}

// Récupérer les notifications
$notifications = $controller->getNotifications();

// Calculer le temps d'attente
$dateInscription = new DateTime($etudiant['date_inscription']);
$aujourdhui = new DateTime();
$joursAttente = $aujourdhui->diff($dateInscription)->days;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>En attente - <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🎓 Gestion des Stages</a>
            <nav>
                <ul class="nav-menu">
                    <li><a href="attente.php">En attente</a></li>
                    <li><a href="notifications.php">Notifications</a></li>
                    <li><a href="#" class="btn-deconnexion">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Container principal -->
    <div class="container">
        <!-- Alertes -->
        <div id="alert-container"></div>

        <!-- En-tête de la page d'attente -->
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Votre candidature est en cours d'examen ⏳</h1>
                <p class="card-subtitle">L'équipe administrative examine votre dossier</p>
            </div>
            <div class="row">
                <div class="col-8">
                    <h2>Bonjour <?php echo htmlspecialchars($etudiant['prenom']); ?> !</h2>
                    <p>Votre inscription a été reçue le <strong><?php echo date('d/m/Y', strtotime($etudiant['date_inscription'])); ?></strong> 
                    et est actuellement en cours d'examen par l'équipe administrative.</p>
                    
                    <?php if ($joursAttente > 0): ?>
                        <p><strong>Temps d'attente : <?php echo $joursAttente; ?> jour<?php echo $joursAttente > 1 ? 's' : ''; ?></strong></p>
                    <?php endif; ?>
                </div>
                <div class="col-4 text-right">
                    <div class="badge badge-warning">En attente</div>
                </div>
            </div>
        </div>

        <!-- Informations sur le processus -->
        <div class="row">
            <div class="col-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Processus d'examen</h2>
                    </div>
                    
                    <div class="process-steps">
                        <div class="step completed">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h3>Inscription terminée</h3>
                                <p>Votre dossier a été enregistré avec succès</p>
                                <small><?php echo date('d/m/Y H:i', strtotime($etudiant['date_inscription'])); ?></small>
                            </div>
                        </div>
                        
                        <div class="step active">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h3>Examen en cours</h3>
                                <p>L'équipe administrative examine votre candidature</p>
                                <small>En cours...</small>
                            </div>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h3>Décision</h3>
                                <p>Vous recevrez une notification de la décision</p>
                                <small>En attente</small>
                            </div>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h3>Finalisation</h3>
                                <p>Si accepté, vous recevrez les détails du stage</p>
                                <small>En attente</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <strong>Information :</strong> Le délai d'examen varie généralement entre 3 et 7 jours ouvrables. 
                        Vous recevrez une notification par email dès qu'une décision sera prise.
                    </div>
                </div>

                <!-- Critères d'évaluation -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h2 class="card-title">Critères d'évaluation</h2>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h3>Critères académiques</h3>
                            <ul>
                                <li>Moyenne générale (<?php echo number_format($etudiant['moyenne'], 2); ?>/20)</li>
                                <li>Régularité dans les études</li>
                                <li>Progression académique</li>
                                <li>Résultats par matière</li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <h3>Critères de profil</h3>
                            <ul>
                                <li>Filière demandée (<?php echo htmlspecialchars($etudiant['filiere']); ?>)</li>
                                <li>Établissement d'origine</li>
                                <li>Motivation et projet professionnel</li>
                                <li>Disponibilité pour le stage</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations personnelles -->
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Vos informations</h3>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom complet</label>
                        <p class="form-control-static"><?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <p class="form-control-static"><?php echo htmlspecialchars($etudiant['email']); ?></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Filière</label>
                        <p class="form-control-static"><?php echo htmlspecialchars($etudiant['filiere']); ?></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Établissement</label>
                        <p class="form-control-static"><?php echo htmlspecialchars($etudiant['etablissement']); ?></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Moyenne</label>
                        <p class="form-control-static"><?php echo number_format($etudiant['moyenne'], 2); ?>/20</p>
                    </div>
                </div>

                <!-- Notifications récentes -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Notifications récentes</h3>
                    </div>
                    <?php if (!empty($notifications)): ?>
                        <div class="notification-list">
                            <?php foreach (array_slice($notifications, 0, 3) as $notification): ?>
                                <div class="notification-item <?php echo $notification['lu'] ? 'lu' : ''; ?>" data-id="<?php echo $notification['id']; ?>">
                                    <div class="notification-content">
                                        <p><?php echo htmlspecialchars($notification['message']); ?></p>
                                        <small><?php echo date('d/m/Y H:i', strtotime($notification['date_envoi'])); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($notifications) > 3): ?>
                            <div class="text-center mt-2">
                                <a href="notifications.php" class="btn btn-outline btn-sm">Voir toutes</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">Aucune notification</p>
                    <?php endif; ?>
                </div>

                <!-- Actions disponibles -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Actions disponibles</h3>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="notifications.php" class="btn btn-outline">
                            📬 Voir les notifications
                        </a>
                        <a href="profil.php" class="btn btn-outline">
                            👤 Modifier mon profil
                        </a>
                        <a href="index.php" class="btn btn-outline">
                            🏠 Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Questions fréquentes</h2>
            </div>
            <div class="row">
                <div class="col-6">
                    <h3>Combien de temps dure l'examen ?</h3>
                    <p>Le délai d'examen varie généralement entre 3 et 7 jours ouvrables. Nous vous tiendrons informé de l'avancement.</p>
                    
                    <h3>Puis-je modifier mes informations ?</h3>
                    <p>Oui, vous pouvez modifier votre profil tant que votre candidature n'a pas été traitée.</p>
                </div>
                <div class="col-6">
                    <h3>Comment serai-je notifié ?</h3>
                    <p>Vous recevrez une notification par email et dans votre espace personnel dès qu'une décision sera prise.</p>
                    
                    <h3>Que faire si ma candidature est refusée ?</h3>
                    <p>Vous pourrez postuler à nouveau lors de la prochaine session de stages.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-4 mb-4">
        <p>&copy; 2024 Gestion des Stages. Tous droits réservés.</p>
    </footer>

    <style>
        .process-steps {
            position: relative;
        }
        
        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 20px;
            top: 50px;
            width: 2px;
            height: 30px;
            background: #e1e5e9;
        }
        
        .step.completed:not(:last-child)::after {
            background: #27ae60;
        }
        
        .step.active:not(:last-child)::after {
            background: #f39c12;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e1e5e9;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .step.completed .step-number {
            background: #27ae60;
            color: white;
        }
        
        .step.active .step-number {
            background: #f39c12;
            color: white;
            animation: pulse 2s infinite;
        }
        
        .step-content h3 {
            margin: 0 0 0.5rem 0;
            color: #333;
        }
        
        .step-content p {
            margin: 0 0 0.25rem 0;
            color: #666;
        }
        
        .step-content small {
            color: #999;
            font-size: 0.85rem;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 0.75rem;
            border-bottom: 1px solid #e1e5e9;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .notification-item:hover {
            background: rgba(102, 126, 234, 0.05);
        }
        
        .notification-item.lu {
            opacity: 0.7;
        }
        
        .notification-content p {
            margin: 0 0 0.25rem 0;
            font-size: 0.9rem;
        }
        
        .notification-content small {
            color: #999;
            font-size: 0.8rem;
        }
    </style>

    <script src="js/app.js"></script>
</body>
</html> 