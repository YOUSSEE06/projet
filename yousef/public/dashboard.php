<?php
/**
 * Tableau de bord étudiant - Gestion des Stages
 * Page pour les étudiants dont le stage a été accepté
 */

require_once __DIR__ . '/../backend/controllers/EtudiantController.php';

$controller = new EtudiantController();

// Vérifier si l'étudiant est connecté
$controller->redirigerSiNonConnecte();

// Récupérer les informations de l'étudiant
$etudiant = $controller->getEtudiantConnecte();

// Vérifier que l'étudiant est accepté
if ($etudiant['statut'] !== 'accepte') {
    switch ($etudiant['statut']) {
        case 'refuse':
            header('Location: refuse.php');
            exit();
        case 'en_attente':
            header('Location: attente.php');
            exit();
    }
}

// Récupérer les notifications
$notifications = $controller->getNotifications();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🎓 Gestion des Stages</a>
            <nav>
                <ul class="nav-menu">
                    <li><a href="dashboard.php">Tableau de bord</a></li>
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

        <!-- En-tête du tableau de bord -->
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Félicitations ! 🎉</h1>
                <p class="card-subtitle">Votre candidature pour le stage a été acceptée</p>
            </div>
            <div class="row">
                <div class="col-8">
                    <h2>Bonjour <?php echo htmlspecialchars($etudiant['prenom']); ?> !</h2>
                    <p>Votre profil a été validé par l'équipe administrative. Vous pouvez maintenant accéder aux informations de votre stage.</p>
                </div>
                <div class="col-4 text-right">
                    <div class="badge badge-success">Stage Accepté</div>
                </div>
            </div>
        </div>

        <!-- Informations du stage -->
        <div class="row">
            <div class="col-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Informations de votre stage</h2>
                    </div>
                    
                    <?php
                    // Récupérer les détails du stage depuis la base de données
                    require_once __DIR__ . '/../config/database.php';
                    $db = getDB();
                    $sql = "SELECT * FROM stages_acceptes WHERE id_etudiant = ?";
                    $stage = $db->queryOne($sql, [$etudiant['id']]);
                    ?>
                    
                    <?php if ($stage): ?>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Type de stage</label>
                                    <p class="form-control-static"><?php echo htmlspecialchars($stage['type_stage']); ?></p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Filière</label>
                                    <p class="form-control-static"><?php echo htmlspecialchars($stage['filiere']); ?></p>
                                </div>
                            </div>
                        </div>

                        <?php if ($stage['date_debut_stage']): ?>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Date de début</label>
                                        <p class="form-control-static"><?php echo date('d/m/Y', strtotime($stage['date_debut_stage'])); ?></p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Date de fin</label>
                                        <p class="form-control-static"><?php echo date('d/m/Y', strtotime($stage['date_fin_stage'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($stage['encadrant']): ?>
                            <div class="form-group">
                                <label class="form-label">Encadrant</label>
                                <p class="form-control-static"><?php echo htmlspecialchars($stage['encadrant']); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($stage['entreprise']): ?>
                            <div class="form-group">
                                <label class="form-label">Entreprise</label>
                                <p class="form-control-static"><?php echo htmlspecialchars($stage['entreprise']); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <strong>Note :</strong> Les détails complets de votre stage (dates, encadrant, entreprise) seront communiqués ultérieurement par l'équipe administrative.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <strong>Information :</strong> Les détails de votre stage sont en cours de finalisation. Vous recevrez bientôt plus d'informations.
                        </div>
                    <?php endif; ?>
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
            </div>
        </div>

        <!-- Actions disponibles -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Actions disponibles</h2>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="text-center">
                        <a href="notifications.php" class="btn btn-outline btn-lg">
                            📬 Voir les notifications
                        </a>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center">
                        <a href="profil.php" class="btn btn-outline btn-lg">
                            👤 Modifier mon profil
                        </a>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center">
                        <a href="#" class="btn btn-outline btn-lg">
                            📋 Documents requis
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations importantes -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Informations importantes</h2>
            </div>
            <div class="row">
                <div class="col-6">
                    <h3>Prochaines étapes</h3>
                    <ul>
                        <li>Attendre la confirmation des dates de stage</li>
                        <li>Préparer les documents requis</li>
                        <li>Prendre contact avec votre encadrant</li>
                        <li>Assister à la réunion d'information</li>
                    </ul>
                </div>
                <div class="col-6">
                    <h3>Documents à préparer</h3>
                    <ul>
                        <li>Convention de stage</li>
                        <li>Attestation d'assurance</li>
                        <li>CV et lettre de motivation</li>
                        <li>Relevés de notes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-4 mb-4">
        <p>&copy; 2024 Gestion des Stages. Tous droits réservés.</p>
    </footer>

    <script src="js/app.js"></script>
</body>
</html> 