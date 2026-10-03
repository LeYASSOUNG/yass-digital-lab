<template>
  <div class="login-page flex justify-center items-center" style="min-height: 88vh; position: relative; padding: 40px 20px;">
    
    <!-- Background Ambient Glow -->
    <div class="hero-glow"></div>

    <!-- Main Container -->
    <div class="fade-in main-auth-card" style="border-radius: var(--radius-lg); width: 100%; max-width: 980px; position: relative; z-index: 1; border: 1px solid var(--color-border); box-shadow: var(--shadow-xl); background: var(--color-bg-card); overflow: hidden; display: flex; flex-wrap: wrap;">
      
      <!-- LEFT SIDE: Showcase Panel -->
      <div class="auth-showcase-panel" style="flex: 1; min-width: 320px; background: var(--color-bg-elevated); padding: 48px 40px; color: var(--color-text); display: flex; flex-direction: column; justify-content: space-between; border-right: 1px solid var(--color-border); position: relative;">
        
        <div style="position: relative; z-index: 2;">
          <!-- Top Bar -->
          <div class="flex justify-between items-center mb-8">
            <router-link to="/" class="flex items-center gap-1.5" style="font-size: 0.88rem; color: var(--color-text-muted); text-decoration: none; font-weight: 600;">
              <ArrowLeft :size="16" /> Accueil
            </router-link>
            <Logo :size="36" :showTagline="false" />
          </div>

          <!-- Showcase Title -->
          <div class="mb-8">
            <span class="badge-pill badge-indigo mb-3" style="font-size: 0.76rem;">ESPACE MEMBRE SÉCURISÉ</span>
            <h1 style="font-size: 2.1rem; font-weight: 800; line-height: 1.25; color: var(--color-text); margin-bottom: 12px;">
              Accédez à vos outils et templates <span style="color: #818CF8;">Yass Digital Lab</span>.
            </h1>
            <p style="color: var(--color-text-muted); font-size: 0.96rem; line-height: 1.65;">
              Gérez vos commandes, téléchargez vos fichiers sources et accédez à vos automatisations IA en toute simplicité.
            </p>
          </div>

          <!-- Feature Bullets -->
          <div class="flex flex-col gap-4 mb-8">
            <div class="flex items-start gap-3.5 p-3.5" style="background: var(--color-bg-card); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
              <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(124,58,237,0.2); color: var(--color-primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <Zap :size="18" />
              </div>
              <div>
                <h4 style="font-size: 0.94rem; font-weight: 700; color: var(--color-text); margin: 0 0 2px;">Téléchargements Instantanés</h4>
                <p style="font-size: 0.84rem; color: var(--color-text-muted); margin: 0;">Accès immédiat à vos fichiers sources et mises à jour à vie.</p>
              </div>
            </div>

            <div class="flex items-start gap-3.5 p-3.5" style="background: var(--color-bg-card); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
              <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(16,185,129,0.2); color: #10B981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <ShieldCheck :size="18" />
              </div>
              <div>
                <h4 style="font-size: 0.94rem; font-weight: 700; color: var(--color-text); margin: 0 0 2px;">Sécurité Certifiée SSL</h4>
                <p style="font-size: 0.84rem; color: var(--color-text-muted); margin: 0;">Protection des données avec authentification chiffrée.</p>
              </div>
            </div>

            <div class="flex items-start gap-3.5 p-3.5" style="background: var(--color-bg-card); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
              <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(139,92,246,0.2); color: var(--color-primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <Headphones :size="18" />
              </div>
              <div>
                <h4 style="font-size: 0.94rem; font-weight: 700; color: var(--color-text); margin: 0 0 2px;">Support Technique Dédié</h4>
                <p style="font-size: 0.84rem; color: var(--color-text-muted); margin: 0;">Assistance réactive pour l'intégration de vos projets.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Testimonial -->
        <div style="position: relative; z-index: 2; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
          <div class="flex items-center gap-1" style="color: var(--color-accent); margin-bottom: 6px;">
            <Star v-for="n in 5" :key="n" :size="14" style="fill: var(--color-accent);" />
          </div>
          <p style="font-size: 0.86rem; font-style: italic; color: var(--color-text); margin-bottom: 4px;">
            "Plateforme ultra-fluide et sécurisée. L'espace membre est parfait !"
          </p>
          <span style="font-size: 0.8rem; font-weight: 700; color: #818CF8;">— Diarrassouba Yassoungo (Créateur)</span>
        </div>
      </div>

      <!-- RIGHT SIDE: Auth Form -->
      <div style="flex: 1.15; min-width: 340px; padding: 44px 38px; background: var(--color-bg-card); position: relative;">
        
        <div class="text-center mb-6">
          <h2 style="font-size: 1.7rem; font-weight: 800; margin-bottom: 6px; color: var(--color-text);">
            <template v-if="showOtpForm">Vérification de l'Email</template>
            <template v-else-if="activeTab === 'login'">Connexion Espace Membre</template>
            <template v-else>Créer un Compte Client</template>
          </h2>
          <p style="color: var(--color-text-muted); font-size: 0.9rem;">
            <template v-if="showOtpForm">Entrez le code OTP reçu par email</template>
            <template v-else-if="activeTab === 'login'">Saisissez vos identifiants pour accéder à vos produits</template>
            <template v-else>Rejoignez la communauté en quelques clics</template>
          </p>
        </div>

        <!-- Tabs -->
        <div v-if="!showOtpForm" class="flex gap-2 mb-6" style="background: var(--color-bg-elevated); padding: 5px; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
          <button 
            @click="activeTab = 'login'" 
            :class="activeTab === 'login' ? 'btn-primary' : 'btn-secondary'"
            style="flex: 1; padding: 9px; border-radius: var(--radius-sm); font-size: 0.88rem;"
          >
            <Lock :size="15" /> Connexion
          </button>
          <button 
            @click="activeTab = 'register'" 
            :class="activeTab === 'register' ? 'btn-primary' : 'btn-secondary'"
            style="flex: 1; padding: 9px; border-radius: var(--radius-sm); font-size: 0.88rem;"
          >
            <Sparkles :size="15" /> Créer un compte
          </button>
        </div>

        <!-- Notification Message -->
        <div v-if="errorMessage" style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); color: #EF4444; padding: 12px 14px; border-radius: var(--radius-md); margin-bottom: 20px; text-align: center; font-size: 0.88rem; font-weight: 600;" class="flex items-center justify-center gap-2">
          <AlertTriangle :size="18" /> {{ errorMessage }}
        </div>

        <!-- FORM 3 : VERIFICATION OTP -->
        <form v-if="showOtpForm" @submit.prevent="handleVerifyOtp" class="flex flex-col gap-4">
          <div>
            <label style="display: block; margin-bottom: 6px; font-size: 0.88rem; text-align: center;">Code de vérification (6 chiffres)</label>
            <input 
              v-model="otpCode" 
              type="text" 
              maxlength="6"
              required 
              style="width: 100%; text-align: center; letter-spacing: 4px; padding: 12px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 1.2rem; font-weight: bold;" 
              placeholder="123456" 
            />
          </div>

          <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="width: 100%; margin-top: 6px; padding: 12px 20px; font-size: 0.96rem;" :disabled="loading">
            <span>{{ loading ? 'Vérification...' : 'Valider mon compte' }}</span>
            <ArrowRight v-if="!loading" :size="18" />
          </button>

          <div style="text-align: center; margin-top: 15px;">
             <button type="button" @click="handleResendOtp" style="background: none; border: none; color: var(--color-primary); font-size: 0.85rem; cursor: pointer; text-decoration: underline;">
                Renvoyer le code OTP
             </button>
          </div>
        </form>

        <!-- FORM 1 : CONNEXION -->
        <form v-else-if="activeTab === 'login'" @submit.prevent="handleLogin" class="flex flex-col gap-4">
          <div>
            <label style="display: block; margin-bottom: 6px; font-size: 0.88rem;">Adresse Email</label>
            <div style="position: relative;">
              <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none; display: flex; align-items: center;">
                <Mail :size="18" />
              </div>
              <input 
                v-model="loginEmail" 
                type="email" 
                required 
                style="width: 100%; padding: 11px 16px 11px 42px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" 
                placeholder="votre@email.com" 
              />
            </div>
          </div>
          
          <div>
            <div class="flex justify-between items-center mb-1">
              <label style="font-size: 0.88rem;">Mot de passe</label>
              <router-link to="/forgot-password" style="color: var(--color-primary); font-size: 0.82rem; font-weight: 600;">
                Mot de passe oublié ?
              </router-link>
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

          <div class="flex justify-between items-center mt-1">
            <label class="flex items-center gap-2" style="cursor: pointer; font-size: 0.86rem; color: var(--color-text-light); font-weight: 500;">
              <input v-model="rememberMe" type="checkbox" style="width: 16px; height: 16px; accent-color: var(--color-primary);" />
              Se souvenir de moi
            </label>
          </div>

          <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="width: 100%; margin-top: 6px; padding: 12px 20px; font-size: 0.96rem;" :disabled="loading">
            <span>{{ loading ? 'Connexion en cours...' : 'Se connecter à l\'espace' }}</span>
            <ArrowRight v-if="!loading" :size="18" />
          </button>

          <!-- Social logins -->
          <div style="margin-top: 10px; text-align: center;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
              <div style="flex: 1; height: 1px; background: var(--color-border);"></div>
              <span style="font-size: 0.76rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 600;">ou continuer avec</span>
              <div style="flex: 1; height: 1px; background: var(--color-border);"></div>
            </div>
            <div class="flex gap-3">
              <button @click="socialLogin('google')" type="button" class="btn btn-secondary" style="flex: 1; padding: 9px; font-size: 0.85rem;" :disabled="loading">
                <Globe :size="16" style="color: #ea4335;" /> Google
              </button>
              <button @click="socialLogin('github')" type="button" class="btn btn-secondary" style="flex: 1; padding: 9px; font-size: 0.85rem;" :disabled="loading">
                <Github :size="16" /> GitHub
              </button>
            </div>
          </div>
        </form>

        <!-- FORM 2 : INSCRIPTION -->
        <form v-else @submit.prevent="handleRegister" class="flex flex-col gap-3">
          <div>
            <label style="display: block; margin-bottom: 4px; font-size: 0.86rem;">Nom complet *</label>
            <div style="position: relative;">
              <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                <User :size="16" />
              </div>
              <input v-model="regName" type="text" required style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="ex: Jean Dupont" />
            </div>
          </div>

          <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
              <label style="display: block; margin-bottom: 4px; font-size: 0.86rem;">Email *</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                  <Mail :size="16" />
                </div>
                <input v-model="regEmail" type="email" required style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="jean@example.com" />
              </div>
            </div>
            <div>
              <label style="display: block; margin-bottom: 4px; font-size: 0.86rem;">Téléphone</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                  <Phone :size="16" />
                </div>
                <input v-model="regPhone" type="tel" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="+33 6 12 34 56 78" />
              </div>
            </div>
          </div>

          <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
              <label style="display: block; margin-bottom: 4px; font-size: 0.86rem;">Société</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                  <Building :size="16" />
                </div>
                <input v-model="regCompany" type="text" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Société" />
              </div>
            </div>
            <div>
              <label style="display: block; margin-bottom: 4px; font-size: 0.86rem;">Adresse</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                  <MapPin :size="16" />
                </div>
                <input v-model="regAddress" type="text" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Paris, France" />
              </div>
            </div>
          </div>
          
          <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
              <label style="display: block; margin-bottom: 4px; font-size: 0.86rem;">Mot de passe *</label>
              <input v-model="regPassword" type="password" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Min. 6 car." />
            </div>
            <div>
              <label style="display: block; margin-bottom: 4px; font-size: 0.86rem;">Confirmation *</label>
              <input v-model="regPasswordConfirm" type="password" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Répéter" />
            </div>
          </div>

          <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="width: 100%; margin-top: 8px; padding: 12px; font-size: 0.95rem;" :disabled="loading">
            <span>{{ loading ? 'Création en cours...' : 'Créer mon compte client' }}</span>
            <ArrowRight v-if="!loading" :size="18" />
          </button>
        </form>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useToastStore } from '../stores/toast';
