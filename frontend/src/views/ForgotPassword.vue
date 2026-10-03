<template>
  <div class="forgot-password-page container" style="margin-top: 50px; margin-bottom: 60px;">
    <div class="fade-in" style="padding: 42px 34px; border-radius: var(--radius-lg); max-width: 480px; margin: 0 auto; background: var(--color-bg-card); border: 1px solid var(--color-border); box-shadow: var(--shadow-xl);">
      
      <div class="text-center mb-6">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(124,58,237,0.12); border: 1px solid var(--color-primary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px;">
          <KeyRound :size="28" style="color: var(--color-primary);" />
        </div>
        <h1 style="font-size: 1.65rem; font-weight: 800; margin: 0 0 6px;">
          {{ step === 1 ? 'Mot de passe oublié ?' : 'Nouveau mot de passe' }}
        </h1>
        <p style="color: var(--color-text-muted); font-size: 0.9rem; margin: 0; line-height: 1.6;">
          {{ step === 1 
            ? 'Entrez votre adresse email et nous vous enverrons un code de vérification pour réinitialiser votre mot de passe.' 
            : 'Entrez le code OTP reçu par email ainsi que votre nouveau mot de passe.' 
          }}
        </p>
      </div>

      <div v-if="successMessage" class="mb-6 p-4" style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); border-radius: var(--radius-md); color: #10B981; font-size: 0.88rem; font-weight: 600;">
        {{ successMessage }}
      </div>
      
      <div v-if="errorMessage" class="mb-6 p-4" style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); border-radius: var(--radius-md); color: #EF4444; font-size: 0.88rem; font-weight: 600;">
        {{ errorMessage }}
      </div>

      <!-- ETAPE 1 : DEMANDE D'EMAIL -->
      <form v-if="step === 1" @submit.prevent="handleForgotPassword" class="flex flex-col gap-4">
        <div>
          <label class="form-label" style="font-size: 0.86rem; font-weight: 600; margin-bottom: 6px; display: block;">Adresse Email *</label>
          <div style="position: relative;">
            <Mail :size="16" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted);" />
            <input 
              v-model="email" 
              type="email" 
              required 
              placeholder="votre@email.com"
              style="width: 100%; padding: 11px 14px 11px 40px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;"
            />
          </div>
        </div>

        <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="padding: 12px; font-weight: 700; margin-top: 6px;" :disabled="loading">
          <Send :size="16" /> {{ loading ? 'Envoi en cours...' : 'Envoyer le code OTP' }}
        </button>

        <div class="text-center mt-4" style="border-top: 1px solid var(--color-border); padding-top: 16px;">
          <router-link to="/login" style="color: var(--color-primary); font-weight: 700; font-size: 0.88rem; text-decoration: none;" class="flex items-center justify-center gap-1">
            <ArrowLeft :size="15" /> Retour à la connexion
          </router-link>
        </div>
      </form>

      <!-- ETAPE 2 : VERIFICATION OTP ET NOUVEAU MOT DE PASSE -->
      <form v-else @submit.prevent="handleResetPassword" class="flex flex-col gap-4">
        <div>
          <label class="form-label" style="font-size: 0.86rem; font-weight: 600; margin-bottom: 6px; display: block;">Code de vérification (6 chiffres) *</label>
          <input 
            v-model="otpCode" 
            type="text" 
            maxlength="6"
            required 
            placeholder="123456"
            style="width: 100%; padding: 11px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 1.2rem; font-weight: bold; text-align: center; letter-spacing: 4px;"
          />
        </div>
        
        <div>
          <label class="form-label" style="font-size: 0.86rem; font-weight: 600; margin-bottom: 6px; display: block;">Nouveau mot de passe *</label>
          <input 
            v-model="password" 
            type="password" 
            required 
            placeholder="Min. 6 caractères"
            style="width: 100%; padding: 11px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;"
          />
        </div>
        
        <div>
          <label class="form-label" style="font-size: 0.86rem; font-weight: 600; margin-bottom: 6px; display: block;">Confirmer le mot de passe *</label>
          <input 
            v-model="passwordConfirm" 
            type="password" 
            required 
            placeholder="Répétez le mot de passe"
            style="width: 100%; padding: 11px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;"
          />
        </div>

        <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="padding: 12px; font-weight: 700; margin-top: 6px;" :disabled="loading">
           {{ loading ? 'Vérification en cours...' : 'Mettre à jour le mot de passe' }}
        </button>

        <div class="text-center mt-4" style="border-top: 1px solid var(--color-border); padding-top: 16px;">
          <button type="button" @click="step = 1" style="background: none; border: none; color: var(--color-primary); font-size: 0.85rem; cursor: pointer; text-decoration: underline;">
            Renvoyer un nouveau code
          </button>
        </div>
      </form>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';
import { useToastStore } from '../stores/toast';
import { KeyRound, Mail, Send, ArrowLeft } from 'lucide-vue-next';

const router = useRouter();
const toastStore = useToastStore();
const email = ref('');
const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const step = ref(1);
const otpCode = ref('');
const password = ref('');
const passwordConfirm = ref('');

const handleForgotPassword = async () => {
  loading.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  try {
    const res = await api.post('/forgot-password', { email: email.value });
    successMessage.value = res.data.message;
    toastStore.showToast('Un code OTP vous a été envoyé.', 'success');
    step.value = 2; // On passe à l'étape 2 (OTP)
  } catch (e) {
    errorMessage.value = e.response?.data?.message || 'Erreur lors de l\'envoi';
    toastStore.showToast(errorMessage.value, 'error');
  } finally {
    loading.value = false;
  }
};

const handleResetPassword = async () => {
  if (password.value !== passwordConfirm.value) {
    errorMessage.value = 'Les mots de passe ne correspondent pas.';
    return;
  }
  
  loading.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  try {
    const res = await api.post('/reset-password', { 
      email: email.value, 
      otp: otpCode.value,
      password: password.value,
      password_confirmation: passwordConfirm.value
    });
    toastStore.showToast('Mot de passe réinitialisé avec succès !', 'success');
    router.push('/login');
  } catch (e) {
    errorMessage.value = e.response?.data?.message || 'Le code est invalide ou a expiré.';
    toastStore.showToast(errorMessage.value, 'error');
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.forgot-password-page {
  padding: 30px 0 80px;
  color: var(--color-text);
}
.forgot-password-page h1 {
  color: var(--color-text) !important;
}
</style>

