<?php
/**
 * Tableau de bord administrateur - Gestion des Stages
 * Interface de gestion des candidatures étudiantes
 */

require_once __DIR__ . '/../../backend/controllers/AdminController.php';

$controller = new AdminController();

// Vérifier si l'administrateur est connecté
$controller->redirigerSiNonConnecte();

// Récupérer les données
$etudiantsEnAttente = $controller->getEtudiantsEnAttente();
$statistiques = $controller->getStatistiques();
$etudiantsAcceptes = $controller->getEtudiantsAcceptes();
$etudiantsRefuses = $controller->getEtudiantsRefuses();

// Paramètres de tri
$tri = $_GET['tri'] ?? 'date_inscription';
$ordre = $_GET['ordre'] ?? 'ASC';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Administrateur - Gestion des Stages</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="../index.php" class="logo">🎓 Gestion des Stages</a>
            <nav>
                <ul class="nav-menu">
                    <li><a href="dashboard.php">Tableau de bord</a></li>
                    <li><a href="etudiants.php">Gestion étudiants</a></li>
                    <li><a href="statistiques.php">Statistiques</a></li>
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
                <h1 class="card-title">Tableau de bord Administrateur</h1>
                <p class="card-subtitle">Gestion des candidatures pour les stages</p>
            </div>
        </div>

        <!-- Statistiques globales -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $statistiques['globales']['total_etudiants'] ?? 0; ?></div>
                <div class="stat-label">Total étudiants</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $statistiques['globales']['en_attente'] ?? 0; ?></div>
                <div class="stat-label">En attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $statistiques['globales']['acceptes'] ?? 0; ?></div>
                <div class="stat-label">Acceptés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $statistiques['globales']['refuses'] ?? 0; ?></div>
                <div class="stat-label">Refusés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($statistiques['globales']['moyenne_generale'] ?? 0, 1); ?></div>
                <div class="stat-label">Moyenne générale</div>
            </div>
        </div>

        <!-- Onglets -->
        <div class="tabs">
            <button class="tab active" data-tab="en-attente">En attente (<?php echo count($etudiantsEnAttente); ?>)</button>
            <button class="tab" data-tab="acceptes">Acceptés (<?php echo count($etudiantsAcceptes); ?>)</button>
            <button class="tab" data-tab="refuses">Refusés (<?php echo count($etudiantsRefuses); ?>)</button>
        </div>

        <!-- Contenu des onglets -->
        
        <!-- Onglet : Étudiants en attente -->
        <div id="en-attente" class="tab-content active">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Étudiants en attente de traitement</h2>
                    <p class="card-subtitle">Candidatures à examiner</p>
                </div>
                
                <?php if (!empty($etudiantsEnAttente)): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="?tri=nom&ordre=<?php echo $tri === 'nom' && $ordre === 'ASC' ? 'DESC' : 'ASC'; ?>" class="btn-tri" data-field="nom" data-order="<?php echo $tri === 'nom' ? $ordre : 'ASC'; ?>">
                                            Nom
                                            <?php if ($tri === 'nom'): ?>
                                                <?php echo $ordre === 'ASC' ? '↑' : '↓'; ?>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>
                                        <a href="?tri=filiere&ordre=<?php echo $tri === 'filiere' && $ordre === 'ASC' ? 'DESC' : 'ASC'; ?>" class="btn-tri" data-field="filiere" data-order="<?php echo $tri === 'filiere' ? $ordre : 'ASC'; ?>">
                                            Filière
                                            <?php if ($tri === 'filiere'): ?>
                                                <?php echo $ordre === 'ASC' ? '↑' : '↓'; ?>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?tri=moyenne&ordre=<?php echo $tri === 'moyenne' && $ordre === 'ASC' ? 'DESC' : 'ASC'; ?>" class="btn-tri" data-field="moyenne" data-order="<?php echo $tri === 'moyenne' ? $ordre : 'ASC'; ?>">
                                            Moyenne
                                            <?php if ($tri === 'moyenne'): ?>
                                                <?php echo $ordre === 'ASC' ? '↑' : '↓'; ?>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?tri=date_inscription&ordre=<?php echo $tri === 'date_inscription' && $ordre === 'ASC' ? 'DESC' : 'ASC'; ?>" class="btn-tri" data-field="date_inscription" data-order="<?php echo $tri === 'date_inscription' ? $ordre : 'ASC'; ?>">
                                            Date d'inscription
                                            <?php if ($tri === 'date_inscription'): ?>
                                                <?php echo $ordre === 'ASC' ? '↑' : '↓'; ?>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($etudiantsEnAttente as $etudiant): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($etudiant['prenom']); ?></td>
                                        <td><?php echo htmlspecialchars($etudiant['email']); ?></td>
                                        <td>
                                            <span class="badge badge-info"><?php echo htmlspecialchars($etudiant['filiere']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $etudiant['moyenne'] >= 14 ? 'success' : ($etudiant['moyenne'] >= 12 ? 'warning' : 'danger'); ?>">
                                                <?php echo number_format($etudiant['moyenne'], 2); ?>/20
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($etudiant['date_inscription'])); ?></td>
                                        <td>
                                            <button class="btn btn-success btn-sm btn-accepter" 
                                                    data-id="<?php echo $etudiant['id']; ?>" 
                                                    data-nom="<?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?>">
                                                ✅ Accepter
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-refuser" 
                                                    data-id="<?php echo $etudiant['id']; ?>" 
                                                    data-nom="<?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?>">
                                                ❌ Refuser
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center p-4">
                        <div class="empty-state">
                            <div class="empty-icon">✅</div>
                            <h3>Aucun étudiant en attente</h3>
                            <p>Toutes les candidatures ont été traitées.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Onglet : Étudiants acceptés -->
        <div id="acceptes" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Étudiants acceptés</h2>
                    <p class="card-subtitle">Stages confirmés</p>
                </div>
                
                <?php if (!empty($etudiantsAcceptes)): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Filière</th>
                                    <th>Type de stage</th>
                                    <th>Date d'acceptation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($etudiantsAcceptes as $stage): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($stage['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($stage['prenom']); ?></td>
                                        <td><?php echo htmlspecialchars($stage['email']); ?></td>
                                        <td>
                                            <span class="badge badge-info"><?php echo htmlspecialchars($stage['filiere']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success"><?php echo htmlspecialchars($stage['type_stage']); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($stage['date_acceptation'])); ?></td>
                                        <td>
                                            <button class="btn btn-outline btn-sm" onclick="voirDetails(<?php echo $stage['id']; ?>)">
                                                👁️ Voir détails
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center p-4">
                        <div class="empty-state">
                            <div class="empty-icon">📋</div>
                            <h3>Aucun étudiant accepté</h3>
                            <p>Aucun stage n'a encore été confirmé.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Onglet : Étudiants refusés -->
        <div id="refuses" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Étudiants refusés</h2>
                    <p class="card-subtitle">Candidatures non retenues</p>
                </div>
                
                <?php if (!empty($etudiantsRefuses)): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Filière</th>
                                    <th>Raison du refus</th>
                                    <th>Date de refus</th>
                                    <th>Admin responsable</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($etudiantsRefuses as $refus): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($refus['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($refus['prenom']); ?></td>
                                        <td><?php echo htmlspecialchars($refus['email']); ?></td>
                                        <td>
                                            <span class="badge badge-info"><?php echo htmlspecialchars($refus['filiere']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars(substr($refus['raison_refus'], 0, 50)) . (strlen($refus['raison_refus']) > 50 ? '...' : ''); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($refus['date_refus'])); ?></td>
                                        <td><?php echo htmlspecialchars($refus['admin_responsable']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center p-4">
                        <div class="empty-state">
                            <div class="empty-icon">📝</div>
                            <h3>Aucun étudiant refusé</h3>
                            <p>Aucune candidature n'a encore été refusée.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal d'acceptation -->
    <div id="modal-accepter" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Accepter un étudiant</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form method="POST" data-ajax="true" data-action="accepter_etudiant">
                <input type="hidden" name="etudiant_id" value="">
                <div class="form-group">
                    <label for="type_stage" class="form-label">Type de stage</label>
                    <select id="type_stage" name="type_stage" class="form-control" required>
                        <option value="">Sélectionnez le type de stage</option>
                        <option value="PFE">PFE (Projet de Fin d'Études)</option>
                        <option value="Stage d'été">Stage d'été</option>
                        <option value="Stage d'hiver">Stage d'hiver</option>
                    </select>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-secondary modal-close">Annuler</button>
                    <button type="submit" class="btn btn-success">Accepter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de refus -->
    <div id="modal-refuser" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Refuser un étudiant</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form method="POST" data-ajax="true" data-action="refuser_etudiant">
                <input type="hidden" name="etudiant_id" value="">
                <div class="form-group">
                    <label for="raison_refus" class="form-label">Raison du refus *</label>
                    <textarea id="raison_refus" name="raison_refus" class="form-control" rows="4" required placeholder="Expliquez la raison du refus..."></textarea>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-secondary modal-close">Annuler</button>
                    <button type="submit" class="btn btn-danger">Refuser</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-4 mb-4">
        <p>&copy; 2024 Gestion des Stages. Tous droits réservés.</p>
    </footer>

    <style>
        .empty-state {
            text-align: center;
            padding: 2rem;
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .btn-tri {
            background: none;
            border: none;
            color: inherit;
            font-weight: inherit;
            cursor: pointer;
            text-decoration: none;
        }
        
        .btn-tri:hover {
            color: #667eea;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
    </style>

    <script src="../js/app.js"></script>
    <script>
        // Gestion des onglets
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const targetId = this.getAttribute('data-tab');
                
                // Masquer tous les contenus d'onglets
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Désactiver tous les onglets
                document.querySelectorAll('.tab').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Activer l'onglet sélectionné
                this.classList.add('active');
                document.getElementById(targetId).classList.add('active');
            });
        });

        // Fonction pour voir les détails d'un stage
        function voirDetails(stageId) {
            // Ici vous pouvez implémenter l'affichage des détails
            alert('Fonctionnalité de détails à implémenter pour le stage ID: ' + stageId);
        }
    </script>
</body>
</html> 