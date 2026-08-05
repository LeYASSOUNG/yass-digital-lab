<template>
  <div class="login-page flex justify-center items-center" style="min-height: 88vh; position: relative; padding: 40px 20px;">
    
    <!-- Background Ambient Glow & Decorative Elements -->
    <div style="position: absolute; width: 600px; height: 600px; background: radial-gradient(circle, rgba(212,175,55,0.18), transparent 70%); top: 50%; left: 50%; transform: translate(-50%, -50%); pointer-events: none;"></div>
    <div style="position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, rgba(16,185,129,0.10), transparent 70%); top: 20%; right: 15%; pointer-events: none;"></div>

    <!-- Main Container: Dual Column Layout for Large Screens, Single Column for Mobile -->
    <div class="glass fade-in main-auth-card" style="border-radius: 28px; width: 100%; max-width: 960px; position: relative; z-index: 1; border: 1px solid var(--color-border); box-shadow: var(--shadow-lg); overflow: hidden; display: flex; flex-wrap: wrap;">
      
      <!-- LEFT SIDE: Showcase & Brand Benefits -->
      <div class="auth-showcase-panel" style="flex: 1; min-width: 320px; background: linear-gradient(135deg, rgba(15,23,42,0.92) 0%, rgba(5,8,17,0.95) 100%); padding: 48px 40px; color: #FFFFFF; display: flex; flex-direction: column; justify-content: space-between; border-right: 1px solid rgba(212,175,55,0.2); position: relative;">
        
        <!-- Ambient Light Overlay -->
        <div style="position: absolute; inset: 0; background: radial-gradient(circle at top left, rgba(212,175,55,0.15), transparent 60%); pointer-events: none;"></div>

        <div style="position: relative; z-index: 2;">
          <!-- Top Back & Logo Bar -->
          <div class="flex justify-between items-center mb-8">
            <router-link to="/" class="flex items-center gap-1" style="font-size: 0.9rem; color: rgba(255,255,255,0.8); text-decoration: none; font-weight: 600; transition: color 0.2s;">
              <ArrowLeft :size="16" /> Retour à l'accueil
            </router-link>
            <Logo :size="36" :showTagline="false" :inverted="true" />
          </div>

          <!-- Showcase Title -->
          <div class="mb-8">
            <span style="background: rgba(212,175,55,0.18); color: #F0CC55; border: 1px solid rgba(212,175,55,0.4); padding: 4px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 12px;">
              Espace Membre Officiel
            </span>
            <h1 style="font-size: 2rem; font-weight: 700; line-height: 1.25; color: #FFFFFF; margin-bottom: 12px;">
              Accédez à la puissance numérique de <span style="color: #F0CC55;">Yass Digital Lab</span>.
            </h1>
            <p style="color: rgba(255,255,255,0.85); font-size: 0.98rem; line-height: 1.6;">
              Gérez vos commandes, téléchargez vos templates SaaS et accédez à vos outils IA en toute simplicité.
            </p>
          </div>

          <!-- Feature Bullets -->
          <div class="flex flex-col gap-4 mb-8">
            <div class="flex items-start gap-3 p-3" style="background: rgba(255,255,255,0.04); border-radius: 14px; border: 1px solid rgba(255,255,255,0.08);">
              <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(212,175,55,0.2); color: #F0CC55; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <Zap :size="18" />
              </div>
              <div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: #FFFFFF; margin: 0 0 2px;">Téléchargements Instantanés</h4>
                <p style="font-size: 0.84rem; color: rgba(255,255,255,0.75); margin: 0;">Accès direct à vos fichiers sources, clés d'activation et livrables.</p>
              </div>
            </div>

            <div class="flex items-start gap-3 p-3" style="background: rgba(255,255,255,0.04); border-radius: 14px; border: 1px solid rgba(255,255,255,0.08);">
              <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16,185,129,0.2); color: #10B981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <ShieldCheck :size="18" />
              </div>
              <div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: #FFFFFF; margin: 0 0 2px;">Sécurité Maximale Certifiée</h4>
                <p style="font-size: 0.84rem; color: rgba(255,255,255,0.75); margin: 0;">Protection des données de niveau bancaire (SSL 256-bit).</p>
              </div>
            </div>

            <div class="flex items-start gap-3 p-3" style="background: rgba(255,255,255,0.04); border-radius: 14px; border: 1px solid rgba(255,255,255,0.08);">
              <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59,130,246,0.2); color: #60A5FA; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <Headphones :size="18" />
              </div>
              <div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: #FFFFFF; margin: 0 0 2px;">Support Technique Dédié 24/7</h4>
                <p style="font-size: 0.84rem; color: rgba(255,255,255,0.75); margin: 0;">Équipe disponible en direct pour vous accompagner dans vos projets.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Testimonial Snippet -->
        <div style="position: relative; z-index: 2; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.15);">
          <div class="flex items-center gap-1" style="color: #F0CC55; margin-bottom: 6px;">
            <Star v-for="n in 5" :key="n" :size="14" style="fill: #F0CC55;" />
          </div>
          <p style="font-size: 0.86rem; font-style: italic; color: rgba(255,255,255,0.9); margin-bottom: 6px;">
            "Plateforme exceptionnellement fluide et sécurisée. L'espace membre est parfait !"
          </p>
          <span style="font-size: 0.8rem; font-weight: 700; color: #F0CC55;">— Diarrassouba Yassoungo (Créateur)</span>
        </div>
      </div>

      <!-- RIGHT SIDE: Auth Form Container -->
      <div style="flex: 1.1; min-width: 340px; padding: 44px 38px; background: var(--color-bg-card); position: relative;">
        
        <div class="text-center mb-6">
          <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 6px; color: var(--color-primary);">
            {{ activeTab === 'login' ? 'Connexion Espace Membre 👋' : 'Créer un Compte Client ✨' }}
          </h2>
          <p style="color: var(--color-text-light); font-size: 0.92rem;">
            {{ activeTab === 'login' ? 'Remplissez vos identifiants pour continuer' : 'Renseignez vos informations ci-dessous' }}
          </p>
        </div>

        <!-- Mode Switcher Tabs (Connexion / Inscription) -->
        <div class="flex gap-2 mb-6" style="background: rgba(0,0,0,0.06); padding: 5px; border-radius: 14px; border: 1px solid var(--color-border);">
          <button 
            @click="activeTab = 'login'" 
            :style="activeTab === 'login' ? 'background: var(--color-bg); color: var(--color-accent); font-weight: 700; box-shadow: var(--shadow-sm);' : 'color: var(--color-text-light); font-weight: 600;'"
            style="flex: 1; border: none; padding: 10px 14px; border-radius: 10px; cursor: pointer; font-size: 0.9rem; transition: all 0.25s; display: inline-flex; align-items: center; justify-content: center; gap: 8px;"
          >
            <Lock :size="16" /> Connexion
          </button>
          <button 
            @click="activeTab = 'register'" 
            :style="activeTab === 'register' ? 'background: var(--color-bg); color: var(--color-accent); font-weight: 700; box-shadow: var(--shadow-sm);' : 'color: var(--color-text-light); font-weight: 600;'"
            style="flex: 1; border: none; padding: 10px 14px; border-radius: 10px; cursor: pointer; font-size: 0.9rem; transition: all 0.25s; display: inline-flex; align-items: center; justify-content: center; gap: 8px;"
          >
            <Sparkles :size="16" /> Créer un compte
          </button>
        </div>

        <!-- Notification Message -->
        <div v-if="errorMessage" style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); color: #ef4444; padding: 12px 14px; border-radius: 12px; margin-bottom: 20px; text-align: center; font-size: 0.88rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
          <AlertTriangle :size="18" /> {{ errorMessage }}
        </div>

        <!-- FORM 1 : CONNEXION -->
        <form v-if="activeTab === 'login'" @submit.prevent="handleLogin" class="flex flex-col gap-4">
          <div>
            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.92rem; color: var(--color-text);">Adresse Email</label>
            <div style="position: relative;">
              <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none; display: flex; align-items: center;">
                <Mail :size="18" />
              </div>
              <input 
                v-model="loginEmail" 
                type="email" 
                required 
                style="width: 100%; padding: 12px 16px 12px 42px; border-radius: 12px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.95rem;" 
                placeholder="votre@email.com" 
              />
            </div>
          </div>
          
          <div>
            <div class="flex justify-between items-center mb-1">
              <label style="font-weight: 600; font-size: 0.92rem; color: var(--color-text);">Mot de passe</label>
              <router-link to="/forgot-password" style="color: var(--color-accent); font-size: 0.82rem; text-decoration: underline; font-weight: 600;">
                Mot de passe oublié ?
              </router-link>
            </div>
            <div style="position: relative;">
              <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none; display: flex; align-items: center;">
                <Lock :size="18" />
              </div>
              <input 
                v-model="loginPassword" 
                :type="showPassword ? 'text' : 'password'" 
                required 
                style="width: 100%; padding: 12px 42px 12px 42px; border-radius: 12px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.95rem;" 
                placeholder="••••••••" 
              />
              <button type="button" @click="showPassword = !showPassword" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--color-text-light); display: flex; align-items: center; justify-content: center;" title="Afficher/Masquer">
                <EyeOff v-if="showPassword" :size="18" />
                <Eye v-else :size="18" />
              </button>
            </div>
          </div>

          <div class="flex justify-between items-center mt-1">
            <label class="flex items-center gap-2" style="cursor: pointer; font-size: 0.88rem; color: var(--color-text-light); font-weight: 500;">
              <input v-model="rememberMe" type="checkbox" style="width: 16px; height: 16px; accent-color: var(--color-accent);" />
              Se souvenir de moi
            </label>
          </div>

          <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="width: 100%; margin-top: 6px; padding: 13px 20px; font-size: 1rem;" :disabled="loading">
            <span>{{ loading ? 'Connexion en cours...' : 'Se connecter à l\'espace' }}</span>
            <ArrowRight v-if="!loading" :size="18" />
          </button>

          <!-- Social logins -->
          <div style="margin-top: 10px; text-align: center;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
              <div style="flex: 1; height: 1px; background: var(--color-border);"></div>
              <span style="font-size: 0.76rem; color: var(--color-text-light); text-transform: uppercase; font-weight: 600;">ou continuer avec</span>
              <div style="flex: 1; height: 1px; background: var(--color-border);"></div>
            </div>
            <div class="flex gap-3">
              <button @click="socialLogin('Google')" type="button" style="flex: 1; padding: 10px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.85rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: border-color 0.2s;">
                <Globe :size="16" style="color: #ea4335;" /> Google
              </button>
              <button @click="socialLogin('GitHub')" type="button" style="flex: 1; padding: 10px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.85rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: border-color 0.2s;">
                <Github :size="16" /> GitHub
              </button>
            </div>
          </div>
        </form>

        <!-- FORM 2 : CREATION DE COMPTE -->
        <form v-else @submit.prevent="handleRegister" class="flex flex-col gap-3">
          <div>
            <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.88rem; color: var(--color-text);">Nom complet *</label>
            <div style="position: relative;">
              <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                <User :size="16" />
              </div>
              <input v-model="regName" type="text" required style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="ex: Jean Dupont" />
            </div>
          </div>

          <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
              <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.88rem; color: var(--color-text);">Email *</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                  <Mail :size="16" />
                </div>
                <input v-model="regEmail" type="email" required style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="jean@example.com" />
              </div>
            </div>
            <div>
              <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.88rem; color: var(--color-text);">Téléphone</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                  <Phone :size="16" />
                </div>
                <input v-model="regPhone" type="tel" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="+33 6 12 34 56 78" />
              </div>
            </div>
          </div>

          <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
              <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.88rem; color: var(--color-text);">Entreprise</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                  <Building :size="16" />
                </div>
                <input v-model="regCompany" type="text" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Société" />
              </div>
            </div>
            <div>
              <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.88rem; color: var(--color-text);">Adresse</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                  <MapPin :size="16" />
                </div>
                <input v-model="regAddress" type="text" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Paris, France" />
              </div>
            </div>
          </div>
          
          <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
              <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.88rem; color: var(--color-text);">Mot de passe *</label>
              <input v-model="regPassword" type="password" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Min. 6 car." />
            </div>
            <div>
              <label style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 0.88rem; color: var(--color-text);">Confirmation *</label>
              <input v-model="regConfirmPassword" type="password" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem;" placeholder="Répéter" />
            </div>
          </div>

          <!-- Password strength bar -->
          <div v-if="regPassword" class="mt-1 mb-1">
            <div style="height: 4px; border-radius: 999px; background: rgba(0,0,0,0.1); overflow: hidden; display: flex;">
              <div :style="{ width: passwordStrength.pct + '%', background: passwordStrength.color }" style="height: 100%; transition: all 0.3s;"></div>
            </div>
            <span style="font-size: 0.76rem; font-weight: 600; display: block; margin-top: 3px;" :style="{ color: passwordStrength.color }">
              Force du mot de passe : {{ passwordStrength.label }}
            </span>
          </div>

          <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="width: 100%; margin-top: 8px; padding: 12px 20px; font-size: 0.98rem;" :disabled="loading">
            <span>{{ loading ? 'Création...' : 'Créer mon compte Client' }}</span>
            <ArrowRight v-if="!loading" :size="18" />
          </button>
        </form>

        <!-- Bottom Security Badge -->
        <div style="margin-top: 22px; text-align: center; font-size: 0.78rem; color: var(--color-text-light); display: flex; align-items: center; justify-content: center; gap: 12px;">
          <span class="flex items-center gap-1"><ShieldCheck :size="14" style="color: var(--color-success);" /> SSL 256-bit</span>
          <span>•</span>
          <span class="flex items-center gap-1"><Zap :size="14" style="color: var(--color-accent);" /> Cloud Sync</span>
        </div>

      </div>

    </div>

    <!-- Modal Mot de passe oublié -->
    <div v-if="showForgotModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px;">
      <div class="glass fade-in" style="padding: 36px; border-radius: 24px; width: 100%; max-width: 420px; position: relative; border: 1px solid var(--color-accent);">
        <button @click="showForgotModal = false" style="position: absolute; right: 18px; top: 18px; background: none; border: none; cursor: pointer; color: var(--color-text-light);" title="Fermer">
          <X :size="20" />
        </button>
        <h3 class="mb-2 flex items-center gap-2" style="font-size: 1.3rem;"><Lock :size="22" style="color: var(--color-accent);" /> Mot de passe oublié ?</h3>
        <p style="color: var(--color-text-light); font-size: 0.92rem; margin-bottom: 22px; line-height: 1.6;">Saisissez votre adresse email pour recevoir un lien de réinitialisation sécurisé instantané.</p>
        
        <form @submit.prevent="submitForgotPassword" class="flex flex-col gap-4">
          <div>
            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Votre Adresse Email</label>
            <div style="position: relative;">
              <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                <Mail :size="18" />
              </div>
              <input v-model="resetEmail" type="email" required style="width: 100%; padding: 12px 16px 12px 42px; border-radius: 12px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.95rem;" placeholder="votre@email.com" />
            </div>
          </div>
          <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="width: 100%; padding: 12px 20px;">
            <span>Envoyer le lien de secours</span> <ArrowRight :size="18" />
          </button>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useToastStore } from '../stores/toast';
