<?php
/**
 * Configuration Email avec SendGrid
 * Envoie de VRAIS emails aux adresses Gmail et autres
 * Service professionnel - 100 emails/jour gratuits
 */

class EmailConfigSendGrid {
    private $apiKey;
    private $fromEmail;
    private $fromName;
    
    // Configuration SendGrid - À personnaliser
    private const SENDGRID_API_KEY = 'VOTRE_CLE_API_SENDGRID'; // À remplacer
    private const FROM_EMAIL = 'youssef.oularbi628@outlook.fr'; // Votre email vérifié
    private const FROM_NAME = 'Système de Gestion des Stages - UIZ';
    
    public function __construct() {
        $this->apiKey = self::SENDGRID_API_KEY;
        $this->fromEmail = self::FROM_EMAIL;
        $this->fromName = self::FROM_NAME;
    }
    
    /**
     * Envoie un email de notification d'acceptation de stage
     * @param string $emailEtudiant Email de l'étudiant
     * @param string $nomEtudiant Nom de l'étudiant
     * @param string $typeStage Type de stage accepté
     * @return bool Succès de l'envoi
     */
    public function envoyerNotificationAcceptation($emailEtudiant, $nomEtudiant, $typeStage) {
        $sujet = 'Acceptation de votre demande de stage';
        $contenu = $this->getTemplateAcceptation($nomEtudiant, $typeStage);
        
        return $this->envoyerEmail($emailEtudiant, $nomEtudiant, $sujet, $contenu);
    }
    
    /**
     * Envoie un email de notification de refus de stage
     * @param string $emailEtudiant Email de l'étudiant
     * @param string $nomEtudiant Nom de l'étudiant
     * @param string $typeStage Type de stage refusé
     * @param string $motifRefus Motif du refus
     * @return bool Succès de l'envoi
     */
    public function envoyerNotificationRefus($emailEtudiant, $nomEtudiant, $typeStage, $motifRefus = '') {
        $sujet = 'Réponse à votre demande de stage';
        $contenu = $this->getTemplateRefus($nomEtudiant, $typeStage, $motifRefus);
        
        return $this->envoyerEmail($emailEtudiant, $nomEtudiant, $sujet, $contenu);
    }
    
    /**
     * Envoie un email personnalisé
     * @param string $destinataire Email du destinataire
     * @param string $nomDestinataire Nom du destinataire
     * @param string $sujet Sujet de l'email
     * @param string $message Corps du message
     * @return bool Succès de l'envoi
     */
    public function envoyerEmailPersonnalise($destinataire, $nomDestinataire, $sujet, $message) {
        $contenu = $this->getTemplatePersonnalise($nomDestinataire, $message);
        
        return $this->envoyerEmail($destinataire, $nomDestinataire, $sujet, $contenu);
    }
    
    /**
     * Envoie un email via l'API SendGrid
     * @param string $destinataire Email du destinataire
     * @param string $nomDestinataire Nom du destinataire
     * @param string $sujet Sujet de l'email
     * @param string $contenuHtml Contenu HTML de l'email
     * @return bool Succès de l'envoi
     */
    private function envoyerEmail($destinataire, $nomDestinataire, $sujet, $contenuHtml) {
        try {
            // Données pour l'API SendGrid
            $data = [
                'personalizations' => [
                    [
                        'to' => [
                            [
                                'email' => $destinataire,
                                'name' => $nomDestinataire
                            ]
                        ],
                        'subject' => $sujet
                    ]
                ],
                'from' => [
                    'email' => $this->fromEmail,
                    'name' => $this->fromName
                ],
                'content' => [
                    [
                        'type' => 'text/html',
                        'value' => $contenuHtml
                    ]
                ]
            ];
            
            // Appel à l'API SendGrid
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Vérifier le succès
            if ($httpCode >= 200 && $httpCode < 300) {
                $this->logEmail($destinataire, $sujet, 'SUCCESS', 'Email envoyé via SendGrid');
                return true;
            } else {
                $this->logEmail($destinataire, $sujet, 'ERROR', "HTTP $httpCode: $response");
                return false;
            }
            
        } catch (Exception $e) {
            $this->logEmail($destinataire, $sujet, 'ERROR', $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log les opérations d'email
     */
    private function logEmail($destinataire, $sujet, $status, $details) {
        $logDir = __DIR__ . '/../emails_sent/';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logFile = $logDir . 'sendgrid_log.txt';
        $logEntry = date('Y-m-d H:i:s') . " | $status | $destinataire | $sujet | $details\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Template HTML pour email d'acceptation
     */
    private function getTemplateAcceptation($nomEtudiant, $typeStage) {
        return "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #28a745; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 20px; background-color: #f8f9fa; border-radius: 0 0 10px 10px; }
                .footer { padding: 10px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🎉 Félicitations !</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$nomEtudiant}</strong>,</p>
                    <p>Nous avons le plaisir de vous informer que votre demande de stage <strong>{$typeStage}</strong> a été acceptée.</p>
                    <p>Vous recevrez prochainement plus d'informations concernant les modalités et le déroulement de votre stage.</p>
                    <p>Félicitations et bienvenue dans notre programme de stages !</p>
                </div>
                <div class='footer'>
                    <p>Système de Gestion des Stages - UIZ<br>
                    Email envoyé via SendGrid</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Template HTML pour email de refus
     */
    private function getTemplateRefus($nomEtudiant, $typeStage, $motifRefus) {
        $motifSection = $motifRefus ? "<p><strong>Motif :</strong> {$motifRefus}</p>" : '';
        
        return "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 20px; background-color: #f8f9fa; border-radius: 0 0 10px 10px; }
                .footer { padding: 10px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>📧 Réponse à votre demande de stage</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$nomEtudiant}</strong>,</p>
                    <p>Nous vous remercions pour votre intérêt concernant le stage <strong>{$typeStage}</strong>.</p>
                    <p>Malheureusement, nous ne pouvons pas donner suite à votre demande pour le moment.</p>
                    {$motifSection}
                    <p>Nous vous encourageons à postuler à nouveau lors des prochaines sessions.</p>
                </div>
                <div class='footer'>
                    <p>Système de Gestion des Stages - UIZ<br>
                    Email envoyé via SendGrid</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Template HTML pour email personnalisé
     */
    private function getTemplatePersonnalise($nomDestinataire, $message) {
        return "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 20px; background-color: #f8f9fa; border-radius: 0 0 10px 10px; }
                .footer { padding: 10px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>📧 Message du Système de Gestion des Stages</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$nomDestinataire}</strong>,</p>
                    <div>{$message}</div>
                </div>
                <div class='footer'>
                    <p>Système de Gestion des Stages - UIZ<br>
                    Email envoyé via SendGrid</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
