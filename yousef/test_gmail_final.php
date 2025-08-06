<?php
/**
 * Test Final Gmail SMTP avec Mot de Passe d'Application
 * Test de vrais emails envoyés aux adresses Gmail
 */

require_once 'config/email.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Gmail SMTP Final - Vrais Emails</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header {
            text-align: center;
            color: #2c3e50;
            border-bottom: 3px solid #28a745;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            padding: 20px;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            margin: 15px 0;
        }
        .error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            padding: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            margin: 15px 0;
        }
        .info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
            padding: 20px;
            border: 1px solid #bee5eb;
            border-radius: 10px;
            margin: 15px 0;
        }
        .btn {
            background: #28a745;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
            font-size: 16px;
        }
        .btn:hover {
            background: #218838;
        }
        .gmail-badge {
            background: linear-gradient(135deg, #ea4335, #fbbc05);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            font-weight: bold;
            margin: 10px 0;
        }
        .form-group {
            margin: 15px 0;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .config-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Test Gmail SMTP Final</h1>
            <div class="gmail-badge">📧 Vrais Emails vers Gmail</div>
            <p>Configuration avec mot de passe d'application Gmail</p>
        </div>

        <div class="info">
            <h3>📋 Configuration Actuelle</h3>
            <div class="config-info">
                <strong>Email Gmail:</strong> youssefoularbi630@gmail.com<br>
                <strong>Mot de passe d'app:</strong> jizs hjnq wpgq vxry<br>
                <strong>Serveur SMTP:</strong> smtp.gmail.com:587<br>
                <strong>Sécurité:</strong> TLS/STARTTLS
            </div>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emailDestinataire = $_POST['email_destinataire'] ?? '';
            $nomDestinataire = $_POST['nom_destinataire'] ?? '';
            $typeTest = $_POST['type_test'] ?? 'acceptation';
            
            if ($emailDestinataire && $nomDestinataire) {
                echo "<h2>🔄 Test d'Envoi en Cours...</h2>";
                
                try {
                    $emailConfig = new EmailConfig();
                    
                    if ($typeTest === 'acceptation') {
                        $resultat = $emailConfig->envoyerNotificationAcceptation(
                            $emailDestinataire, 
                            $nomDestinataire, 
                            'Stage de Développement Web'
                        );
                    } else {
                        $resultat = $emailConfig->envoyerNotificationRefus(
                            $emailDestinataire, 
                            $nomDestinataire, 
                            'Stage de Développement Web',
                            'Profil ne correspondant pas aux critères requis pour cette session'
                        );
                    }
                    
                    if ($resultat) {
                        echo "<div class='success'>";
                        echo "<h3>✅ EMAIL ENVOYÉ AVEC SUCCÈS !</h3>";
                        echo "<p><strong>📧 Destinataire:</strong> $emailDestinataire</p>";
                        echo "<p><strong>👤 Nom:</strong> $nomDestinataire</p>";
                        echo "<p><strong>📋 Type:</strong> " . ucfirst($typeTest) . "</p>";
                        echo "<p><strong>⏰ Heure:</strong> " . date('Y-m-d H:i:s') . "</p>";
                        echo "<hr>";
                        echo "<h4>🎉 L'email a été envoyé à l'adresse Gmail !</h4>";
                        echo "<p>L'étudiant devrait recevoir l'email dans sa boîte Gmail dans quelques minutes.</p>";
                        echo "</div>";
                    } else {
                        echo "<div class='error'>";
                        echo "<h3>❌ ÉCHEC DE L'ENVOI</h3>";
                        echo "<p>L'email n'a pas pu être envoyé. Vérifiez :</p>";
                        echo "<ul>";
                        echo "<li>La connexion internet</li>";
                        echo "<li>Les paramètres Gmail (2FA activé ?)</li>";
                        echo "<li>Le mot de passe d'application</li>";
                        echo "<li>Les logs d'erreur PHP</li>";
                        echo "</ul>";
                        echo "</div>";
                    }
                    
                } catch (Exception $e) {
                    echo "<div class='error'>";
                    echo "<h3>❌ ERREUR TECHNIQUE</h3>";
                    echo "<p><strong>Message d'erreur:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                    echo "<p>Vérifiez la configuration SMTP et les logs.</p>";
                    echo "</div>";
                }
            } else {
                echo "<div class='error'>";
                echo "<h3>⚠️ Données Manquantes</h3>";
                echo "<p>Veuillez remplir tous les champs requis.</p>";
                echo "</div>";
            }
        }
        ?>

        <h2>📧 Test d'Envoi d'Email</h2>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email_destinataire">📧 Email Destinataire (Gmail):</label>
                <input type="email" id="email_destinataire" name="email_destinataire" 
                       placeholder="exemple@gmail.com" required>
                <small>Entrez une vraie adresse Gmail pour recevoir l'email de test</small>
            </div>
            
            <div class="form-group">
                <label for="nom_destinataire">👤 Nom du Destinataire:</label>
                <input type="text" id="nom_destinataire" name="nom_destinataire" 
                       placeholder="Nom Prénom" required>
            </div>
            
            <div class="form-group">
                <label for="type_test">📋 Type d'Email:</label>
                <select id="type_test" name="type_test" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="acceptation">✅ Email d'Acceptation</option>
                    <option value="refus">❌ Email de Refus</option>
                </select>
            </div>
            
            <div style="text-align: center;">
                <button type="submit" class="btn">🚀 Envoyer Email de Test</button>
            </div>
        </form>

        <div class="success">
            <h3>✅ Fonctionnement Automatique</h3>
            <p><strong>Une fois ce test réussi, votre système fonctionnera automatiquement :</strong></p>
            <ul>
                <li>📧 <strong>Acceptation d'étudiant</strong> → Email automatique envoyé à son adresse Gmail</li>
                <li>📧 <strong>Refus d'étudiant</strong> → Email automatique envoyé à son adresse Gmail</li>
                <li>📊 <strong>Logs automatiques</strong> de tous les envois</li>
                <li>🔄 <strong>Intégration complète</strong> dans votre système de gestion</li>
            </ul>
        </div>

        <div class="info">
            <h3>🔧 Dépannage</h3>
            <p><strong>Si l'envoi échoue :</strong></p>
            <ul>
                <li>Vérifiez que la 2FA est activée sur le compte Gmail</li>
                <li>Vérifiez que le mot de passe d'application est correct</li>
                <li>Vérifiez la connexion internet</li>
                <li>Consultez les logs PHP pour plus de détails</li>
            </ul>
        </div>
    </div>
</body>
</html>
