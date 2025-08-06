<?php
/**
 * Page de profil étudiant - Gestion des Stages
 * Permet aux étudiants de modifier leurs informations
 */

require_once __DIR__ . '/../backend/controllers/EtudiantController.php';

$controller = new EtudiantController();

// Vérifier si l'étudiant est connecté
$controller->redirigerSiNonConnecte();

// Récupérer les informations de l'étudiant
$etudiant = $controller->getEtudiantConnecte();

// Traitement de la mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_profil') {
        // Ici vous pouvez implémenter la mise à jour du profil
        $resultat = ['success' => true, 'message' => 'Profil mis à jour avec succès'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></title>
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
                    <li><a href="notifications.php">Notifications</a></li>
                    <li><a href="profil.php">Mon profil</a></li>
                    <li><a href="#" class="btn-deconnexion">Déconnexion</a></li>
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

        <!-- En-tête du profil -->
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Mon Profil</h1>
                <p class="card-subtitle">Gérez vos informations personnelles</p>
            </div>
            <div class="row">
                <div class="col-8">
                    <h2>Bonjour <?php echo htmlspecialchars($etudiant['prenom']); ?> !</h2>
                    <p>Vous pouvez modifier vos informations personnelles ci-dessous.</p>
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

        <!-- Formulaire de modification -->
        <div class="row">
            <div class="col-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Modifier mes informations</h2>
                    </div>
                    
                    <form method="POST" data-ajax="true" data-action="update_profil">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="nom" class="form-label">Nom *</label>
                                    <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($etudiant['nom']); ?>" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="prenom" class="form-label">Prénom *</label>
                                    <input type="text" id="prenom" name="prenom" class="form-control" value="<?php echo htmlspecialchars($etudiant['prenom']); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($etudiant['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="filiere" class="form-label">Filière *</label>
                            <select id="filiere" name="filiere" class="form-control" required>
                                <option value="">Sélectionnez votre filière</option>
                                <option value="Génie Info" <?php echo $etudiant['filiere'] === 'Génie Info' ? 'selected' : ''; ?>>Génie Informatique</option>
                                <option value="Génie Électrique" <?php echo $etudiant['filiere'] === 'Génie Électrique' ? 'selected' : ''; ?>>Génie Électrique</option>
                                <option value="Technique de Management" <?php echo $etudiant['filiere'] === 'Technique de Management' ? 'selected' : ''; ?>>Technique de Management</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="date_naissance" class="form-label">Date de naissance *</label>
                            <input type="date" id="date_naissance" name="date_naissance" class="form-control" value="<?php echo $etudiant['date_naissance']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="etablissement" class="form-label">Établissement *</label>
                            <input type="text" id="etablissement" name="etablissement" class="form-control" value="<?php echo htmlspecialchars($etudiant['etablissement']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="moyenne" class="form-label">Moyenne générale *</label>
                            <input type="number" id="moyenne" name="moyenne" class="form-control" min="0" max="20" step="0.01" value="<?php echo $etudiant['moyenne']; ?>" required>
                        </div>

                        <div class="alert alert-info">
                            <strong>Note :</strong> La modification de certaines informations peut nécessiter une nouvelle validation de votre candidature.
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">Mettre à jour mon profil</button>
                    </form>
                </div>

                <!-- Changement de mot de passe -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h2 class="card-title">Changer mon mot de passe</h2>
                    </div>
                    
                    <form method="POST" data-ajax="true" data-action="change_password">
                        <div class="form-group">
                            <label for="ancien_mot_de_passe" class="form-label">Ancien mot de passe *</label>
                            <input type="password" id="ancien_mot_de_passe" name="ancien_mot_de_passe" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="nouveau_mot_de_passe" class="form-label">Nouveau mot de passe *</label>
                            <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" class="form-control" minlength="6" required>
                        </div>

                        <div class="form-group">
                            <label for="confirmation_nouveau_mot_de_passe" class="form-label">Confirmation du nouveau mot de passe *</label>
                            <input type="password" id="confirmation_nouveau_mot_de_passe" name="confirmation_nouveau_mot_de_passe" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-warning">Changer le mot de passe</button>
                    </form>
                </div>
            </div>

            <!-- Informations du profil -->
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informations actuelles</h3>
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
                    <div class="form-group">
                        <label class="form-label">Date d'inscription</label>
                        <p class="form-control-static"><?php echo date('d/m/Y H:i', strtotime($etudiant['date_inscription'])); ?></p>
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
                        <a href="notifications.php" class="btn btn-outline">
                            📬 Notifications
                        </a>
                        <a href="index.php" class="btn btn-outline">
                            🏠 Retour à l'accueil
                        </a>
                    </div>
                </div>

                <!-- Informations importantes -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Informations importantes</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Votre email ne peut pas être modifié</li>
                            <li>La modification de la filière peut affecter votre candidature</li>
                            <li>Conservez votre mot de passe en sécurité</li>
                            <li>Contactez l'administration en cas de problème</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des modifications -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Historique des modifications</h2>
            </div>
            <div class="row">
                <div class="col-6">
                    <h3>Dernières modifications</h3>
                    <ul>
                        <li><strong>Profil créé :</strong> <?php echo date('d/m/Y H:i', strtotime($etudiant['date_inscription'])); ?></li>
                        <li><strong>Dernière modification :</strong> <?php echo date('d/m/Y H:i', strtotime($etudiant['date_modification'])); ?></li>
                    </ul>
                </div>
                <div class="col-6">
                    <h3>Statut de la candidature</h3>
                    <ul>
                        <li><strong>Statut actuel :</strong> 
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
                        </li>
                        <li><strong>ID étudiant :</strong> <?php echo $etudiant['id']; ?></li>
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
        .d-grid.gap-2 {
            display: grid;
            gap: 0.5rem;
        }
        
        .form-control-static {
            padding: 0.5rem;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 4px;
            font-weight: 500;
        }
    </style>

    <script src="js/app.js"></script>
</body>
</html> 