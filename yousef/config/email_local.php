<?php
/**
 * Configuration Email Locale - Solution Sans SMTP
 * Sauvegarde les emails dans des fichiers au lieu de les envoyer
 * Parfait pour développement et test
 */

class EmailConfigLocal {
    private $emailsDir;
    
    public function __construct() {
        // Créer le dossier pour sauvegarder les emails
        $this->emailsDir = __DIR__ . '/../emails_sent/';
        if (!file_exists($this->emailsDir)) {
            mkdir($this->emailsDir, 0777, true);
        }
    }
    
    /**
     * Envoie un email de notification d'acceptation de stage
     * @param string $emailEtudiant Email de l'étudiant
     * @param string $nomEtudiant Nom de l'étudiant
     * @param string $typeStage Type de stage accepté
     * @return bool Succès de l'opération
     */
    public function envoyerNotificationAcceptation($emailEtudiant, $nomEtudiant, $typeStage) {
        $sujet = 'Acceptation de votre demande de stage';
        $contenu = $this->getTemplateAcceptation($nomEtudiant, $typeStage);
        
        return $this->sauvegarderEmail($emailEtudiant, $nomEtudiant, $sujet, $contenu, 'acceptation');
    }
    
    /**
     * Envoie un email de notification de refus de stage
     * @param string $emailEtudiant Email de l'étudiant
     * @param string $nomEtudiant Nom de l'étudiant
     * @param string $typeStage Type de stage refusé
     * @param string $motifRefus Motif du refus
     * @return bool Succès de l'opération
     */
    public function envoyerNotificationRefus($emailEtudiant, $nomEtudiant, $typeStage, $motifRefus = '') {
        $sujet = 'Réponse à votre demande de stage';
        $contenu = $this->getTemplateRefus($nomEtudiant, $typeStage, $motifRefus);
        
        return $this->sauvegarderEmail($emailEtudiant, $nomEtudiant, $sujet, $contenu, 'refus');
    }
    
    /**
     * Envoie un email personnalisé
     * @param string $destinataire Email du destinataire
     * @param string $nomDestinataire Nom du destinataire
     * @param string $sujet Sujet de l'email
     * @param string $message Corps du message
     * @return bool Succès de l'opération
     */
    public function envoyerEmailPersonnalise($destinataire, $nomDestinataire, $sujet, $message) {
        $contenu = $this->getTemplatePersonnalise($nomDestinataire, $message);
        
        return $this->sauvegarderEmail($destinataire, $nomDestinataire, $sujet, $contenu, 'personnalise');
    }
    
    /**
     * Sauvegarde l'email dans un fichier
     * @param string $destinataire Email du destinataire
     * @param string $nomDestinataire Nom du destinataire
     * @param string $sujet Sujet de l'email
     * @param string $contenu Contenu HTML de l'email
     * @param string $type Type d'email (acceptation, refus, personnalise)
     * @return bool Succès de l'opération
     */
    private function sauvegarderEmail($destinataire, $nomDestinataire, $sujet, $contenu, $type) {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = $timestamp . '_' . $type . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $destinataire) . '.html';
            $filepath = $this->emailsDir . $filename;
            
            // Créer le contenu complet de l'email
            $emailComplet = $this->creerEmailComplet($destinataire, $nomDestinataire, $sujet, $contenu, $type);
            
            // Sauvegarder dans le fichier
            $resultat = file_put_contents($filepath, $emailComplet);
            