import api from '../api';
import Logo from '../components/Logo.vue';
import { 
  ArrowLeft, 
  ArrowRight, 
  Zap, 
  ShieldCheck, 
  Headphones, 
  Star, 
  Lock, 
  Sparkles, 
  Mail, 
  Eye, 
  EyeOff, 
  Globe, 
  Github, 
  User, 
  Phone, 
  Building, 
  MapPin, 
  AlertTriangle 
} from 'lucide-vue-next';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const toastStore = useToastStore();

const activeTab = ref('login');
const loading = ref(false);
const errorMessage = ref('');
const showPassword = ref(false);
const rememberMe = ref(true);

const loginEmail = ref('');
const loginPassword = ref('');

const regName = ref('');
const regEmail = ref('');
const regPhone = ref('');
const regCompany = ref('');
const regAddress = ref('');
const regPassword = ref('');
const regPasswordConfirm = ref('');

const showOtpForm = ref(false);
const otpCode = ref('');

onMounted(() => {
  if (route.query.tab === 'register') {
    activeTab.value = 'register';
  }
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
      if (!res.user.email_verified_at) {
        toastStore.showToast('Votre compte n\'est pas vérifié. Un code OTP a été envoyé.', 'info');
        regEmail.value = loginEmail.value;
        await authStore.resendOtp(loginEmail.value);
        showOtpForm.value = true;
      } else {
        toastStore.showToast('Connexion réussie ! Bienvenue.', 'success');
        router.push('/dashboard');
      }
    } else {
      errorMessage.value = res.message || 'Identifiants incorrects ou compte introuvable.';
      toastStore.showToast(errorMessage.value, 'error');
    }
  } catch (err) {
    errorMessage.value = err.message || 'Erreur inconnue.';
    toastStore.showToast(errorMessage.value, 'error');
  } finally {
    loading.value = false;
  }
};

