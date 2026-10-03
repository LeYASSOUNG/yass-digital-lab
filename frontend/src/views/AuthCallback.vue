<template>
  <div class="auth-callback-page flex justify-center items-center" style="min-height: 80vh;">
    <div class="text-center">
      <div class="spinner mb-4" style="margin: 0 auto; width: 40px; height: 40px; border: 4px solid rgba(124,58,237,0.2); border-top-color: var(--color-primary); border-radius: 50%; animation: spin 1s linear infinite;"></div>
      <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--color-text);">Connexion sécurisée en cours...</h2>
      <p style="color: var(--color-text-muted); font-size: 0.9rem; margin-top: 10px;">Veuillez patienter pendant la finalisation de votre authentification sociale.</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useToastStore } from '../stores/toast';
import api from '../api';
import { useWishlistStore } from '../stores/wishlist';
import { useNotificationStore } from '../stores/notification';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const toastStore = useToastStore();

onMounted(async () => {
  const token = route.query.token;
  
  if (token) {
    try {
      // Set the token temporarily in localStorage to fetch user data
      localStorage.setItem('token', token);
      
      // Fetch user data
      const res = await api.get('/user');
      const user = res.data;
      
      // Update auth store with user and token
      authStore.token = token;
      authStore.user = user;
      localStorage.setItem('user', JSON.stringify(user));
      
      // Load user specifics
      useWishlistStore().loadFromApi();
      useNotificationStore().startPolling();
      
      toastStore.showToast(`Bienvenue ${user.name} !`, 'success');
      router.push('/dashboard');
    } catch (err) {
      console.error(err);
      authStore.logout();
      toastStore.showToast('Erreur lors de la récupération du profil utilisateur.', 'error');
      router.push('/login');
    }
  } else {
    toastStore.showToast('Jeton d\'authentification manquant.', 'error');
    router.push('/login');
  }
});
</script>

<style scoped>
@keyframes spin {
  to { transform: rotate(360deg); }
}
.auth-callback-page {
  background: var(--page-gradient);
  color: var(--color-text);
}
</style>