            if ($resultat !== false) {
                // Log de succès
                $this->logEmail($destinataire, $sujet, $type, 'SUCCESS', $filename);
                return true;
            } else {
                $this->logEmail($destinataire, $sujet, $type, 'ERROR', 'Impossible de sauvegarder le fichier');
                return false;
            }
            
        } catch (Exception $e) {
            $this->logEmail($destinataire, $sujet, $type, 'ERROR', $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crée le contenu complet de l'email avec métadonnées
     */
    private function creerEmailComplet($destinataire, $nomDestinataire, $sujet, $contenu, $type) {
        $metadata = [
            'timestamp' => date('Y-m-d H:i:s'),
            'destinataire' => $destinataire,
            'nom_destinataire' => $nomDestinataire,
            'sujet' => $sujet,
            'type' => $type,
            'expediteur' => 'Système de Gestion des Stages - UIZ',
            'status' => 'SIMULE (Email sauvegardé localement)'
        ];
        
        $html = "<!DOCTYPE html>\n<html lang='fr'>\n<head>\n";
        $html .= "<meta charset='UTF-8'>\n";
        $html .= "<title>" . htmlspecialchars($sujet) . "</title>\n";
        $html .= "<style>\n";
        $html .= "body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }\n";
        $html .= ".metadata { background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; }\n";
        $html .= ".email-content { background: white; padding: 20px; border-radius: 5px; }\n";
        $html .= "</style>\n";
        $html .= "</head>\n<body>\n";
        
        // Métadonnées
        $html .= "<div class='metadata'>\n";
        $html .= "<h3>📧 Métadonnées de l'Email</h3>\n";
        foreach ($metadata as $key => $value) {
            $html .= "<strong>" . ucfirst(str_replace('_', ' ', $key)) . ":</strong> " . htmlspecialchars($value) . "<br>\n";
        }
        $html .= "</div>\n";
        
        // Contenu de l'email
        $html .= "<div class='email-content'>\n";
        $html .= $contenu;
        $html .= "</div>\n";
        
        $html .= "</body>\n</html>";
        
        return $html;
    }
    
    /**
     * Log les opérations d'email
     */
    private function logEmail($destinataire, $sujet, $type, $status, $details) {
        $logFile = $this->emailsDir . 'email_log.txt';
        $logEntry = date('Y-m-d H:i:s') . " | $status | $type | $destinataire | $sujet | $details\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Retourne le nombre d'emails sauvegardés
     */
    public function getNombreEmailsEnvoyes() {
        $files = glob($this->emailsDir . '*.html');
        return count($files);
    }
    
    /**
     * Retourne la liste des emails sauvegardés
     */
    public function getListeEmails() {
        $files = glob($this->emailsDir . '*.html');
        $emails = [];
        
        foreach ($files as $file) {
            $emails[] = [
                'filename' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'date' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
        
        // Trier par date (plus récent en premier)
        usort($emails, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });
        
        return $emails;
    }
    
    /**
     * Template HTML pour email d'acceptation
     */
    private function getTemplateAcceptation($nomEtudiant, $typeStage) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #28a745; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h2 style='margin: 0;'>🎉 Félicitations !</h2>
            </div>
            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 0 0 10px 10px;'>
                <p>Bonjour <strong>{$nomEtudiant}</strong>,</p>
                <p>Nous avons le plaisir de vous informer que votre demande de stage <strong>{$typeStage}</strong> a été acceptée.</p>
                <p>Vous recevrez prochainement plus d'informations concernant les modalités et le déroulement de votre stage.</p>
                <p>Félicitations et bienvenue dans notre programme de stages !</p>
                <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
                    <p style='color: #888; font-size: 14px; margin: 0;'>
                        Système de Gestion des Stages<br>
                        Université Ibn Zohr (UIZ)<br>
                        <em>Email généré automatiquement</em>
                    </p>
                </div>
            </div>
        </div>";
    }
    
    /**
     * Template HTML pour email de refus
     */
    private function getTemplateRefus($nomEtudiant, $typeStage, $motifRefus) {
        $motifSection = $motifRefus ? "<p><strong>Motif :</strong> {$motifRefus}</p>" : '';
        
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h2 style='margin: 0;'>📧 Réponse à votre demande de stage</h2>
            </div>
            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 0 0 10px 10px;'>
                <p>Bonjour <strong>{$nomEtudiant}</strong>,</p>
                <p>Nous vous remercions pour votre intérêt concernant le stage <strong>{$typeStage}</strong>.</p>
                <p>Malheureusement, nous ne pouvons pas donner suite à votre demande pour le moment.</p>
                {$motifSection}
                <p>Nous vous encourageons à postuler à nouveau lors des prochaines sessions.</p>
                <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
                    <p style='color: #888; font-size: 14px; margin: 0;'>
                        Système de Gestion des Stages<br>
                        Université Ibn Zohr (UIZ)<br>
                        <em>Email généré automatiquement</em>
                    </p>
                </div>
            </div>
        </div>";
    }
    
    /**
     * Template HTML pour email personnalisé
     */
    private function getTemplatePersonnalise($nomDestinataire, $message) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #007bff; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h2 style='margin: 0;'>📧 Message du Système de Gestion des Stages</h2>
            </div>
            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 0 0 10px 10px;'>
                <p>Bonjour <strong>{$nomDestinataire}</strong>,</p>
                <div>{$message}</div>
                <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
                    <p style='color: #888; font-size: 14px; margin: 0;'>
                        Système de Gestion des Stages<br>
                        Université Ibn Zohr (UIZ)<br>
                        <em>Email généré automatiquement</em>
                    </p>
                </div>
            </div>
        </div>";
    }
}