const handleRegister = async () => {
  if (regPassword.value !== regPasswordConfirm.value) {
    errorMessage.value = 'Les mots de passe ne correspondent pas.';
    return;
  }
  loading.value = true;
  errorMessage.value = '';
  try {
    const res = await authStore.register({
      name: regName.value,
      email: regEmail.value,
      phone: regPhone.value,
      company: regCompany.value,
      address: regAddress.value,
      password: regPassword.value,
      password_confirmation: regPasswordConfirm.value
    });
    
    if (res.success) {
      toastStore.showToast('Compte créé ! Veuillez vérifier votre email.', 'info');
      showOtpForm.value = true;
    } else {
      errorMessage.value = res.message || 'Erreur lors de l\'inscription.';
      toastStore.showToast(errorMessage.value, 'error');
    }
  } catch (err) {
    errorMessage.value = err.message || 'Erreur lors de l\'inscription.';
    toastStore.showToast(errorMessage.value, 'error');
  } finally {
    loading.value = false;
  }
};

const handleVerifyOtp = async () => {
  if (!otpCode.value || otpCode.value.length < 5) {
    errorMessage.value = 'Veuillez entrer un code OTP valide.';
    return;
  }
  loading.value = true;
  errorMessage.value = '';
  try {
    const res = await authStore.verifyOtp(regEmail.value, otpCode.value);
    
    if (res.success) {
      toastStore.showToast('Compte vérifié avec succès ! Bienvenue.', 'success');
      router.push('/dashboard');
    } else {
      errorMessage.value = res.message || 'Code OTP invalide.';
      toastStore.showToast(errorMessage.value, 'error');
    }
  } catch (err) {
    errorMessage.value = err.message || 'Erreur lors de la vérification.';
    toastStore.showToast(errorMessage.value, 'error');
  } finally {
    loading.value = false;
  }
};

