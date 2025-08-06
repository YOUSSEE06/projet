<?php
/**
 * Test Email d'Inscription - Email de Bienvenue
 * Test de l'envoi automatique d'email lors de l'inscription
 */

require_once 'config/email.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email d'Inscription - Bienvenue</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #007bff 0%, #6f42c1 100%);
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
            border-bottom: 3px solid #007bff;
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
            background: #007bff;
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
            background: #0056b3;
        }
        .inscription-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
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
        .form-group input, .form-group select {
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
            <h1>🎉 Test Email d'Inscription</h1>
            <div class="inscription-badge">📧 Email de Bienvenue Automatique</div>
            <p>Test de l'envoi d'email lors de l'inscription sur la plateforme</p>
        </div>

        <div class="info">
            <h3>📋 Fonctionnement</h3>
            <p><strong>Maintenant, quand un étudiant s'inscrit sur votre plateforme :</strong></p>
            <ul>
                <li>✅ <strong>Inscription réussie</strong> → Email de bienvenue envoyé automatiquement</li>
                <li>📧 <strong>Email professionnel</strong> avec informations du compte</li>
                <li>🎯 <strong>Instructions</strong> pour les prochaines étapes</li>
                <li>📊 <strong>Logs automatiques</strong> de tous les envois</li>
            </ul>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emailEtudiant = $_POST['email_etudiant'] ?? '';
            $nomEtudiant = $_POST['nom_etudiant'] ?? '';
            $filiere = $_POST['filiere'] ?? '';
            
            if ($emailEtudiant && $nomEtudiant && $filiere) {
                echo "<h2>🔄 Test d'Envoi d'Email de Bienvenue...</h2>";
                
                try {
                    $emailConfig = new EmailConfig();
                    
                    $resultat = $emailConfig->envoyerEmailBienvenue(
                        $emailEtudiant, 
                        $nomEtudiant, 
                        $filiere
                    );
                    
                    if ($resultat) {
                        echo "<div class='success'>";
                        echo "<h3>✅ EMAIL DE BIENVENUE ENVOYÉ AVEC SUCCÈS !</h3>";
                        echo "<p><strong>📧 Destinataire:</strong> $emailEtudiant</p>";
                        echo "<p><strong>👤 Nom:</strong> $nomEtudiant</p>";
                        echo "<p><strong>🎓 Filière:</strong> $filiere</p>";
                        echo "<p><strong>⏰ Heure:</strong> " . date('Y-m-d H:i:s') . "</p>";
                        echo "<hr>";
                        echo "<h4>🎉 L'email de bienvenue a été envoyé !</h4>";
                        echo "<p>L'étudiant devrait recevoir l'email dans sa boîte Gmail avec :</p>";
                        echo "<ul>";
                        echo "<li>🎉 Message de bienvenue personnalisé</li>";
                        echo "<li>📋 Informations de son compte (filière, date d'inscription)</li>";
                        echo "<li>🚀 Instructions pour les prochaines étapes</li>";
                        echo "<li>📧 Rappel sur les notifications automatiques</li>";
                        echo "</ul>";
                        echo "</div>";
                    } else {
                        echo "<div class='error'>";
                        echo "<h3>❌ ÉCHEC DE L'ENVOI</h3>";
                        echo "<p>L'email de bienvenue n'a pas pu être envoyé. Vérifiez :</p>";
                        echo "<ul>";
                        echo "<li>La connexion internet</li>";
                        echo "<li>Les paramètres Gmail SMTP</li>";
                        echo "<li>Le mot de passe d'application Gmail</li>";
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

        <h2>📧 Test Email de Bienvenue</h2>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email_etudiant">📧 Email de l'Étudiant:</label>
                <input type="email" id="email_etudiant" name="email_etudiant" 
                       placeholder="etudiant@gmail.com" required>
                <small>Email où sera envoyé l'email de bienvenue</small>
            </div>
            
            <div class="form-group">
                <label for="nom_etudiant">👤 Nom Complet de l'Étudiant:</label>
                <input type="text" id="nom_etudiant" name="nom_etudiant" 
                       placeholder="Prénom Nom" required>
            </div>
            
            <div class="form-group">
                <label for="filiere">🎓 Filière d'Études:</label>
                <select id="filiere" name="filiere" required>
                    <option value="">Choisir une filière</option>
                    <option value="Informatique">Informatique</option>
                    <option value="Génie Civil">Génie Civil</option>
                    <option value="Électronique">Électronique</option>
                    <option value="Mécanique">Mécanique</option>
                    <option value="Gestion">Gestion</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Finance">Finance</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>
            
            <div style="text-align: center;">
                <button type="submit" class="btn">🚀 Envoyer Email de Bienvenue</button>
            </div>
        </form>

        <div class="success">
            <h3>✅ Intégration Automatique</h3>
            <p><strong>Cette fonctionnalité est maintenant intégrée dans votre système :</strong></p>
            <ul>
                <li>📝 <strong>Inscription d'étudiant</strong> → Email de bienvenue automatique</li>
                <li>✅ <strong>Acceptation de stage</strong> → Email d'acceptation automatique</li>
                <li>❌ <strong>Refus de stage</strong> → Email de refus automatique</li>
                <li>📊 <strong>Tous les emails</strong> sont envoyés aux vraies adresses Gmail</li>
            </ul>
        </div>

        <div class="info">
            <h3>🔧 Configuration Actuelle</h3>
            <div class="config-info">
                <strong>Email SMTP:</strong> youssefoularbi630@gmail.com<br>
                <strong>Serveur:</strong> smtp.gmail.com:587<br>
                <strong>Sécurité:</strong> TLS avec mot de passe d'application<br>
                <strong>Status:</strong> ✅ Configuré et fonctionnel
            </div>
        </div>

        <div class="info">
            <h3>📋 Contenu de l'Email de Bienvenue</h3>
            <p><strong>L'email de bienvenue contient :</strong></p>
            <ul>
                <li>🎉 <strong>Message de bienvenue</strong> personnalisé avec le nom</li>
                <li>✅ <strong>Confirmation d'inscription</strong> réussie</li>
                <li>📋 <strong>Informations du compte</strong> (filière, date d'inscription)</li>
                <li>🚀 <strong>Prochaines étapes</strong> à suivre</li>
                <li>📧 <strong>Information sur les notifications</strong> automatiques</li>
                <li>🎨 <strong>Design professionnel</strong> avec couleurs UIZ</li>
            </ul>
        </div>
    </div>
</body>
</html>
