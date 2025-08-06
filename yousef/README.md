# Gestion des Stages - Application Web

Une application web complète pour la gestion des candidatures de stages étudiants, développée en PHP avec une interface moderne et responsive.

## 🚀 Fonctionnalités

### Phase 1 - Espace Étudiant
- **Inscription** : Formulaire complet avec validation côté client et serveur
- **Connexion** : Authentification avec mots de passe en clair
- **Validation** : Vérification des données avec messages d'erreur personnalisés
- **Base de données** : Structure complète avec 7 tables principales
- **Sécurité** : Sessions sécurisées et gestion des statuts

### Phase 2 - Espace Administrateur
- **Interface admin** : Tableau de bord avec onglets pour chaque statut
- **Gestion des candidatures** : Boutons accepter/refuser avec modales
- **Tri et filtrage** : Options de tri par nom, moyenne, date d'inscription
- **Transfert de données** : Déplacement automatique entre les tables
- **Notifications** : Système de notifications automatiques

## 🛠️ Technologies Utilisées

- **Backend** : PHP 8.0+
- **Frontend** : HTML5, CSS3, JavaScript (ES6+)
- **Base de données** : MySQL 8.0+
- **Architecture** : MVC (Modèle-Vue-Contrôleur)
- **Interface** : Design responsive et moderne
- **Validation** : Client-side et server-side
- **AJAX** : Requêtes asynchrones pour une meilleure UX

## 📁 Structure du Projet

```
gestion-stages/
├── public/                 # Fichiers publics accessibles
│   ├── index.php          # Page d'accueil avec formulaires
│   ├── dashboard.php      # Tableau de bord étudiant accepté
│   ├── attente.php        # Page d'attente pour candidatures en cours
│   ├── refuse.php         # Page de refus
│   ├── notifications.php  # Gestion des notifications
│   ├── profil.php         # Modification du profil
│   ├── admin/             # Espace administrateur
│   │   ├── login.php      # Connexion admin
│   │   └── dashboard.php  # Tableau de bord admin
│   ├── css/               # Styles CSS
│   │   └── style.css      # Feuille de style principale
│   └── js/                # Scripts JavaScript
│       └── app.js         # Script principal
├── backend/               # Logique métier
│   ├── controllers/       # Contrôleurs
│   │   ├── EtudiantController.php
│   │   └── AdminController.php
│   └── models/            # Modèles de données
│       ├── Etudiant.php
│       └── Administrateur.php
├── config/                # Configuration
│   └── database.php       # Connexion à la base de données
├── database/              # Scripts de base de données
│   └── schema.sql         # Schéma complet de la base
└── README.md              # Documentation
```

## 🗄️ Base de Données

### Tables Principales
1. **etudiants** : Informations des étudiants
2. **authentification** : Identifiants de connexion
3. **file_attente** : Candidatures en attente de traitement
4. **stages_acceptes** : Étudiants acceptés
5. **etudiants_refuses** : Étudiants refusés
6. **notifications** : Historique des notifications
7. **administrateurs** : Comptes administrateurs

### Relations et Intégrité
- Clés étrangères pour maintenir la cohérence
- Triggers automatiques pour la gestion des statuts
- Vues pour faciliter les requêtes fréquentes
- Index optimisés pour les performances

## 🔧 Installation

### Prérequis
- PHP 8.0 ou supérieur
- MySQL 8.0 ou supérieur
- Serveur web (Apache/Nginx)
- Extensions PHP : PDO, PDO_MySQL

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone [url-du-repo]
   cd gestion-stages
   ```

2. **Configurer la base de données**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

3. **Configurer la connexion**
   Éditer `config/database.php` avec vos paramètres :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'gestion_stages');
   define('DB_USER', 'votre_utilisateur');
   define('DB_PASS', 'votre_mot_de_passe');
   ```

4. **Configurer le serveur web**
   Pointer le document root vers le dossier `public/`

5. **Tester l'application**
   - Accéder à `http://localhost/`
   - Compte admin par défaut : `admin@stages.com` / `admin123`

## 🔐 Sécurité

- **Sessions** : Gestion sécurisée des sessions utilisateur
- **Validation** : Validation stricte des données d'entrée
- **Protection SQL** : Requêtes préparées pour éviter les injections
- **Mots de passe** : Stockage en clair (pour des raisons de simplicité)
- **Authentification** : Vérification des droits d'accès

## 📱 Interface Utilisateur

### Design Responsive
- Adaptation automatique aux différentes tailles d'écran
- Interface mobile-friendly
- Navigation intuitive

### Composants UI
- **Formulaires** : Validation en temps réel
- **Tableaux** : Tri et filtrage dynamiques
- **Modales** : Actions contextuelles
- **Alertes** : Notifications utilisateur
- **Badges** : Indicateurs de statut

### Interactions
- **AJAX** : Soumission de formulaires sans rechargement
- **Animations** : Transitions fluides
- **Feedback** : Retours visuels immédiats

## 🚀 Fonctionnalités Avancées

### Espace Étudiant
- Inscription avec validation complète
- Connexion sécurisée
- Suivi du statut de candidature
- Gestion des notifications
- Modification du profil

### Espace Administrateur
- Vue d'ensemble des candidatures
- Tri et filtrage avancés
- Actions d'acceptation/refus
- Gestion des notifications
- Statistiques en temps réel

## 🔄 Workflow

1. **Inscription étudiant** → Validation → File d'attente
2. **Examen admin** → Décision (accepter/refuser)
3. **Notification** → Mise à jour du statut
4. **Suivi** → Accès aux informations appropriées

## 📊 Statistiques

L'application fournit des statistiques détaillées :
- Nombre total d'étudiants
- Répartition par statut
- Moyennes par filière
- Tendances d'inscription

## 🛠️ Maintenance

### Logs
- Erreurs PHP dans les logs du serveur
- Requêtes SQL dans les logs MySQL
- Actions utilisateur tracées

### Sauvegarde
- Script de sauvegarde de la base de données
- Sauvegarde automatique recommandée

## 📝 Notes de Développement

### Architecture
- Séparation claire des responsabilités (MVC)
- Code modulaire et réutilisable
- Documentation complète

### Performance
- Requêtes SQL optimisées
- Index appropriés
- Mise en cache des données statiques

### Extensibilité
- Structure modulaire
- Interfaces claires
- Facilité d'ajout de fonctionnalités

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature
3. Commiter les changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

## 📞 Support

Pour toute question ou problème :
- Ouvrir une issue sur GitHub
- Contacter l'équipe de développement
- Consulter la documentation technique

---

**Développé avec ❤️ pour la gestion des stages étudiants** 