<template>
  <div class="login-page flex justify-center items-center" style="min-height: 80vh; position: relative; margin-top: 20px;">
    
    <!-- Background Ambient Glow -->
    <div style="position: absolute; width: 450px; height: 450px; background: radial-gradient(circle, rgba(212,175,55,0.15), transparent 70%); top: 50%; left: 50%; transform: translate(-50%, -50%); pointer-events: none;"></div>

    <div class="glass" style="padding: 40px 36px; border-radius: 24px; width: 100%; max-width: 460px; position: relative; z-index: 1;">
      
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <router-link to="/" style="font-size: 0.9rem; color: var(--color-text-light); text-decoration: none;">← Retour</router-link>
        <span style="font-weight: 800; font-size: 1.1rem;">Yass<span style="color: var(--color-accent);">DigitalLab</span></span>
      </div>

      <!-- Mode Switcher Tabs (Connexion / Inscription) -->
      <div class="flex gap-2 mb-6" style="background: rgba(0,0,0,0.1); padding: 4px; border-radius: 12px; border: 1px solid var(--color-border);">
        <button 
          @click="activeTab = 'login'" 
          :style="activeTab === 'login' ? 'background: var(--color-bg-card); color: var(--color-accent); font-weight: 700; box-shadow: var(--shadow-sm);' : 'color: var(--color-text-light);'"
          style="flex: 1; border: none; padding: 10px; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.2s;"
        >
          🔐 Connexion
        </button>
        <button 
          @click="activeTab = 'register'" 
          :style="activeTab === 'register' ? 'background: var(--color-bg-card); color: var(--color-accent); font-weight: 700; box-shadow: var(--shadow-sm);' : 'color: var(--color-text-light);'"
          style="flex: 1; border: none; padding: 10px; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.2s;"
        >
          ✨ Créer un compte
        </button>
      </div>

      <!-- Notification Message -->
      <div v-if="errorMessage" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; padding: 12px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-size: 0.85rem; font-weight: 600;">
        ⚠️ {{ errorMessage }}
      </div>

      <!-- FORM 1 : CONNEXION (Client & Admin) -->
      <form v-if="activeTab === 'login'" @submit.prevent="handleLogin" class="flex flex-col gap-4">
        <div>
          <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Adresse Email</label>
          <input v-model="loginEmail" type="email" required style="width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.95rem;" placeholder="votre@email.com" />
        </div>
        
        <div>
          <div class="flex justify-between items-center mb-1">
            <label style="font-weight: 600; font-size: 0.9rem;">Mot de passe</label>
            <button type="button" @click="showForgotModal = true" style="background: none; border: none; color: var(--color-accent); font-size: 0.8rem; cursor: pointer; text-decoration: underline;">
              Mot de passe oublié ?
            </button>
          </div>
          <div style="position: relative;">
            <input v-model="loginPassword" :type="showPassword ? 'text' : 'password'" required style="width: 100%; padding: 12px 40px 12px 16px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.95rem;" placeholder="••••••••" />
            <button type="button" @click="showPassword = !showPassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.1rem; color: var(--color-text-light);">
              {{ showPassword ? '👁️' : '🙈' }}
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;" :disabled="loading">
          {{ loading ? '⏳ Connexion...' : 'Se connecter →' }}
        </button>
      </form>

      <!-- FORM 2 : CREATION DE COMPTE CLIENT -->
      <form v-else @submit.prevent="handleRegister" class="flex flex-col gap-3">
        <div>
          <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.85rem;">Nom complet *</label>
          <input v-model="regName" type="text" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="ex: Jean Dupont" />
        </div>

        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.85rem;">Adresse Email *</label>
            <input v-model="regEmail" type="email" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="jean@example.com" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.85rem;">Téléphone</label>
            <input v-model="regPhone" type="tel" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="+33 6 12 34 56 78" />
          </div>
        </div>

        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.85rem;">Entreprise (Optionnel)</label>
            <input v-model="regCompany" type="text" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Nom de société" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.85rem;">Ville / Adresse</label>
            <input v-model="regAddress" type="text" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Paris, France" />
          </div>
        </div>
        
        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.85rem;">Mot de passe *</label>
            <input v-model="regPassword" type="password" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Min. 6 car." />
          </div>
          <div>
            <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.85rem;">Confirmation *</label>
            <input v-model="regConfirmPassword" type="password" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Répéter mot de passe" />
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;" :disabled="loading">
          {{ loading ? '⏳ Création...' : 'Créer mon compte Client →' }}
        </button>
      </form>

      <!-- Comptes Test Info -->
      <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--color-border); text-align: center; font-size: 0.8rem; color: var(--color-text-light);">
        <p style="margin-bottom: 4px;"><strong>🔑 Comptes de démonstration :</strong></p>
        <p>• Admin: <code>admin@yassdigital.lab</code> / <code>password</code></p>
        <p>• Client: <code>client@yassdigital.lab</code> / <code>password</code></p>
      </div>
    </div>

    <!-- Modal Mot de passe oublié -->
    <div v-if="showForgotModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px;">
      <div class="glass" style="padding: 36px; border-radius: 20px; width: 100%; max-width: 400px; position: relative;">
        <button @click="showForgotModal = false" style="position: absolute; right: 16px; top: 16px; background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--color-text-light);">✕</button>
        <h3 class="mb-2">🔒 Mot de passe oublié ?</h3>
        <p style="color: var(--color-text-light); font-size: 0.88rem; margin-bottom: 20px;">Saisissez votre adresse email pour recevoir un lien de réinitialisation sécurisé.</p>
        
        <form @submit.prevent="submitForgotPassword" class="flex flex-col gap-4">
          <input v-model="resetEmail" type="email" required style="width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.95rem;" placeholder="votre@email.com" />
          <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
            Envoyer le lien →
          </button>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useToastStore } from '../stores/toast';
