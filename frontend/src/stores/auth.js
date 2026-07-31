/**
 * ============================================================
 * Store Pinia — Authentification (auth.js)
 * Yass Digital Lab — Frontend Vue 3
 * ============================================================
 * Gère l'état global d'authentification de l'application :
 *   - Session utilisateur (user, token)
 *   - Connexion / Inscription / Déconnexion
 *   - Persistance dans localStorage pour survivre au rechargement
 *   - Configuration automatique des headers Axios avec le Bearer token
 *
 * Utilisé dans : LoginView.vue, ClientDashboard.vue, App.vue, router/index.js
 * ============================================================
 */

import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // -------------------------------------------------------
  // État réactif — Hydraté depuis localStorage au démarrage
  // pour maintenir la session après un rechargement de page
  // -------------------------------------------------------
  const user  = ref(JSON.parse(localStorage.getItem('user')) || null)   // Données de l'utilisateur connecté
  const token = ref(localStorage.getItem('token') || null)               // Token Bearer Sanctum

  /**
   * Connexion d'un utilisateur existant.
   *
   * Envoie les identifiants à l'API, stocke le token et l'utilisateur
   * dans localStorage, et configure le header Authorization d'Axios.
   *
   * @param {string} email    - Adresse email
   * @param {string} password - Mot de passe
   * @returns {{ success: boolean, user?: object, message?: string }}
   */
  const login = async (email, password) => {
    try {
      const response = await axios.post('http://localhost:8000/api/login', { email, password })

      // Stockage du token et des données utilisateur en mémoire réactive
      token.value = response.data.access_token
      user.value  = response.data.user

      // Persistance dans localStorage pour les rechargements de page
      localStorage.setItem('token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))

      // Configuration globale du header Authorization pour toutes les futures requêtes Axios
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`

      return { success: true, user: user.value }
    } catch (error) {
      console.error('Erreur de connexion', error)
      return { success: false, message: error.response?.data?.message || 'Erreur de connexion' }
    }
  }

  /**
   * Inscription d'un nouveau compte client.
   *
   * Envoie les données du formulaire d'inscription à l'API.
   * Les champs phone, company, address sont facultatifs.
   *
   * @param {string}      name    - Nom complet
   * @param {string}      email   - Adresse email
   * @param {string}      password - Mot de passe
   * @param {string|null} phone   - Téléphone (optionnel)
   * @param {string|null} company - Entreprise (optionnel)
   * @param {string|null} address - Adresse de facturation (optionnel)
   * @returns {{ success: boolean, user?: object, message?: string }}
   */
  const register = async (name, email, password, phone = null, company = null, address = null) => {
    try {
      const response = await axios.post('http://localhost:8000/api/register', {
        name,
        email,
        password,
        phone,
        company,
        address
      })

      // Même logique que login : stockage + configuration Axios
      token.value = response.data.access_token
      user.value  = response.data.user

      localStorage.setItem('token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))

      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`

      return { success: true, user: user.value }
    } catch (error) {
      console.error('Erreur d inscription', error)
      return { success: false, message: error.response?.data?.message || 'Erreur lors de la création du compte' }
    }
  }

  /**
   * Déconnexion de l'utilisateur.
   *
   * Révoque le token côté serveur (API), puis nettoie le localStorage
   * et réinitialise l'état local. Effectué dans finally pour garantir
   * la déconnexion même si l'API est inaccessible.
   */
  const logout = async () => {
    try {
      // Révocation du token sur le serveur Laravel Sanctum
      if (token.value) {
        await axios.post('http://localhost:8000/api/logout', {}, {
          headers: { Authorization: `Bearer ${token.value}` }
        })
      }
    } catch (e) {
      // Si l'API est hors ligne, on déconnecte quand même localement
      console.error(e)
    } finally {
      // Nettoyage complet de la session côté client
      user.value  = null
      token.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common['Authorization'] // Suppression du header Axios
    }
  }

  // Exposition des données et actions du store
  return { user, token, login, register, logout }
})
