<?php
/**
 * Page de refus - Gestion des Stages
 * Page pour les étudiants dont la candidature a été refusée
 */

require_once __DIR__ . '/../backend/controllers/EtudiantController.php';

$controller = new EtudiantController();

// Vérifier si l'étudiant est connecté
$controller->redirigerSiNonConnecte();

// Récupérer les informations de l'étudiant
$etudiant = $controller->getEtudiantConnecte();

// Vérifier que l'étudiant est refusé
if ($etudiant['statut'] !== 'refuse') {
    switch ($etudiant['statut']) {
        case 'accepte':
            header('Location: dashboard.php');
            exit();
        case 'en_attente':
            header('Location: attente.php');
            exit();
    }
}

// Récupérer les notifications
$notifications = $controller->getNotifications();

// Récupérer les détails du refus
require_once __DIR__ . '/../config/database.php';
$db = getDB();
$sql = "SELECT * FROM etudiants_refuses WHERE id_etudiant = ?";
$refus = $db->queryOne($sql, [$etudiant['id']]);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidature refusée - <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🎓 Gestion des Stages</a>
            <nav>
                <ul class="nav-menu">
                    <li><a href="refuse.php">Candidature refusée</a></li>
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

        <!-- En-tête de la page de refus -->
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Candidature non retenue 😔</h1>
                <p class="card-subtitle">Nous regrettons de vous informer que votre candidature n'a pas été retenue</p>
            </div>
            <div class="row">
                <div class="col-8">
                    <h2>Bonjour <?php echo htmlspecialchars($etudiant['prenom']); ?> !</h2>
                    <p>Après examen approfondi de votre dossier, nous regrettons de vous informer que votre candidature pour le stage n'a pas été retenue.</p>
                    
                    <?php if ($refus): ?>
                        <div class="alert alert-warning">
                            <strong>Raison du refus :</strong><br>
                            <?php echo nl2br(htmlspecialchars($refus['raison_refus'])); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-4 text-right">
                    <div class="badge badge-danger">Candidature refusée</div>
                </div>
            </div>
        </div>

        <!-- Informations détaillées -->
        <div class="row">
            <div class="col-8">
                <!-- Explication du processus -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Pourquoi cette décision ?</h2>
                    </div>
                    <p>Cette décision a été prise après un examen approfondi de votre dossier par l'équipe administrative. 
                    Plusieurs critères sont pris en compte lors de l'évaluation des candidatures :</p>
                    
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

                <!-- Conseils pour améliorer -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h2 class="card-title">Conseils pour améliorer votre candidature</h2>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h3>Améliorations académiques</h3>
                            <ul>
                                <li>Travailler à améliorer votre moyenne générale</li>
                                <li>Participer davantage aux activités académiques</li>
                                <li>Développer vos compétences techniques</li>
                                <li>Suivre des formations complémentaires</li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <h3>Améliorations personnelles</h3>
                            <ul>
                                <li>Réfléchir à votre projet professionnel</li>
                                <li>Développer vos compétences relationnelles</li>
                                <li>Participer à des projets extra-scolaires</li>
                                <li>Améliorer votre CV et lettre de motivation</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Prochaines étapes -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h2 class="card-title">Prochaines étapes</h2>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h3>Vous pouvez :</h3>
                            <ul>
                                <li>Postuler à nouveau lors de la prochaine session</li>
                                <li>Consulter d'autres opportunités de stage</li>
                                <li>Prendre contact avec le service des stages</li>
                                <li>Demander un rendez-vous de conseil</li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <h3>Ressources disponibles</h3>
                            <ul>
                                <li>Service d'orientation et d'insertion</li>
                                <li>Ateliers de préparation aux stages</li>
                                <li>Conseillers pédagogiques</li>
                                <li>Bibliothèque de ressources</li>
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
                        <a href="contact.php" class="btn btn-outline">
                            📞 Contacter l'administration
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
                    <h3>Puis-je faire appel de cette décision ?</h3>
                    <p>Vous pouvez demander un rendez-vous avec le service des stages pour discuter de votre dossier et obtenir des conseils.</p>
                    
                    <h3>Quand puis-je postuler à nouveau ?</h3>
                    <p>Vous pourrez postuler à nouveau lors de la prochaine session de stages, généralement au début du semestre suivant.</p>
                </div>
                <div class="col-6">
                    <h3>Comment améliorer mes chances ?</h3>
                    <p>Travailler sur les points mentionnés dans la raison du refus et améliorer votre profil académique et personnel.</p>
                    
                    <h3>Y a-t-il d'autres opportunités ?</h3>
                    <p>Oui, il existe d'autres types de stages et d'opportunités. Consultez le service d'orientation pour plus d'informations.</p>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Besoin d'aide ?</h2>
            </div>
            <div class="row">
                <div class="col-4">
                    <h3>Service des stages</h3>
                    <p>📧 stages@universite.fr<br>
                    📞 01 23 45 67 89</p>
                </div>
                <div class="col-4">
                    <h3>Service d'orientation</h3>
                    <p>📧 orientation@universite.fr<br>
                    📞 01 23 45 67 90</p>
                </div>
                <div class="col-4">
                    <h3>Conseillers pédagogiques</h3>
                    <p>📧 conseil@universite.fr<br>
                    📞 01 23 45 67 91</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-4 mb-4">
        <p>&copy; 2024 Gestion des Stages. Tous droits réservés.</p>
    </footer>

    <style>
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
        
        .d-grid.gap-2 {
            display: grid;
            gap: 0.5rem;
        }
    </style>

    <script src="js/app.js"></script>
</body>
</html> 