<?php
/**
 * Configuration Email avec PHPMailer - UIZ
 * Classe pour gérer l'envoi d'emails dans l'application de gestion des stages
 * Configuré pour : youssef.oularbi.95@uiz.ac.ma
 */

require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailConfig {
    private $mail;
    
    // Configuration SMTP - Gmail avec mot de passe d'application
    private const SMTP_HOST = 'smtp.gmail.com';
    private const SMTP_PORT = 587;
    private const SMTP_USERNAME = 'youssefoularbi630@gmail.com'; // Votre email Gmail
    private const SMTP_PASSWORD = 'jizs hjnq wpgq vxry'; // Mot de passe d'application Gmail
    private const FROM_EMAIL = 'youssefoularbi630@gmail.com';
    private const FROM_NAME = 'Système de Gestion des Stages - UIZ';
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configureMailer();
    }
    
    /**
     * Configure les paramètres SMTP de PHPMailer
     */
    private function configureMailer() {
        try {
            // Configuration du serveur
            $this->mail->isSMTP();
            $this->mail->Host = self::SMTP_HOST;
            $this->mail->SMTPAuth = true;
            $this->mail->Username = self::SMTP_USERNAME;
            $this->mail->Password = self::SMTP_PASSWORD;
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = self::SMTP_PORT;
            
            // Configuration de l'expéditeur
            $this->mail->setFrom(self::FROM_EMAIL, self::FROM_NAME);
            
            // Configuration de l'encodage
            $this->mail->CharSet = 'UTF-8';
            $this->mail->isHTML(true);
            
        } catch (Exception $e) {
            error_log("Erreur configuration PHPMailer: " . $e->getMessage());
            throw new Exception("Impossible de configurer le service email");
        }
    }
    
    /**
     * Envoie un email de notification d'acceptation de stage
     * @param string $emailEtudiant Email de l'étudiant
     * @param string $nomEtudiant Nom de l'étudiant
     * @param string $typeStage Type de stage accepté
     * @return bool Succès de l'envoi
     */
    public function envoyerNotificationAcceptation($emailEtudiant, $nomEtudiant, $typeStage) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($emailEtudiant, $nomEtudiant);
            
            $this->mail->Subject = 'Acceptation de votre demande de stage';
            
            $body = $this->getTemplateAcceptation($nomEtudiant, $typeStage);
            $this->mail->Body = $body;
            
            return $this->mail->send();
            
        } catch (Exception $e) {
            error_log("Erreur envoi email acceptation: " . $e->getMessage());
            return false;
        }
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
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($emailEtudiant, $nomEtudiant);
            
            $this->mail->Subject = 'Réponse à votre demande de stage';
            
            $body = $this->getTemplateRefus($nomEtudiant, $typeStage, $motifRefus);
            $this->mail->Body = $body;
            
            return $this->mail->send();
            
        } catch (Exception $e) {
            error_log("Erreur envoi email refus: " . $e->getMessage());
            return false;
        }
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
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($destinataire, $nomDestinataire);
            
            $this->mail->Subject = $sujet;
            $this->mail->Body = $this->getTemplatePersonnalise($nomDestinataire, $message);
            
            return $this->mail->send();
            
        } catch (Exception $e) {
            error_log("Erreur envoi email personnalisé: " . $e->getMessage());
            return false;
        }
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
                .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f8f9fa; }
                .footer { padding: 10px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Félicitations !</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$nomEtudiant}</strong>,</p>
                    <p>Nous avons le plaisir de vous informer que votre demande de stage <strong>{$typeStage}</strong> a été acceptée.</p>
                    <p>Vous recevrez prochainement plus d'informations concernant les modalités et le déroulement de votre stage.</p>
                    <p>Félicitations et bienvenue dans notre programme de stages !</p>
                </div>
                <div class='footer'>
                    <p>Système de Gestion des Stages<br>
                    Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
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
                .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f8f9fa; }
                .footer { padding: 10px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Réponse à votre demande de stage</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$nomEtudiant}</strong>,</p>
                    <p>Nous vous remercions pour votre intérêt concernant le stage <strong>{$typeStage}</strong>.</p>
                    <p>Malheureusement, nous ne pouvons pas donner suite à votre demande pour le moment.</p>
                    {$motifSection}
                    <p>Nous vous encourageons à postuler à nouveau lors des prochaines sessions.</p>
                </div>
                <div class='footer'>
                    <p>Système de Gestion des Stages<br>
                    Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
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
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f8f9fa; }
                .footer { padding: 10px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Message du Système de Gestion des Stages</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$nomDestinataire}</strong>,</p>
                    <div>{$message}</div>
                </div>
                <div class='footer'>
                    <p>Système de Gestion des Stages<br>
                    Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Envoie un email de bienvenue lors de l'inscription
     * @param string $emailEtudiant Email de l'étudiant
     * @param string $nomEtudiant Nom complet de l'étudiant
     * @param string $filiere Filière d'études
     * @return bool Succès de l'envoi
     */
    public function envoyerEmailBienvenue($emailEtudiant, $nomEtudiant, $filiere) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($emailEtudiant, $nomEtudiant);
            
            $this->mail->Subject = 'Bienvenue sur la plateforme de gestion des stages - UIZ';
            $this->mail->Body = $this->getTemplateBienvenue($nomEtudiant, $filiere);
            
            return $this->mail->send();
            
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email de bienvenue: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Template HTML pour email de bienvenue
     */
    private function getTemplateBienvenue($nomEtudiant, $filiere) {
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
                .welcome-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px; margin: 15px 0; text-align: center; }
                .info-box { background: #e9ecef; padding: 15px; border-radius: 8px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🎉 Bienvenue sur notre plateforme !</h2>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$nomEtudiant}</strong>,</p>
                    
                    <div class='welcome-box'>
                        <h3>✅ Inscription Réussie !</h3>
                        <p>Votre compte a été créé avec succès</p>
                    </div>
                    
                    <p>Nous sommes ravis de vous accueillir sur la <strong>Plateforme de Gestion des Stages de l'UIZ</strong>.</p>
                    
                    <div class='info-box'>
                        <h4>📋 Informations de votre compte :</h4>
                        <p><strong>Filière :</strong> {$filiere}</p>
                        <p><strong>Statut :</strong> Étudiant inscrit</p>
                        <p><strong>Date d'inscription :</strong> " . date('d/m/Y à H:i') . "</p>
                    </div>
                    
                    <h4>🚀 Prochaines étapes :</h4>
                    <ul>
                        <li>✅ Connectez-vous à votre espace étudiant</li>
                        <li>📝 Complétez votre profil si nécessaire</li>
                        <li>📋 Consultez les offres de stage disponibles</li>
                        <li>📧 Surveillez vos emails pour les notifications</li>
                    </ul>
                    
                    <p><strong>Important :</strong> Vous recevrez des notifications par email concernant l'état de vos candidatures (acceptation, refus, etc.).</p>
                    
                    <p>Si vous avez des questions, n'hésitez pas à contacter l'administration.</p>
                    
                    <p>Bonne chance dans vos démarches de stage !</p>
                </div>
                <div class='footer'>
                    <p>Système de Gestion des Stages - UIZ<br>
                    Email envoyé automatiquement lors de votre inscription</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
