/**
 * ============================================================
 * Store Pinia — Notifications (notification.js)
 * Yass Digital Lab — Frontend Vue 3
 * ============================================================
 * Gère le centre de notifications avec API REST & Polling.
 * ============================================================
 */

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../api';

const DEFAULT_NOTIFICATIONS = [
  {
    id: 1,
    type: 'promo',
    title: 'Bienvenue sur Yass Digital Lab !',
    desc: 'Explorez notre catalogue de templates SaaS et packs IA.',
    time: 'À l\'instant',
    read: false,
    link: '/products'
  }
];

export const useNotificationStore = defineStore('notifications', () => {
  const notifications = ref([]);
  const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);
  
  let pollingInterval = null;

  const loadFromApi = async () => {
    const token = localStorage.getItem('token');
    if (!token) {
      notifications.value = DEFAULT_NOTIFICATIONS;
      return;
    }
    try {
      const res = await api.get('/user/notifications');
      notifications.value = res.data || [];
    } catch (e) {
      console.error('Erreur chargement notifications:', e);
    }
  };

  const startPolling = () => {
    loadFromApi();
    if (!pollingInterval) {
      pollingInterval = setInterval(loadFromApi, 60000); // Check every minute
    }
  };

  const stopPolling = () => {
    if (pollingInterval) {
      clearInterval(pollingInterval);
      pollingInterval = null;
    }
    notifications.value = DEFAULT_NOTIFICATIONS;
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
  };

  const markAsRead = async (id) => {
    const item = notifications.value.find(n => n.id === id);
    if (item && !item.read) {
      item.read = true;
      const token = localStorage.getItem('token');
      if (token) {
        try {
          await api.post(`/user/notifications/${id}/mark-read`);
        } catch (_) {}
      }
    }
  };

  const markAllAsRead = async () => {
    notifications.value.forEach(n => n.read = true);
    const token = localStorage.getItem('token');
    if (token) {
      try {
        await api.post('/user/notifications/mark-read');
      } catch (_) {}
    }
  };

  const clearAll = async () => {
    notifications.value = [];
    const token = localStorage.getItem('token');
    if (token) {
      try {
        await api.delete('/user/notifications');
      } catch (_) {}
    }
  };

  return {
    notifications,
    unreadCount,
    loadFromApi,
    startPolling,
    stopPolling,
    addNotification,
    markAsRead,
    markAllAsRead,
    clearAll
  };
});

