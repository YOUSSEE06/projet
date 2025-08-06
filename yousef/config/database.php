<?php
/**
 * Configuration de la base de données
 * Fichier de connexion sécurisé pour l'application de gestion des stages
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_stages');
define('DB_USER', 'root');        // À modifier selon votre configuration
define('DB_PASS', '');            // À modifier selon votre configuration
define('DB_CHARSET', 'utf8mb4');

// Configuration des options PDO
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
]);

/**
 * Classe Database pour la gestion de la connexion
 */
class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Constructeur privé pour le pattern Singleton
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            // Log de l'erreur (en production, ne pas afficher les détails)
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            throw new Exception("Impossible de se connecter à la base de données");
        }
    }
    
    /**
     * Obtient l'instance unique de la base de données (Singleton)
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtient la connexion PDO
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Exécute une requête préparée
     * @param string $sql Requête SQL
     * @param array $params Paramètres de la requête
     * @return PDOStatement
     */
    public function prepare($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erreur SQL: " . $e->getMessage());
            throw new Exception("Erreur lors de l'exécution de la requête");
        }
    }
    
    /**
     * Exécute une requête et retourne tous les résultats
     * @param string $sql Requête SQL
     * @param array $params Paramètres de la requête
     * @return array
     */
    public function query($sql, $params = []) {
        $stmt = $this->prepare($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Exécute une requête et retourne une seule ligne
     * @param string $sql Requête SQL
     * @param array $params Paramètres de la requête
     * @return array|false
     */
    public function queryOne($sql, $params = []) {
        $stmt = $this->prepare($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Exécute une requête d'insertion, mise à jour ou suppression
     * @param string $sql Requête SQL
     * @param array $params Paramètres de la requête
     * @return int Nombre de lignes affectées
     */
    public function execute($sql, $params = []) {
        $stmt = $this->prepare($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Obtient l'ID de la dernière insertion
     * @return string
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Démarre une transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Valide une transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Annule une transaction
     */
    public function rollback() {
        return $this->connection->rollback();
    }
    
    /**
     * Vérifie si une transaction est en cours
     * @return bool
     */
    public function inTransaction() {
        return $this->connection->inTransaction();
    }
    
    /**
     * Ferme la connexion
     */
    public function close() {
        $this->connection = null;
        self::$instance = null;
    }
}

/**
 * Fonction utilitaire pour obtenir une instance de la base de données
 * @return Database
 */
function getDB() {
    return Database::getInstance();
}

/**
 * Fonction utilitaire pour échapper les caractères spéciaux
 * @param string $string Chaîne à échapper
 * @return string
 */
function escapeString($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Fonction utilitaire pour valider un email
 * @param string $email Email à valider
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Fonction utilitaire pour valider une date
 * @param string $date Date à valider (format Y-m-d)
 * @return bool
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Fonction utilitaire pour valider une moyenne
 * @param float $moyenne Moyenne à valider
 * @return bool
 */
function validateMoyenne($moyenne) {
    return is_numeric($moyenne) && $moyenne >= 0 && $moyenne <= 20;
}

// Test de connexion (optionnel, pour le débogage)
if (defined('TEST_CONNECTION') && TEST_CONNECTION) {
    try {
        $db = getDB();
        echo "Connexion à la base de données réussie !";
    } catch (Exception $e) {
        echo "Erreur de connexion: " . $e->getMessage();
    }
}
?> 