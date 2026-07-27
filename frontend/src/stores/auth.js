import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user')) || null)
  const token = ref(localStorage.getItem('token') || null)

  const login = async (email, password) => {
    try {
      const response = await axios.post('http://localhost:8000/api/login', { email, password })
      token.value = response.data.access_token
      user.value = response.data.user
      
      localStorage.setItem('token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))
      
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      return { success: true, user: user.value }
    } catch (error) {
      console.error('Erreur de connexion', error)
      return { success: false, message: error.response?.data?.message || 'Erreur de connexion' }
    }
  }

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
      token.value = response.data.access_token
      user.value = response.data.user
      
      localStorage.setItem('token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))
      
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      return { success: true, user: user.value }
    } catch (error) {
      console.error('Erreur d inscription', error)
      return { success: false, message: error.response?.data?.message || 'Erreur lors de la création du compte' }
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
      user.value = null
      token.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common['Authorization']
    }
  }

  return { user, token, login, register, logout }
})
