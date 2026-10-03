<template>
  <div class="admin-login-page flex justify-center items-center" style="min-height: 100vh; position: relative; padding: 40px 20px; background: var(--color-bg);">
    
    <!-- Background Ambient Glow -->
    <div class="hero-glow"></div>

    <!-- Main Container -->
    <div class="fade-in main-auth-card" style="border-radius: var(--radius-lg); width: 100%; max-width: 500px; position: relative; z-index: 1; border: 1px solid var(--color-border); box-shadow: var(--shadow-xl); background: var(--color-bg-card); overflow: hidden; display: flex; flex-direction: column;">
      
      <div style="padding: 44px 38px; position: relative;">
        
        <div class="text-center mb-8">
          <div class="flex justify-center mb-4">
            <Logo :size="48" :showTagline="false" />
          </div>
          <span class="badge-pill badge-indigo mb-3" style="font-size: 0.76rem;">ACCÈS RÉSERVÉ</span>
          <h2 style="font-size: 1.7rem; font-weight: 800; margin-bottom: 6px; color: var(--color-text);">
            Administration
          </h2>
          <p style="color: var(--color-text-muted); font-size: 0.9rem;">
            Identifiez-vous pour accéder au panneau de contrôle
          </p>
        </div>

        <!-- Notification Message -->
        <div v-if="errorMessage" style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); color: #EF4444; padding: 12px 14px; border-radius: var(--radius-md); margin-bottom: 20px; text-align: center; font-size: 0.88rem; font-weight: 600;" class="flex items-center justify-center gap-2">
          <AlertTriangle :size="18" /> {{ errorMessage }}
        </div>

        <form @submit.prevent="handleLogin" class="flex flex-col gap-4">
          <div>
            <label style="display: block; margin-bottom: 6px; font-size: 0.88rem;">Email Administrateur</label>
            <div style="position: relative;">
              <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none; display: flex; align-items: center;">
                <ShieldCheck :size="18" />
              </div>
              <input 
                v-model="loginEmail" 
                type="email" 
                required 
                style="width: 100%; padding: 11px 16px 11px 42px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" 
                placeholder="admin@yassdigital.lab" 
              />
            </div>
          </div>
          
          <div>
            <div class="flex justify-between items-center mb-1">
              <label style="font-size: 0.88rem;">Mot de passe</label>
            </div>
            <div style="position: relative;">
              <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none; display: flex; align-items: center;">
                <Lock :size="18" />
              </div>
              <input 
                v-model="loginPassword" 
                :type="showPassword ? 'text' : 'password'" 
                required 
                style="width: 100%; padding: 11px 42px 11px 42px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" 
                placeholder="••••••••" 
              />
              <button type="button" @click="showPassword = !showPassword" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--color-text-muted); display: flex; align-items: center; justify-content: center;" title="Afficher/Masquer">
                <EyeOff v-if="showPassword" :size="18" />
                <Eye v-else :size="18" />
              </button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="width: 100%; margin-top: 16px; padding: 12px 20px; font-size: 0.96rem;" :disabled="loading">
            <span>{{ loading ? 'Authentification...' : 'Accéder au Dashboard' }}</span>
            <ArrowRight v-if="!loading" :size="18" />
          </button>
        </form>

        <div style="margin-top: 24px; text-align: center;">
          <router-link to="/" style="color: var(--color-text-muted); font-size: 0.85rem; text-decoration: none;" class="flex items-center justify-center gap-2">
            <ArrowLeft :size="14" /> Retour à l'accueil
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useToastStore } from '../stores/toast';
import Logo from '../components/Logo.vue';
import { 
  ArrowLeft, 
  ArrowRight, 
  ShieldCheck, 
  Lock, 
  Eye, 
  EyeOff, 
  AlertTriangle 
} from 'lucide-vue-next';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const toastStore = useToastStore();

const loading = ref(false);
const errorMessage = ref('');
const showPassword = ref(false);

const loginEmail = ref('');
const loginPassword = ref('');

onMounted(() => {
  if (route.query.error) {
    errorMessage.value = route.query.error;
    toastStore.showToast(route.query.error, 'error');
  }
});

const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = '';
  try {
    const res = await authStore.login(loginEmail.value, loginPassword.value);
    
    if (res.success) {
      const adminRoles = ['admin', 'super_admin', 'editor', 'creator', 'support'];
      if (adminRoles.includes(res.user.role)) {
        toastStore.showToast('Authentification Admin réussie.', 'success');
        router.push('/admin');
      } else {
        errorMessage.value = "Accès refusé. Vous n'avez pas les droits d'administration.";
        toastStore.showToast(errorMessage.value, 'error');
        await authStore.logout();
      }
    } else {
      errorMessage.value = res.message || 'Identifiants incorrects.';
      toastStore.showToast(errorMessage.value, 'error');
    }
  } catch (err) {
    errorMessage.value = err.message || 'Erreur inconnue.';
    toastStore.showToast(errorMessage.value, 'error');
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.admin-login-page {
  background: var(--page-gradient);
}
.main-auth-card {
  background: var(--color-bg-elevated);
  border: 1px solid rgba(255, 255, 255, 0.09);
  backdrop-filter: blur(24px);
}
</style>