const handleResendOtp = async () => {
  try {
    const res = await authStore.resendOtp(regEmail.value);
    if (res.success) {
      toastStore.showToast('Un nouveau code OTP a été envoyé.', 'info');
    } else {
      toastStore.showToast(res.message || 'Erreur lors du renvoi du code.', 'error');
    }
  } catch (err) {
    toastStore.showToast('Erreur serveur lors du renvoi du code.', 'error');
  }
};

const socialLogin = async (provider) => {
  loading.value = true;
  try {
    const res = await api.get(`/auth/${provider}/redirect`);
    if (res.data.url) {
      toastStore.showToast(`Redirection vers ${provider}...`, 'info');
      window.location.href = res.data.url;
    }
  } catch (err) {
    console.error("Social login error:", err);
    loading.value = false;
    errorMessage.value = `Erreur (${err.name}): ${err.message || err}. Vérifiez votre bloqueur de pub.`;
    toastStore.showToast(errorMessage.value, 'error');
  }
};
</script>

<style scoped>
.login-page {
  background: var(--page-gradient);
  padding: 40px 0 80px;
  color: var(--color-text);
}

.main-auth-card {
  background: var(--color-bg-elevated);
  border: 1px solid rgba(255, 255, 255, 0.09);
  backdrop-filter: blur(24px);
}
</style>

