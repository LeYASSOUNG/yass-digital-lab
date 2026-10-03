/**
 * ============================================================
 * Wishlist Store — Yass Digital Lab
 * ============================================================
 * Gestion des produits favoris avec persistance hybride :
 * - localStorage pour les utilisateurs non connectés (guest)
 * - API Laravel (table wishlists) pour les utilisateurs connectés
 *
 * Au moment de la connexion, la wishlist guest est fusionnée
 * avec la wishlist serveur automatiquement.
 * ============================================================
 */

import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../api';

export const useWishlistStore = defineStore('wishlist', () => {
  const items = ref(JSON.parse(localStorage.getItem('wishlist')) || []);
  const synced = ref(false);

  // ----------------------------------------------------------------
  // Chargement de la wishlist depuis l'API (utilisateur connecté)
  // ----------------------------------------------------------------
  const loadFromApi = async () => {
    const token = localStorage.getItem('token');
    if (!token) {
      synced.value = false;
      return;
    }
    try {
      const res = await api.get('/user/wishlist');
      const serverItems = res.data || [];

      // Fusion : garder les items guest qui ne sont pas encore côté serveur
      const guestItems = JSON.parse(localStorage.getItem('wishlist')) || [];
      const serverIds = new Set(serverItems.map(p => p.id));

      // Pousser les favoris guest non encore synchronisés vers le serveur
      for (const guestItem of guestItems) {
        if (!serverIds.has(guestItem.id)) {
          try {
            await api.post(`/user/wishlist/${guestItem.id}/toggle`);
            serverItems.push(guestItem);
          } catch (_) { /* ignore */ }
        }
      }

      items.value = serverItems;
      localStorage.setItem('wishlist', JSON.stringify(items.value));
      synced.value = true;
    } catch (e) {
      // Non connecté ou erreur réseau : on garde le localStorage
      synced.value = false;
    }
  };

  // ----------------------------------------------------------------
  // Toggle (Ajouter / Retirer un produit des favoris)
  // ----------------------------------------------------------------
  const toggleWishlist = async (product) => {
    const token = localStorage.getItem('token');

    if (token) {
      // Utilisateur connecté → API
      try {
        const res = await api.post(`/user/wishlist/${product.id}/toggle`);
        if (res.data.status === 'removed') {
          items.value = items.value.filter(p => p.id !== product.id);
        } else {
          if (!items.value.find(p => p.id === product.id)) {
            items.value.push(product);
          }
        }
        localStorage.setItem('wishlist', JSON.stringify(items.value));
        return res.data.status; // 'added' ou 'removed'
      } catch (e) {
        console.error('Wishlist API error:', e);
      }
    } else {
      // Utilisateur guest → localStorage uniquement
      const idx = items.value.findIndex(p => p.id === product.id);
      if (idx > -1) {
        items.value.splice(idx, 1);
        localStorage.setItem('wishlist', JSON.stringify(items.value));
        return 'removed';
      } else {
        items.value.push(product);
        localStorage.setItem('wishlist', JSON.stringify(items.value));
        return 'added';
      }
    }
  };

  // ----------------------------------------------------------------
  // Vérifier si un produit est en favori
  // ----------------------------------------------------------------
  const isFavorite = (productId) => {
    return items.value.some(p => p.id === productId);
  };

  // ----------------------------------------------------------------
  // Vider la wishlist complète
  // ----------------------------------------------------------------
  const clearWishlist = async () => {
    const token = localStorage.getItem('token');
    if (token) {
      try {
        await api.delete('/user/wishlist');
      } catch (_) { /* ignore */ }
    }
    items.value = [];
    localStorage.removeItem('wishlist');
  };

  return { items, synced, loadFromApi, toggleWishlist, isFavorite, clearWishlist };
});

