<template>
  <div class="reset-password-page container" style="margin-top: 50px; margin-bottom: 60px;">
    <div class="glass" style="padding: 40px 32px; border-radius: 24px; max-width: 480px; margin: 0 auto; background: var(--color-bg-card);">
      
      <div class="text-center mb-6">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(16,185,129,0.12); border: 1px solid #10b981; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
          <ShieldCheck :size="28" style="color: #10b981;" />
        </div>
        <h1 style="font-size: 1.6rem; font-weight: 800; margin: 0 0 6px;">Nouveau Mot de Passe</h1>
        <p style="color: var(--color-text-light); font-size: 0.88rem; margin: 0;">
          Définissez un nouveau mot de passe sécurisé pour votre compte.
        </p>
      </div>

      <div v-if="errorMessage" class="mb-4 p-4" style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); border-radius: 12px; color: #ef4444; font-size: 0.85rem;">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleResetPassword" class="flex flex-col gap-4">
        <div>
          <label style="font-size: 0.85rem; font-weight: 700; margin-bottom: 6px; display: block;">Adresse Email</label>
          <div style="position: relative;">
            <Mail :size="16" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);" />
            <input 
              v-model="email" 
              type="email" 
              required 
              readonly
              style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;"
            />
          </div>
        </div>

        <div>
          <label style="font-size: 0.85rem; font-weight: 700; margin-bottom: 6px; display: block;">Nouveau Mot de Passe *</label>
          <div style="position: relative;">
            <Lock :size="16" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);" />
            <input 
              v-model="password" 
              type="password" 
              required 
              placeholder="Minimum 6 caractères"
              style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;"
            />
          </div>
        </div>

        <div>
          <label style="font-size: 0.85rem; font-weight: 700; margin-bottom: 6px; display: block;">Confirmer le Mot de Passe *</label>
          <div style="position: relative;">
            <Lock :size="16" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);" />
            <input 
              v-model="passwordConfirmation" 
              type="password" 
              required 
              placeholder="Répétez le mot de passe"
              style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;"
            />
          </div>
        </div>

        <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="padding: 12px; font-weight: 800; margin-top: 8px;" :disabled="loading">
          <CheckCircle2 :size="16" /> {{ loading ? 'Réinitialisation...' : 'Changer mon mot de passe' }}
        </button>
      </form>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';
import { useToastStore } from '../stores/toast';
import { ShieldCheck, Mail, Lock, CheckCircle2 } from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const toastStore = useToastStore();

const email = ref('');
const token = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const loading = ref(false);
const errorMessage = ref('');

onMounted(() => {
  email.value = route.query.email || '';
  token.value = route.query.token || '';
});

const handleResetPassword = async () => {
  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = 'La confirmation du mot de passe ne correspond pas.';
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  try {
    const res = await api.post('/reset-password', {
      token: token.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });

    toastStore.showToast(res.data.message || 'Mot de passe réinitialisé !', 'success');
    router.push('/login');
  } catch (e) {
    errorMessage.value = e.response?.data?.message || 'Erreur de réinitialisation.';
    toastStore.showToast(errorMessage.value, 'error');
  } finally {
    loading.value = false;
  }
};
</script>
