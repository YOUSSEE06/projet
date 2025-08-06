<?php
/**
 * Page de connexion administrateur - Gestion des Stages
 * Interface de connexion pour les administrateurs
 */

require_once __DIR__ . '/../../backend/controllers/AdminController.php';

$controller = new AdminController();

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'connexion') {
        $resultat = $controller->connecter();
    }
}

// Redirection si déjà connecté
if ($controller->estConnecte()) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur - Gestion des Stages</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="../index.php" class="logo">🎓 Gestion des Stages</a>
            <nav>
                <ul class="nav-menu">
                    <li><a href="../index.php">Accueil</a></li>
                    <li><a href="login.php">Administration</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Container principal -->
    <div class="container">
        <!-- Alertes -->
        <div id="alert-container">
            <?php if (isset($resultat)): ?>
                <div class="alert alert-<?php echo $resultat['success'] ? 'success' : 'danger'; ?> fade-in">
                    <?php echo htmlspecialchars($resultat['message']); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Section de connexion -->
        <div class="row justify-center">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">🔐 Connexion Administrateur</h1>
                        <p class="card-subtitle">Accédez au panneau d'administration</p>
                    </div>
                    
                    <form method="POST" data-ajax="true" data-action="connexion">
                        <div class="form-group">
                            <label for="email" class="form-label">Email administrateur</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="mot_de_passe" class="form-label">Mot de passe</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">Se connecter</button>
                    </form>

                    <div class="mt-3 text-center">
                        <p><small>Compte par défaut : admin@stages.com / admin123</small></p>
                    </div>
                </div>

                <!-- Informations de sécurité -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Sécurité</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Cette interface est réservée aux administrateurs</li>
                            <li>Toutes les actions sont enregistrées dans les logs</li>
                            <li>En cas de problème, contactez le service informatique</li>
                            <li>Déconnectez-vous après chaque session</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques publiques -->
        <div class="card mt-4">
            <div class="card-header">
                <h2 class="card-title">Statistiques publiques</h2>
            </div>
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-number">150+</div>
                    <div class="stat-label">Étudiants inscrits</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">45</div>
                    <div class="stat-label">Stages acceptés</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">3</div>
                    <div class="stat-label">Filières disponibles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">15.2</div>
                    <div class="stat-label">Moyenne générale</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-4 mb-4">
        <p>&copy; 2024 Gestion des Stages. Tous droits réservés.</p>
    </footer>

    <script src="../js/app.js"></script>
</body>
</html> 