import { useRouter } from 'vue-router';

const activeTab = ref('login');
const showPassword = ref(false);
const showForgotModal = ref(false);
const resetEmail = ref('');
const errorMessage = ref('');
const loading = ref(false);

const loginEmail = ref('');
const loginPassword = ref('');

const regName = ref('');
const regEmail = ref('');
const regPhone = ref('');
const regCompany = ref('');
const regAddress = ref('');
const regPassword = ref('');
const regConfirmPassword = ref('');

const auth = useAuthStore();
const toastStore = useToastStore();
const router = useRouter();

const submitForgotPassword = () => {
  showForgotModal.value = false;
  toastStore.showToast(`Un email de réinitialisation a été envoyé à ${resetEmail.value} ! 📩`, 'success');
  resetEmail.value = '';
};

const handleLogin = async () => {
  errorMessage.value = '';
  loading.value = true;
  try {
    const res = await auth.login(loginEmail.value, loginPassword.value);
    if (res.success) {
      if (['admin', 'super_admin', 'creator', 'editor', 'support'].includes(res.user?.role)) {
        await router.push('/admin');
      } else {
        await router.push('/dashboard');
      }
    } else {
      errorMessage.value = res.message || 'Identifiants invalides';
    }
  } catch (e) {
    errorMessage.value = 'Erreur de connexion';
  } finally {
    loading.value = false;
  }
};

const handleRegister = async () => {
  errorMessage.value = '';

  if (regPassword.value !== regConfirmPassword.value) {
    errorMessage.value = 'Les mots de passe ne correspondent pas.';
    return;
  }

  loading.value = true;
  try {
    const res = await auth.register(
      regName.value, 
      regEmail.value, 
      regPassword.value,
      regPhone.value,
      regCompany.value,
      regAddress.value
    );
    if (res.success) {
      toastStore.showToast('Compte créé avec succès ! Bienvenue 👋', 'success');
      await router.push('/dashboard');
    } else {
      errorMessage.value = res.message || 'Erreur lors de la création du compte';
    }
  } catch (e) {
    errorMessage.value = 'Erreur lors de la création du compte';
  } finally {
    loading.value = false;
  }
};
</script>