import { useRouter } from 'vue-router';
import Logo from '../components/Logo.vue';
import { 
  Lock, 
  Sparkles, 
  AlertTriangle, 
  Eye, 
  EyeOff, 
  ArrowRight, 
  ArrowLeft,
  X, 
  Mail, 
  User, 
  Phone, 
  Building, 
  MapPin, 
  ShieldCheck, 
  Zap,
  Headphones,
  Star,
  Globe, 
  Github 
} from 'lucide-vue-next';

const activeTab = ref('login');
const showPassword = ref(false);
const showForgotModal = ref(false);
const rememberMe = ref(true);
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

const passwordStrength = computed(() => {
  const p = regPassword.value;
  if (!p) return { label: 'Inexistant', pct: 0, color: '#e2e8f0' };
  if (p.length < 6) return { label: 'Faible', pct: 33, color: '#ef4444' };
  if (p.length < 10 || !/\d/.test(p)) return { label: 'Moyen', pct: 66, color: '#f59e0b' };
  return { label: 'Très Fort 💪', pct: 100, color: '#10b981' };
});

const socialLogin = (provider) => {
  toastStore.showToast(`Connexion avec ${provider} bientôt disponible !`, 'info');
};

const submitForgotPassword = () => {
  showForgotModal.value = false;
  toastStore.showToast(`Un email de réinitialisation a été envoyé à ${resetEmail.value} !`, 'success');
  resetEmail.value = '';
};

const handleLogin = async () => {
  errorMessage.value = '';
  loading.value = true;
  try {
    const res = await auth.login(loginEmail.value, loginPassword.value);
    if (res.success) {
      toastStore.showToast(`Bienvenue ${res.user?.name || ''} !`, 'success');
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

<style scoped>
@media (max-width: 850px) {
  .auth-showcase-panel {
    display: none !important;
  }
  .main-auth-card {
    max-width: 480px !important;
  }
}
</style>
