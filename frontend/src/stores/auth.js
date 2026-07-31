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

/** Helper pour persister les tokens et configurer Axios */
function setSessionData(userRef, tokenRef, userData, accessToken) {
  tokenRef.value = accessToken
  userRef.value  = userData
  localStorage.setItem('token', accessToken)
  localStorage.setItem('user', JSON.stringify(userData))
  axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`
}

/** Helper pour nettoyer les tokens et réinitialiser Axios */
function resetSessionData(userRef, tokenRef) {
  userRef.value  = null
  tokenRef.value = null
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  delete axios.defaults.headers.common['Authorization']
}

export const useAuthStore = defineStore('auth', () => {
  const user  = ref(JSON.parse(localStorage.getItem('user')) || null)
  const token = ref(localStorage.getItem('token') || null)

  const login = async (email, password) => {
    try {
      const response = await axios.post('http://localhost:8000/api/login', { email, password })
      setSessionData(user, token, response.data.user, response.data.access_token)
      return { success: true, user: user.value }
    } catch (error) {
      console.error('Erreur de connexion', error)
      return { success: false, message: error.response?.data?.message || 'Erreur de connexion' }
    }
  }

  /**
   * Inscription d'un nouveau compte client.
   *
   * @param {Object|string} payload - Objet { name, email, password, phone, company, address } ou nom
   */
  const register = async (payload, ...rest) => {
    const data = typeof payload === 'object' && payload !== null
      ? payload
      : {
          name: payload,
          email: rest[0],
          password: rest[1],
          phone: rest[2] || null,
          company: rest[3] || null,
          address: rest[4] || null,
        }

    try {
      const response = await axios.post('http://localhost:8000/api/register', data)
      setSessionData(user, token, response.data.user, response.data.access_token)
      return { success: true, user: user.value }
    } catch (error) {
      console.error('Erreur d inscription', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur lors de la création du compte',
      }
    }
  }

  const logout = async () => {
    try {
      if (token.value) {
        await axios.post('http://localhost:8000/api/logout', {}, {
          headers: { Authorization: `Bearer ${token.value}` }
        })
      }
    } catch (e) {
      console.error(e)
    } finally {
      resetSessionData(user, token)
    }
  }

  return { user, token, login, register, logout }
})
