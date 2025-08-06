/**
 * Application JavaScript - Gestion des Stages
 * Gère toutes les interactions côté client
 */

class StageApp {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.initValidation();
        this.initModals();
        this.initTabs();
        this.initNotifications();
    }

    /**
     * Lie les événements aux éléments DOM
     */
    bindEvents() {
        // Formulaires
        document.addEventListener('submit', (e) => this.handleFormSubmit(e));
        
        // Boutons de déconnexion
        document.querySelectorAll('.btn-deconnexion').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleDeconnexion(e));
        });

        // Boutons de tri
        document.querySelectorAll('.btn-tri').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleTri(e));
        });

        // Boutons d'action admin
        document.querySelectorAll('.btn-accepter').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleAccepter(e));
        });

        document.querySelectorAll('.btn-refuser').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleRefuser(e));
        });

        // Fermeture des modals
        document.querySelectorAll('.modal-close, .modal').forEach(element => {
            element.addEventListener('click', (e) => this.closeModal(e));
        });

        // Validation en temps réel
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', (e) => this.validateField(e.target));
            input.addEventListener('input', (e) => this.clearFieldError(e.target));
        });
    }

    /**
     * Initialise la validation des formulaires
     */
    initValidation() {
        this.validationRules = {
            nom: {
                required: true,
                minLength: 2,
                maxLength: 100,
                pattern: /^[a-zA-ZÀ-ÿ\s'-]+$/
            },
            prenom: {
                required: true,
                minLength: 2,
                maxLength: 100,
                pattern: /^[a-zA-ZÀ-ÿ\s'-]+$/
            },
            email: {
                required: true,
                pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/
            },
            filiere: {
                required: true,
                values: ['Génie Info', 'Génie Électrique', 'Technique de Management']
            },
            date_naissance: {
                required: true,
                custom: (value) => {
                    const date = new Date(value);
                    const today = new Date();
                    const age = today.getFullYear() - date.getFullYear();
                    return age >= 16 && age <= 100;
                }
            },
            etablissement: {
                required: true,
                maxLength: 255
            },
            moyenne: {
                required: true,
                min: 0,
                max: 20,
                pattern: /^\d+(\.\d{1,2})?$/
            },
            mot_de_passe: {
                required: true,
                minLength: 6
            },
            confirmation_mot_de_passe: {
                required: true,
                custom: (value) => {
                    const motDePasse = document.querySelector('[name="mot_de_passe"]')?.value;
                    return value === motDePasse;
                }
            }
        };
    }

    /**
     * Initialise les modals
     */
    initModals() {
        document.querySelectorAll('[data-modal]').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal');
                this.openModal(modalId);
            });
        });
    }

    /**
     * Initialise les onglets
     */
    initTabs() {
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = tab.getAttribute('data-tab');
                this.switchTab(targetId);
            });
        });
    }

    /**
     * Initialise les notifications
     */
    initNotifications() {
        // Auto-fermeture des alertes après 5 secondes
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Marquer les notifications comme lues
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', (e) => this.marquerNotificationLue(e));
        });
    }

    /**
     * Gère la soumission des formulaires
     */
    handleFormSubmit(e) {
        const form = e.target;
        const formData = new FormData(form);
        const action = form.getAttribute('data-action');

        // Validation côté client
        if (!this.validateForm(form)) {
            e.preventDefault();
            return false;
        }

        // Si c'est une soumission AJAX
        if (form.hasAttribute('data-ajax')) {
            e.preventDefault();
            this.submitAjax(form, action, formData);
        }
    }

    /**
     * Valide un formulaire complet
     */
    validateForm(form) {
        let isValid = true;
        const fields = form.querySelectorAll('.form-control');

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Valide un champ individuel
     */
    validateField(field) {
        const fieldName = field.name;
        const value = field.value.trim();
        const rules = this.validationRules[fieldName];

        if (!rules) return true;

        // Supprimer les erreurs précédentes
        this.clearFieldError(field);

        // Validation required
        if (rules.required && !value) {
            this.showFieldError(field, 'Ce champ est obligatoire');
            return false;
        }

        // Validation longueur minimale
        if (rules.minLength && value.length < rules.minLength) {
            this.showFieldError(field, `Minimum ${rules.minLength} caractères`);
            return false;
        }

        // Validation longueur maximale
        if (rules.maxLength && value.length > rules.maxLength) {
            this.showFieldError(field, `Maximum ${rules.maxLength} caractères`);
            return false;
        }

        // Validation pattern
        if (rules.pattern && !rules.pattern.test(value)) {
            this.showFieldError(field, 'Format invalide');
            return false;
        }

        // Validation valeurs autorisées
        if (rules.values && !rules.values.includes(value)) {
            this.showFieldError(field, 'Valeur non autorisée');
            return false;
        }

        // Validation min/max pour les nombres
        if (rules.min !== undefined && parseFloat(value) < rules.min) {
            this.showFieldError(field, `Valeur minimum : ${rules.min}`);
            return false;
        }

        if (rules.max !== undefined && parseFloat(value) > rules.max) {
            this.showFieldError(field, `Valeur maximum : ${rules.max}`);
            return false;
        }

        // Validation personnalisée
        if (rules.custom && !rules.custom(value)) {
            this.showFieldError(field, 'Valeur invalide');
            return false;
        }

        return true;
    }

    /**
     * Affiche une erreur de champ
     */
    showFieldError(field, message) {
        field.classList.add('error');
        
        // Supprimer l'erreur précédente
        const existingError = field.parentNode.querySelector('.form-error');
        if (existingError) {
            existingError.remove();
        }

        // Créer le message d'erreur
        const errorElement = document.createElement('div');
        errorElement.className = 'form-error';
        errorElement.textContent = message;
        field.parentNode.appendChild(errorElement);
    }

    /**
     * Efface l'erreur d'un champ
     */
    clearFieldError(field) {
        field.classList.remove('error');
        const errorElement = field.parentNode.querySelector('.form-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    /**
     * Soumet un formulaire en AJAX
     */
    async submitAjax(form, action, formData) {
        const submitBtn = form.querySelector('[type="submit"]');
        const originalText = submitBtn.textContent;

        try {
            // Afficher le spinner
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Envoi...';

            // Ajouter les données AJAX
            formData.append('ajax', 'true');
            formData.append('action', action);

            // Envoyer la requête
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert(result.message, 'success');
                
                // Redirection si spécifiée
                if (result.redirect) {
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1500);
                }
            } else {
                this.showAlert(result.message, 'danger');
            }

        } catch (error) {
            console.error('Erreur AJAX:', error);
            this.showAlert('Une erreur est survenue. Veuillez réessayer.', 'danger');
        } finally {
            // Restaurer le bouton
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    /**
     * Gère la déconnexion
     */
    async handleDeconnexion(e) {
        e.preventDefault();
        
        if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
            const formData = new FormData();
            formData.append('ajax', 'true');
            formData.append('action', 'deconnexion');

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success && result.redirect) {
                    window.location.href = result.redirect;
                }
            } catch (error) {
                console.error('Erreur de déconnexion:', error);
            }
        }
    }

    /**
     * Gère le tri des tableaux
     */
    handleTri(e) {
        e.preventDefault();
        const btn = e.target;
        const currentOrder = btn.getAttribute('data-order') || 'ASC';
        const newOrder = currentOrder === 'ASC' ? 'DESC' : 'ASC';
        const field = btn.getAttribute('data-field');

        // Mettre à jour l'URL
        const url = new URL(window.location);
        url.searchParams.set('tri', field);
        url.searchParams.set('ordre', newOrder);
        
        window.location.href = url.toString();
    }

    /**
     * Gère l'acceptation d'un étudiant
     */
    async handleAccepter(e) {
        e.preventDefault();
        const btn = e.target;
        const etudiantId = btn.getAttribute('data-id');
        const etudiantNom = btn.getAttribute('data-nom');

        // Ouvrir le modal d'acceptation
        const modal = document.getElementById('modal-accepter');
        if (modal) {
            modal.querySelector('[name="etudiant_id"]').value = etudiantId;
            modal.querySelector('.modal-title').textContent = `Accepter ${etudiantNom}`;
            this.openModal('modal-accepter');
        }
    }

    /**
     * Gère le refus d'un étudiant
     */
    async handleRefuser(e) {
        e.preventDefault();
        const btn = e.target;
        const etudiantId = btn.getAttribute('data-id');
        const etudiantNom = btn.getAttribute('data-nom');

        // Ouvrir le modal de refus
        const modal = document.getElementById('modal-refuser');
        if (modal) {
            modal.querySelector('[name="etudiant_id"]').value = etudiantId;
            modal.querySelector('.modal-title').textContent = `Refuser ${etudiantNom}`;
            this.openModal('modal-refuser');
        }
    }

    /**
     * Ouvre un modal
     */
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    /**
     * Ferme un modal
     */
    closeModal(e) {
        const modal = e.target.closest('.modal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    /**
     * Change d'onglet
     */
    switchTab(tabId) {
        // Masquer tous les contenus d'onglets
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Désactiver tous les onglets
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Activer l'onglet sélectionné
        const targetTab = document.querySelector(`[data-tab="${tabId}"]`);
        const targetContent = document.getElementById(tabId);

        if (targetTab && targetContent) {
            targetTab.classList.add('active');
            targetContent.classList.add('active');
        }
    }

    /**
     * Marque une notification comme lue
     */
    async marquerNotificationLue(e) {
        e.preventDefault();
        const item = e.target.closest('.notification-item');
        const notificationId = item.getAttribute('data-id');

        try {
            const formData = new FormData();
            formData.append('ajax', 'true');
            formData.append('action', 'marquer_notification_lue');
            formData.append('notification_id', notificationId);

            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                item.classList.add('lu');
            }
        } catch (error) {
            console.error('Erreur notification:', error);
        }
    }

    /**
     * Affiche une alerte
     */
    showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alert-container') || document.body;
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} fade-in`;
        alert.innerHTML = `
            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        `;

        alertContainer.appendChild(alert);

        // Auto-fermeture après 5 secondes
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    }

    /**
     * Confirme une action
     */
    confirmAction(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }

    /**
     * Formate une date
     */
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    /**
     * Formate un nombre
     */
    formatNumber(number, decimals = 2) {
        return parseFloat(number).toFixed(decimals);
    }

    /**
     * Effectue une recherche en temps réel
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Initialise la recherche en temps réel
     */
    initSearch() {
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            const debouncedSearch = this.debounce((value) => {
                this.performSearch(value);
            }, 300);

            searchInput.addEventListener('input', (e) => {
                debouncedSearch(e.target.value);
            });
        }
    }

    /**
     * Effectue une recherche
     */
    async performSearch(query) {
        try {
            const response = await fetch(`?search=${encodeURIComponent(query)}`);
            const html = await response.text();
            
            // Mettre à jour le contenu de la page
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('#search-results');
            const currentContent = document.querySelector('#search-results');
            
            if (newContent && currentContent) {
                currentContent.innerHTML = newContent.innerHTML;
            }
        } catch (error) {
            console.error('Erreur de recherche:', error);
        }
    }
}

// Initialisation de l'application
document.addEventListener('DOMContentLoaded', () => {
    window.app = new StageApp();
});

// Fonctions utilitaires globales
window.showAlert = (message, type) => {
    if (window.app) {
        window.app.showAlert(message, type);
    }
};

window.confirmAction = (message, callback) => {
    if (window.app) {
        window.app.confirmAction(message, callback);
    }
}; 