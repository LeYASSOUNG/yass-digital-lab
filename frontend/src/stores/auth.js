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
import axios from 'axios'

function setSessionData(userRef, tokenRef, userData, accessToken) {
  tokenRef.value = accessToken
  userRef.value  = userData
  localStorage.setItem('token', accessToken)
  localStorage.setItem('user', JSON.stringify(userData))
  axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`
}

function resetSessionData(userRef, tokenRef) {
  userRef.value  = null
  tokenRef.value = null
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  delete axios.defaults.headers.common['Authorization']
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
      const response = await axios.post('http://localhost:8000/api/login', { email, password })
      setSessionData(user, token, response.data.user, response.data.access_token)
      return { success: true, user: user.value }
    } catch (error) {
      console.error('Erreur de connexion', error)
      return { success: false, message: error.response?.data?.message || 'Erreur de connexion' }
    }
  }

  const register = async (payload, ...rest) => {
    try {
      const data = formatRegisterPayload(payload, rest)
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
