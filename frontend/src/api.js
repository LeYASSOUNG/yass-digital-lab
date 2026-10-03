import axios from 'axios';

const HTTP_STATUS_UNAUTHORIZED = 401;

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Intercepteur pour injecter automatiquement le jeton d'authentification s'il existe
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});

// Intercepteur pour gérer les erreurs d'authentification (401) et d'autorisation (403)
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      const status = error.response.status;
      const path = window.location.pathname;
      const isProtectedRoute = path.startsWith('/admin') || path.startsWith('/dashboard');

      if (status === HTTP_STATUS_UNAUTHORIZED && isProtectedRoute) {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
      } else if (status === 403 && path.startsWith('/admin')) {
        // Redirige les utilisateurs non autorisés vers le dashboard client
        window.location.href = '/dashboard';
      }
    }
    return Promise.reject(error);
  }
);

export default api;

