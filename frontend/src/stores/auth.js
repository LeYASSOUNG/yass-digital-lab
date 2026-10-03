/**
 * ============================================================
 * Store Pinia — Authentification (auth.js)
 * Yass Digital Lab — Frontend Vue 3
 * ============================================================
 * Gère l'état global d'authentification de l'application.
 * ============================================================
 */

import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api'
import { useWishlistStore } from './wishlist'
import { useNotificationStore } from './notification'

function setSessionData(userRef, tokenRef, userData, accessToken) {
  tokenRef.value = accessToken
  userRef.value  = userData
  localStorage.setItem('token', accessToken)
  localStorage.setItem('user', JSON.stringify(userData))
}

function resetSessionData(userRef, tokenRef) {
  userRef.value  = null
  tokenRef.value = null
  localStorage.removeItem('token')
  localStorage.removeItem('user')
}

function formatRegisterPayload(payload, rest) {
  if (typeof payload === 'object' && payload !== null) {
    return payload
  }
  return {
    name: payload,
    email: rest[0],
    password: rest[1],
    phone: rest[2] || null,
    company: rest[3] || null,
    address: rest[4] || null,
  }
}

export const useAuthStore = defineStore('auth', () => {
  const user  = ref(JSON.parse(localStorage.getItem('user')) || null)
  const token = ref(localStorage.getItem('token') || null)

  const login = async (email, password) => {
    try {
      const response = await api.post('/login', { email, password })
      setSessionData(user, token, response.data.user, response.data.access_token)
      // Synchroniser la wishlist guest avec le serveur après connexion
      useWishlistStore().loadFromApi()
      // Démarrer le polling des notifications
      useNotificationStore().startPolling()
      return { success: true, user: user.value }
    } catch (error) {
      console.error('Erreur de connexion', error)
      return { success: false, message: error.response?.data?.message || 'Erreur de connexion' }
    }
  }

  const register = async (payload, ...rest) => {
    try {
      const data = formatRegisterPayload(payload, rest)
      const response = await api.post('/register', data)
      // Ne pas connecter l'utilisateur ici, il doit d'abord vérifier son OTP
      return { success: true, user: response.data.user }
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
        await api.post('/logout')
      }
    } catch (e) {
      console.error(e)
    } finally {
      // Nettoyer la wishlist locale à la déconnexion
      useWishlistStore().clearWishlist()
      // Arrêter le polling des notifications
      useNotificationStore().stopPolling()
      resetSessionData(user, token)
    }
  }

  const verifyOtp = async (email, otp) => {
    try {
      const response = await api.post('/verify-otp', { email, otp })
      
      if (response.data.access_token) {
        setSessionData(user, token, response.data.user, response.data.access_token)
        useWishlistStore().loadFromApi()
        useNotificationStore().startPolling()
      }
      
      return { success: true, message: response.data.message }
    } catch (error) {
      console.error('Erreur vérification OTP', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur lors de la vérification'
      }
    }
  }

  const resendOtp = async (email) => {
    // Si l'utilisateur est connecté, on peut utiliser /email/verification-notification
    try {
      const response = await api.post('/email/verification-notification', { email })
      return { success: true, message: response.data.message }
    } catch (error) {
      console.error('Erreur renvoi OTP', error)
      return {
        success: false,
        message: error.response?.data?.message || 'Erreur lors du renvoi du code'
      }
    }
  }

  return { user, token, login, register, logout, verifyOtp, resendOtp }
})

