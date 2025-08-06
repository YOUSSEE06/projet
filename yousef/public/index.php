<?php
/**
 * Page d'accueil - Gestion des Stages
 * Formulaires d'inscription et de connexion pour les étudiants
 */

require_once __DIR__ . '/../backend/controllers/EtudiantController.php';

$controller = new EtudiantController();

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'inscription':
                $resultat = $controller->inscrire();
                break;
            case 'connexion':
                $resultat = $controller->connecter();
                break;
        }
    }
}

// Redirection si déjà connecté
if ($controller->estConnecte()) {
    $etudiant = $controller->getEtudiantConnecte();
    switch ($etudiant['statut']) {
        case 'accepte':
            header('Location: dashboard.php');
            exit();
        case 'refuse':
            header('Location: refuse.php');
            exit();
        case 'en_attente':
            header('Location: attente.php');
            exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Stages - Accueil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🎓 Gestion des Stages</a>
            <nav>
                <ul class="nav-menu">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="admin/login.php">Administration</a></li>
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

        <!-- Section d'introduction -->
        <div class="card text-center">
            <div class="card-header">
                <h1 class="card-title">Bienvenue sur la plateforme de gestion des stages</h1>
                <p class="card-subtitle">Inscrivez-vous pour postuler à un stage ou connectez-vous pour suivre votre candidature</p>
            </div>
        </div>

        <!-- Section des formulaires -->
        <div class="row">
            <!-- Formulaire d'inscription -->
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Inscription Étudiant</h2>
                        <p class="card-subtitle">Créez votre compte pour postuler à un stage</p>
                    </div>
                    
                    <form method="POST" data-ajax="true" data-action="inscription">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="nom" class="form-label">Nom *</label>
                                    <input type="text" id="nom" name="nom" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="prenom" class="form-label">Prénom *</label>
                                    <input type="text" id="prenom" name="prenom" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="filiere" class="form-label">Filière *</label>
                            <select id="filiere" name="filiere" class="form-control" required>
                                <option value="">Sélectionnez votre filière</option>
                                <option value="Génie Info">Génie Informatique</option>
                                <option value="Génie Électrique">Génie Électrique</option>
                                <option value="Technique de Management">Technique de Management</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="date_naissance" class="form-label">Date de naissance *</label>
                            <input type="date" id="date_naissance" name="date_naissance" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="etablissement" class="form-label">Établissement *</label>
                            <input type="text" id="etablissement" name="etablissement" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="moyenne" class="form-label">Moyenne générale *</label>
                            <input type="number" id="moyenne" name="moyenne" class="form-control" min="0" max="20" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="mot_de_passe" class="form-label">Mot de passe *</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" minlength="6" required>
                        </div>

                        <div class="form-group">
                            <label for="confirmation_mot_de_passe" class="form-label">Confirmation du mot de passe *</label>
                            <input type="password" id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">S'inscrire</button>
                    </form>
                </div>
            </div>

            <!-- Formulaire de connexion -->
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Connexion Étudiant</h2>
                        <p class="card-subtitle">Accédez à votre espace personnel</p>
                    </div>
                    
                    <form method="POST" data-ajax="true" data-action="connexion">
                        <div class="form-group">
                            <label for="login_email" class="form-label">Email</label>
                            <input type="email" id="login_email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="login_mot_de_passe" class="form-label">Mot de passe</label>
                            <input type="password" id="login_mot_de_passe" name="mot_de_passe" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100">Se connecter</button>
                    </form>

                    <div class="mt-3 text-center">
                        <p><small>Mot de passe oublié ? Contactez l'administration.</small></p>
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Informations importantes</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Tous les champs marqués d'un * sont obligatoires</li>
                            <li>Votre candidature sera examinée par l'équipe administrative</li>
                            <li>Vous recevrez une notification par email du statut de votre candidature</li>
                            <li>Conservez précieusement vos identifiants de connexion</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section des statistiques -->
        <div class="card mt-4">
            <div class="card-header">
                <h2 class="card-title">Statistiques des candidatures</h2>
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

    <script src="js/app.js"></script>
</body>
</html> 
<?php
require_once '../Connect.php';
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// PHPMailer includes
require_once '../PHPMailer-master/src/Exception.php';
require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';

try {
    $sql = new Connect();
    $db = $sql->conn;
} catch (mysqli_sql_exception $e) {
    echo "Can't connect to the database.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($_POST['name'])) {
        // INSCRIPTION
        $name = $_POST['name'];

        // Vérifier si l'email est déjà inscrit
        $check = $db->query("SELECT * FROM registration WHERE email = '$email'");
        if ($check->num_rows > 0) {
            echo "<script>alert('❌ Email déjà inscrit. Essayez de vous connecter.');</script>";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insérer dans la base de données
            $insert = $db->query("INSERT INTO registration (name, email, password) VALUES ('$name', '$email', '$hashedPassword')");
            if ($insert) {
                echo "<script>alert('✅ Inscription réussie. Un email a été envoyé.');</script>";

                // Email à l'admin
                $adminMail = new PHPMailer(true);
                try {
                    $adminMail->CharSet = 'UTF-8';
                    $adminMail->isSMTP();
                    $adminMail->Host = 'smtp.gmail.com';
                    $adminMail->SMTPAuth = true;
                    $adminMail->Username = 'VOTRE_EMAIL@gmail.com'; // 🔴 REMPLACEZ PAR VOTRE EMAIL
                    $adminMail->Password = 'MOT_DE_PASSE_APP'; // 🔴 REMPLACEZ PAR VOTRE MDP APP
                    $adminMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $adminMail->Port = 465;

                    $adminMail->setFrom('VOTRE_EMAIL@gmail.com', 'Form Bot'); // 🔴 REMPLACEZ AUSSI ICI
                    $adminMail->addAddress('ADMIN@EMAIL.COM', 'Admin'); // 🔴 EMAIL DE L'ADMIN
                    $adminMail->isHTML(true);
                    $adminMail->Subject = 'Nouvelle inscription';
                    $adminMail->Body = "
                        <p><strong>Nom:</strong> {$name}</p>
                        <p><strong>Email:</strong> {$email}</p>
                    ";
                    $adminMail->AltBody = "Nom: {$name}\nEmail: {$email}";

                    $adminMail->send();
                } catch (Exception $e) {
                    echo "<script>alert('❌ Erreur email admin: {$adminMail->ErrorInfo}');</script>";
                }

                // Email à l'utilisateur
                $userMail = new PHPMailer(true);
                try {
                    $userMail->CharSet = 'UTF-8';
                    $userMail->isSMTP();
                    $userMail->Host = 'smtp.gmail.com';
                    $userMail->SMTPAuth = true;
                    $userMail->Username = 'VOTRE_EMAIL@gmail.com'; // 🔴 PAREIL
                    $userMail->Password = 'MOT_DE_PASSE_APP'; // 🔴 PAREIL
                    $userMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $userMail->Port = 465;

                    $userMail->setFrom('VOTRE_EMAIL@gmail.com', 'JTR_Shop');
                    $userMail->addAddress($email, $name);
                    $userMail->isHTML(true);
                    $userMail->Subject = '🎉 Félicitations pour votre inscription !';
                    $userMail->Body = "
                        <h2>Bienvenue {$name} !</h2>
                        <p>Merci pour votre inscription sur <strong>JTR_Shop</strong>.</p>
                        <p>📧 Email: {$email}</p>
                    ";
                    $userMail->AltBody = "Bienvenue {$name}, merci pour votre inscription sur JTR_Shop !";

                    $userMail->send();
                } catch (Exception $e) {
                    echo "<script>alert('❌ Erreur email utilisateur: {$userMail->ErrorInfo}');</script>";
                }
            } else {
                echo "<script>alert('❌ Échec de l\'inscription.');</script>";
            }
        }

    } else {
        // CONNEXION
        $check = $db->query("SELECT * FROM registration WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $user = $check->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                echo "<script>alert('✅ Connexion réussie.');</script>";
            } else {
                echo "<script>alert('❌ Mot de passe incorrect.');</script>";
            }
        } else {
            echo "<script>alert('❌ Email non trouvé.');</script>";
        }
    }
}
?>