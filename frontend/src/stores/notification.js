/**
 * ============================================================
 * Store Pinia — Notifications (notification.js)
 * Yass Digital Lab — Frontend Vue 3
 * ============================================================
 * Gère le centre de notifications de l'espace client :
 *   - Liste des notifications (commandes, devis, promotions, système)
 *   - Compteur de notifications non lues (badge rouge)
 *   - Persistance dans localStorage (les notifs survivent au rechargement)
 *   - Ajout dynamique de nouvelles notifications
 *   - Marquage individuel ou global comme "lues"
 *   - Suppression de toutes les notifications
 *
 * Types de notifications :
 *   'order'  → Confirmation de commande
 *   'quote'  → Demande de devis
 *   'promo'  → Code promo / offre spéciale
 *   'system' → Notification système générique
 *
 * Utilisé dans : NotificationCenter.vue, App.vue
 * ============================================================
 */

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useNotificationStore = defineStore('notifications', () => {

  // -------------------------------------------------------
  // État initial — chargé depuis localStorage si disponible
  // Sinon, notifications de démonstration par défaut
  // -------------------------------------------------------
  const notifications = ref(JSON.parse(localStorage.getItem('user_notifications')) || [
    {
      id: 1,
      type: 'order',                                    // Type = commande
      title: 'Commande #FA-000001 Validée',
      desc: 'Votre paiement a été accepté et vos fichiers sont disponibles au téléchargement.',
      time: 'Il y a 5 min',
      read: false,                                       // Non lue → affichée en badge
      link: '/dashboard'
    },
    {
      id: 2,
      type: 'quote',                                    // Type = devis
      title: 'Nouvelle Demande de Devis',
      desc: 'Une proposition pour "Création de site web sur mesure" est en cours de traitement.',
      time: 'Il y a 1h',
      read: false,
      link: '/admin'
    },
    {
      id: 3,
      type: 'promo',                                    // Type = promotion
      title: 'Code Promo Exclusif : YASS20',
      desc: 'Bénéficiez de 20% de réduction immédiate sur tous nos templates SaaS et packs IA.',
      time: 'Hier',
      read: true,                                        // Déjà lue → pas de badge
      link: '/products'
    }
  ]);

  /**
   * Nombre de notifications non lues.
   * Affiché comme badge rouge sur l'icône cloche dans la navbar.
   *
   * @type {import('vue').ComputedRef<number>}
   */
  const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

  /**
   * Sauvegarde l'état actuel des notifications dans localStorage.
   * Appelé après chaque modification pour garantir la persistance.
   */
  const saveToStorage = () => {
    localStorage.setItem('user_notifications', JSON.stringify(notifications.value));
  };

  /**
   * Ajoute une nouvelle notification en tête de liste.
   *
   * Utilisé pour notifier dynamiquement l'utilisateur d'événements
   * (ex: après un paiement, une inscription à la newsletter, etc.)
   *
   * @param {{ type?: string, title: string, desc: string, link?: string|null }} param
   */
  const addNotification = ({ type = 'system', title, desc, link = null }) => {
    // Insertion en tête de tableau (les nouvelles notifs en premier)
    notifications.value.unshift({
      id: Date.now(),         // ID unique basé sur le timestamp
      type,
      title,
      desc,
      time: 'À l\'instant',  // Temps affiché pour les nouvelles notifications
      read: false,            // Toute nouvelle notification est non lue par défaut
      link
    });
    saveToStorage(); // Persistance immédiate
  };

  /**
   * Marque une notification spécifique comme lue.
   *
   * @param {number} id - Identifiant de la notification à marquer
   */
  const markAsRead = (id) => {
    const item = notifications.value.find(n => n.id === id);
    if (item) {
      item.read = true;
      saveToStorage();
    }
  };

  /**
   * Marque toutes les notifications comme lues en une seule action.
   * Remet le compteur de badge à zéro.
   */
  const markAllAsRead = () => {
    notifications.value.forEach(n => n.read = true);
    saveToStorage();
  };

  /**
   * Supprime toutes les notifications (vide le centre de notifications).
   * Aussi nettoie le localStorage pour éviter les données orphelines.
   */
  const clearAll = () => {
    notifications.value = [];
    saveToStorage();
  };

  // Exposition des données réactives et des actions du store
  return {
    notifications,
    unreadCount,
    addNotification,
    markAsRead,
    markAllAsRead,
    clearAll
  };
});
