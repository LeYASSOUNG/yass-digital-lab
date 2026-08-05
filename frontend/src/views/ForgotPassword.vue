<template>
  <div class="forgot-password-page container" style="margin-top: 50px; margin-bottom: 60px;">
    <div class="glass" style="padding: 40px 32px; border-radius: 24px; max-width: 480px; margin: 0 auto; background: var(--color-bg-card);">
      
      <div class="text-center mb-6">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(212,175,55,0.12); border: 1px solid var(--color-accent); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
          <KeyRound :size="28" style="color: var(--color-accent);" />
        </div>
        <h1 style="font-size: 1.6rem; font-weight: 800; margin: 0 0 6px;">Mot de passe oublié ?</h1>
        <p style="color: var(--color-text-light); font-size: 0.88rem; margin: 0;">
          Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
        </p>
      </div>

      <div v-if="successMessage" class="mb-6 p-4" style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; color: #10b981; font-size: 0.88rem;">
        {{ successMessage }}
      </div>

      <form @submit.prevent="handleForgotPassword" class="flex flex-col gap-4">
        <div>
          <label class="form-label" style="font-size: 0.85rem; font-weight: 700; margin-bottom: 6px; display: block;">Adresse Email *</label>
          <div style="position: relative;">
            <Mail :size="16" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);" />
            <input 
              v-model="email" 
              type="email" 
              required 
              placeholder="votre@email.com"
              style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;"
            />
          </div>
        </div>

        <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="padding: 12px; font-weight: 800; margin-top: 8px;" :disabled="loading">
          <Send :size="16" /> {{ loading ? 'Envoi en cours...' : 'Envoyer le lien de réinitialisation' }}
        </button>

        <div class="text-center mt-4" style="border-top: 1px solid var(--color-border); padding-top: 16px;">
          <router-link to="/login" style="color: var(--color-accent); font-weight: 700; font-size: 0.88rem; text-decoration: none;">
            ← Retour à la page de connexion
          </router-link>
        </div>
      </form>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../api';
import { useToastStore } from '../stores/toast';
import { KeyRound, Mail, Send } from 'lucide-vue-next';

const toastStore = useToastStore();
const email = ref('');
const loading = ref(false);
const successMessage = ref('');

const handleForgotPassword = async () => {
  loading.value = true;
  successMessage.value = '';
  try {
    const res = await api.post('/forgot-password', { email: email.value });
    successMessage.value = res.data.message;
    toastStore.showToast('Demande envoyée avec succès', 'success');
  } catch (e) {
    toastStore.showToast(e.response?.data?.message || 'Erreur lors de l\'envoi', 'error');
  } finally {
    loading.value = false;
  }
};
</script>
