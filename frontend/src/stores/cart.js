/**
 * ============================================================
 * Store Pinia — Panier d'achat (cart.js)
 * Yass Digital Lab — Frontend Vue 3
 * ============================================================
 * Gère l'état global du panier d'achat :
 *   - Liste des articles ajoutés au panier
 *   - Ajout avec gestion des doublons (incrémente la quantité)
 *   - Suppression d'un article par son ID
 *   - Vidage complet du panier
 *   - Propriétés calculées : nombre total d'articles et prix total
 *
 * Utilisé dans : CartView.vue, ProductCard.vue, Checkout.vue, Navbar.vue
 *
 * Note : Le panier n'est PAS persisté en localStorage (réinitialisé
 *        à chaque rechargement), ce qui est un comportement volontaire
 *        pour les produits numériques téléchargeables.
 * ============================================================
 */

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  // État réactif : liste des articles dans le panier
  // Chaque article contient : id, title, price, quantity (et autres props du produit)
  const items = ref([])

  /**
   * Ajoute un produit au panier.
   *
   * Si le produit est déjà dans le panier (même id), incrémente sa quantité.
   * Sinon, ajoute le produit avec une quantité initiale de 1.
   *
   * @param {object} product - Objet produit complet (id, title, price, ...)
   */
  const addItem = (product) => {
    // Recherche si le produit est déjà dans le panier
    const existing = items.value.find(item => item.id === product.id)

    if (existing) {
      // Produit déjà présent → incrémenter la quantité
      existing.quantity++
    } else {
      // Nouveau produit → l'ajouter avec quantité = 1
      items.value.push({ ...product, quantity: 1 })
    }
  }

  /**
   * Retire un produit du panier par son identifiant.
   *
   * @param {number} productId - Identifiant du produit à retirer
   */
  const removeItem = (productId) => {
    // Filtre tous les articles sauf celui à supprimer
    items.value = items.value.filter(item => item.id !== productId)
  }

  /**
   * Vide entièrement le panier.
   * Appelé après un paiement réussi ou une annulation volontaire.
   */
  const clearCart = () => {
    items.value = []
  }

  /**
   * Nombre total d'articles dans le panier (somme des quantités).
   * Affiché dans le badge de l'icône panier dans la navbar.
   *
   * @type {import('vue').ComputedRef<number>}
   */
  const totalItems = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0)
  })

  /**
   * Prix total du panier (somme de prix × quantité pour chaque article).
   * Affiché dans la page panier et à la page checkout.
   *
   * @type {import('vue').ComputedRef<number>}
   */
  const totalPrice = computed(() => {
    return items.value.reduce((total, item) => total + (item.price * item.quantity), 0)
  })

  // Exposition des données réactives et des actions
  return { items, addItem, removeItem, clearCart, totalItems, totalPrice }
})

