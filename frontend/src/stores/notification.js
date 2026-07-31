/**
 * ============================================================
 * Store Pinia — Notifications (notification.js)
 * Yass Digital Lab — Frontend Vue 3
 * ============================================================
 * Gère le centre de notifications de l'espace client.
 * ============================================================
 */

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

const DEFAULT_NOTIFICATIONS = [
  {
    id: 1,
    type: 'order',
    title: 'Commande #FA-000001 Validée',
    desc: 'Votre paiement a été accepté et vos fichiers sont disponibles au téléchargement.',
    time: 'Il y a 5 min',
    read: false,
    link: '/dashboard'
  },
  {
    id: 2,
    type: 'quote',
    title: 'Nouvelle Demande de Devis',
    desc: 'Une proposition pour "Création de site web sur mesure" est en cours de traitement.',
    time: 'Il y a 1h',
    read: false,
    link: '/admin'
  },
  {
    id: 3,
    type: 'promo',
    title: 'Code Promo Exclusif : YASS20',
    desc: 'Bénéficiez de 20% de réduction immédiate sur tous nos templates SaaS et packs IA.',
    time: 'Hier',
    read: true,
    link: '/products'
  }
];

export const useNotificationStore = defineStore('notifications', () => {
  const notifications = ref(
    JSON.parse(localStorage.getItem('user_notifications')) || DEFAULT_NOTIFICATIONS
  );

  const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

  const saveToStorage = () => {
    localStorage.setItem('user_notifications', JSON.stringify(notifications.value));
  };

  const addNotification = ({ type = 'system', title, desc, link = null }) => {
    notifications.value.unshift({
      id: Date.now(),
      type,
      title,
      desc,
      time: 'À l\'instant',
      read: false,
      link
    });
    saveToStorage();
  };

  const markAsRead = (id) => {
    const item = notifications.value.find(n => n.id === id);
    if (item) {
      item.read = true;
      saveToStorage();
    }
  };

  const markAllAsRead = () => {
    notifications.value.forEach(n => n.read = true);
    saveToStorage();
  };

  const clearAll = () => {
    notifications.value = [];
    saveToStorage();
  };

  return {
    notifications,
    unreadCount,
    addNotification,
    markAsRead,
    markAllAsRead,
    clearAll
  };
});
