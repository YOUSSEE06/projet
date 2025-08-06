<?php
/**
 * Page des notifications - Gestion des Stages
 * Page pour afficher toutes les notifications d'un étudiant
 */

require_once __DIR__ . '/../backend/controllers/EtudiantController.php';

$controller = new EtudiantController();

// Vérifier si l'étudiant est connecté
$controller->redirigerSiNonConnecte();

// Récupérer les informations de l'étudiant
$etudiant = $controller->getEtudiantConnecte();

// Récupérer toutes les notifications
$notifications = $controller->getNotifications();

// Compter les notifications non lues
$notificationsNonLues = array_filter($notifications, function($n) {
    return !$n['lu'];
});
$nombreNonLues = count($notificationsNonLues);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🎓 Gestion des Stages</a>
            <nav>
                <ul class="nav-menu">
                    <?php if ($etudiant['statut'] === 'accepte'): ?>
                        <li><a href="dashboard.php">Tableau de bord</a></li>
                    <?php elseif ($etudiant['statut'] === 'en_attente'): ?>
                        <li><a href="attente.php">En attente</a></li>
                    <?php elseif ($etudiant['statut'] === 'refuse'): ?>
                        <li><a href="refuse.php">Candidature refusée</a></li>
                    <?php endif; ?>
                    <li><a href="notifications.php">Notifications <?php if ($nombreNonLues > 0): ?><span class="badge badge-danger"><?php echo $nombreNonLues; ?></span><?php endif; ?></a></li>
                    <li><a href="#" class="btn-deconnexion">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Container principal -->
    <div class="container">
        <!-- Alertes -->
        <div id="alert-container"></div>

        <!-- En-tête des notifications -->
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Notifications</h1>
                <p class="card-subtitle">Restez informé de l'évolution de votre candidature</p>
            </div>
            <div class="row">
                <div class="col-8">
                    <h2>Bonjour <?php echo htmlspecialchars($etudiant['prenom']); ?> !</h2>
                    <p>Voici toutes les notifications concernant votre candidature pour le stage.</p>
                </div>
                <div class="col-4 text-right">
                    <div class="badge badge-<?php 
                        if ($etudiant['statut'] === 'accepte') echo 'success';
                        elseif ($etudiant['statut'] === 'en_attente') echo 'warning';
                        else echo 'danger';
                    ?>">
                        <?php 
                        if ($etudiant['statut'] === 'accepte') echo 'Stage Accepté';
                        elseif ($etudiant['statut'] === 'en_attente') echo 'En attente';
                        else echo 'Candidature refusée';
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des notifications -->
        <div class="row">
            <div class="col-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            Toutes les notifications 
                            <?php if ($nombreNonLues > 0): ?>
                                <span class="badge badge-danger"><?php echo $nombreNonLues; ?> non lue<?php echo $nombreNonLues > 1 ? 's' : ''; ?></span>
                            <?php endif; ?>
                        </h2>
                    </div>
                    
                    <?php if (!empty($notifications)): ?>
                        <div class="notifications-container">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="notification-item <?php echo $notification['lu'] ? 'lu' : 'non-lue'; ?>" data-id="<?php echo $notification['id']; ?>">
                                    <div class="notification-header">
                                        <div class="notification-type">
                                            <?php
                                            $icon = '';
                                            $color = '';
                                            switch ($notification['type_notification']) {
                                                case 'acceptation':
                                                    $icon = '✅';
                                                    $color = 'success';
                                                    break;
                                                case 'refus':
                                                    $icon = '❌';
                                                    $color = 'danger';
                                                    break;
                                                case 'en_attente':
                                                    $icon = '⏳';
                                                    $color = 'warning';
                                                    break;
                                                default:
                                                    $icon = '📢';
                                                    $color = 'info';
                                            }
                                            ?>
                                            <span class="notification-icon"><?php echo $icon; ?></span>
                                            <span class="badge badge-<?php echo $color; ?>">
                                                <?php echo ucfirst($notification['type_notification']); ?>
                                            </span>
                                        </div>
                                        <div class="notification-date">
                                            <?php echo date('d/m/Y à H:i', strtotime($notification['date_envoi'])); ?>
                                        </div>
                                    </div>
                                    <div class="notification-content">
                                        <p><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                                    </div>
                                    <?php if (!$notification['lu']): ?>
                                        <div class="notification-actions">
                                            <button class="btn btn-sm btn-outline marquer-lue" data-id="<?php echo $notification['id']; ?>">
                                                Marquer comme lue
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-4">
                            <div class="empty-state">
                                <div class="empty-icon">📭</div>
                                <h3>Aucune notification</h3>
                                <p>Vous n'avez pas encore reçu de notifications.</p>
                            </div>
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
                        <label class="form-label">Statut</label>
                        <p class="form-control-static">
                            <span class="badge badge-<?php 
                                if ($etudiant['statut'] === 'accepte') echo 'success';
                                elseif ($etudiant['statut'] === 'en_attente') echo 'warning';
                                else echo 'danger';
                            ?>">
                                <?php 
                                if ($etudiant['statut'] === 'accepte') echo 'Stage Accepté';
                                elseif ($etudiant['statut'] === 'en_attente') echo 'En attente';
                                else echo 'Candidature refusée';
                                ?>
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Actions disponibles -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Actions disponibles</h3>
                    </div>
                    <div class="d-grid gap-2">
                        <?php if ($etudiant['statut'] === 'accepte'): ?>
                            <a href="dashboard.php" class="btn btn-outline">
                                🏠 Tableau de bord
                            </a>
                        <?php elseif ($etudiant['statut'] === 'en_attente'): ?>
                            <a href="attente.php" class="btn btn-outline">
                                ⏳ Page d'attente
                            </a>
                        <?php elseif ($etudiant['statut'] === 'refuse'): ?>
                            <a href="refuse.php" class="btn btn-outline">
                                ❌ Candidature refusée
                            </a>
                        <?php endif; ?>
                        <a href="profil.php" class="btn btn-outline">
                            👤 Modifier mon profil
                        </a>
                        <a href="index.php" class="btn btn-outline">
                            🏠 Retour à l'accueil
                        </a>
                    </div>
                </div>

                <!-- Statistiques des notifications -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Statistiques</h3>
                    </div>
                    <div class="stats-list">
                        <div class="stat-item">
                            <span class="stat-label">Total</span>
                            <span class="stat-value"><?php echo count($notifications); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Non lues</span>
                            <span class="stat-value"><?php echo $nombreNonLues; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Lues</span>
                            <span class="stat-value"><?php echo count($notifications) - $nombreNonLues; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations sur les notifications -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">À propos des notifications</h2>
            </div>
            <div class="row">
                <div class="col-6">
                    <h3>Types de notifications</h3>
                    <ul>
                        <li><strong>Acceptation :</strong> Votre candidature a été acceptée</li>
                        <li><strong>Refus :</strong> Votre candidature n'a pas été retenue</li>
                        <li><strong>En attente :</strong> Informations sur l'examen de votre dossier</li>
                    </ul>
                </div>
                <div class="col-6">
                    <h3>Gestion des notifications</h3>
                    <ul>
                        <li>Les nouvelles notifications apparaissent en haut de la liste</li>
                        <li>Cliquez sur "Marquer comme lue" pour les marquer</li>
                        <li>Les notifications lues sont grisées</li>
                        <li>Vous recevrez aussi un email pour chaque notification</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-4 mb-4">
        <p>&copy; 2024 Gestion des Stages. Tous droits réservés.</p>
    </footer>

    <style>
        .notifications-container {
            max-height: 600px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 1.5rem;
            border-bottom: 1px solid #e1e5e9;
            transition: background-color 0.3s ease;
        }
        
        .notification-item:hover {
            background: rgba(102, 126, 234, 0.05);
        }
        
        .notification-item.non-lue {
            background: rgba(52, 152, 219, 0.05);
            border-left: 4px solid #3498db;
        }
        
        .notification-item.lu {
            opacity: 0.8;
        }
        
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .notification-type {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .notification-icon {
            font-size: 1.2rem;
        }
        
        .notification-date {
            color: #999;
            font-size: 0.9rem;
        }
        
        .notification-content p {
            margin: 0;
            line-height: 1.6;
        }
        
        .notification-actions {
            margin-top: 1rem;
            text-align: right;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .stats-list {
            display: grid;
            gap: 0.5rem;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 4px;
        }
        
        .stat-label {
            font-weight: 500;
        }
        
        .stat-value {
            font-weight: bold;
            color: #667eea;
        }
        
        .d-grid.gap-2 {
            display: grid;
            gap: 0.5rem;
        }
    </style>

    <script src="js/app.js"></script>
    <script>
        // Gestion des boutons "Marquer comme lue"
        document.querySelectorAll('.marquer-lue').forEach(btn => {
            btn.addEventListener('click', async function() {
                const notificationId = this.getAttribute('data-id');
                const notificationItem = this.closest('.notification-item');
                
                try {
                    const formData = new FormData();
                    formData.append('ajax', 'true');
                    formData.append('action', 'marquer_notification_lue');
                    formData.append('notification_id', notificationId);

                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        notificationItem.classList.remove('non-lue');
                        notificationItem.classList.add('lu');
                        this.remove();
                        
                        // Mettre à jour le compteur
                        const badge = document.querySelector('.badge-danger');
                        if (badge) {
                            const count = parseInt(badge.textContent) - 1;
                            if (count > 0) {
                                badge.textContent = count;
                            } else {
                                badge.remove();
                            }
                        }
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            });
        });
    </script>
</body>
</html> 