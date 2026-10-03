<template>
  <div class="verify-page">

    <!-- Background orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="verify-card fade-in">

      <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           ÉTAT 1 : Chargement (vérification en cours)
      â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
      <div v-if="status === 'loading'" class="state-block">
        <div class="icon-badge icon-loading">
          <Loader2Icon :size="36" class="spin-icon" />
        </div>
        <h1 class="state-title">Vérification en coursâFCFA¦</h1>
        <p class="state-desc">Nous validons votre lien d'activation. Veuillez patienter quelques instants.</p>
        <div class="progress-bar"><div class="progress-fill"></div></div>
      </div>

      <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           ÉTAT 2 : Succès
      â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
      <div v-else-if="status === 'success'" class="state-block fade-in">
        <div class="icon-badge icon-success">
          <CheckCircle2Icon :size="40" />
        </div>
        <div class="badge-pill badge-emerald" style="margin-bottom: 16px;">Compte Activé</div>
        <h1 class="state-title">Email vérifié avec succès !</h1>
        <p class="state-desc">
          Votre compte est désormais totalement actif. Vous avez maintenant accès à l'ensemble des fonctionnalités de Yass Digital Lab.
        </p>

        <!-- What's unlocked -->
        <div class="unlocked-grid">
          <div class="unlocked-item">
            <PackageIcon :size="18" style="color: var(--color-primary);" />
            <span>Téléchargements & Licences</span>
          </div>
          <div class="unlocked-item">
            <Share2Icon :size="18" style="color: var(--color-primary);" />
            <span>Programme d'Affiliation</span>
          </div>
          <div class="unlocked-item">
            <StarIcon :size="18" style="color: var(--color-primary);" />
            <span>Avis Acheteur Vérifié</span>
          </div>
        </div>

        <div class="action-row">
          <router-link to="/dashboard" class="btn-primary verify-btn">
            <UserIcon :size="18" /> Accéder à mon Espace Client
          </router-link>
          <router-link to="/products" class="btn-secondary verify-btn">
            <PackageIcon :size="18" /> Voir le Catalogue
          </router-link>
        </div>
      </div>

      <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           ÉTAT 3 : En attente (connecté, email non vérifié)
      â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
      <div v-else-if="status === 'pending'" class="state-block fade-in">
        <div class="icon-badge icon-pending">
          <MailIcon :size="38" />
        </div>
        <div class="badge-pill badge-gold" style="margin-bottom: 16px;">Vérification Requise</div>
        <h1 class="state-title">Confirmez votre adresse email</h1>
        <p class="state-desc">
          Un email de confirmation a été envoyé à <strong>{{ userEmail }}</strong>. Cliquez sur le lien dans l'email pour activer votre compte.
        </p>

        <div class="email-hint">
          <MailCheckIcon :size="18" style="color: var(--color-accent); flex-shrink: 0;" />
          <div>
            <p style="font-size: 0.85rem; font-weight: 700; margin-bottom: 2px;">Vérifiez votre boîte mail</p>
            <p style="font-size: 0.8rem; color: var(--color-text-muted);">Pensez également à vérifier vos spams ou courriers indésirables.</p>
          </div>
        </div>

        <button
          @click="resendEmail"
          :disabled="resending || resendCooldown > 0"
          class="btn-primary verify-btn"
          style="margin-top: 8px;"
        >
          <Loader2Icon v-if="resending" :size="18" class="spin-icon" />
          <MailIcon v-else :size="18" />
          {{ resending ? 'Envoi en coursâFCFA¦' : resendCooldown > 0 ? `Renvoyer (${resendCooldown}s)` : 'Renvoyer l\'email de vérification' }}
        </button>

        <p v-if="resendSuccess" class="resend-success">
          <CheckCircle2Icon :size="15" /> Email renvoyé avec succès !
        </p>
      </div>

      <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           ÉTAT 4 : Erreur (lien expiré / invalide)
      â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
      <div v-else class="state-block fade-in">
        <div class="icon-badge icon-error">
          <AlertCircleIcon :size="38" />
        </div>
        <div class="badge-pill" style="background: rgba(239,68,68,0.12); color: #EF4444; border: 1px solid rgba(239,68,68,0.3); margin-bottom: 16px;">Lien Invalide</div>
        <h1 class="state-title">Lien de vérification invalide</h1>
        <p class="state-desc">{{ errorMessage }}</p>

        <div class="action-row">
          <button @click="resendEmail" :disabled="resending" class="btn-primary verify-btn">
            <MailIcon :size="18" />
            {{ resending ? 'EnvoiâFCFA¦' : 'Recevoir un nouveau lien' }}
          </button>
          <router-link to="/" class="btn-secondary verify-btn">
            <HomeIcon :size="18" /> Retour à l'accueil
          </router-link>
        </div>

        <p v-if="resendSuccess" class="resend-success">
          <CheckCircle2Icon :size="15" /> Nouveau lien envoyé à votre adresse email !
        </p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';
import {
  CheckCircle2  as CheckCircle2Icon,
  AlertCircle   as AlertCircleIcon,
  Loader2       as Loader2Icon,
  Mail          as MailIcon,
  MailCheck     as MailCheckIcon,
  Package       as PackageIcon,
  GraduationCap as GraduationCapIcon,
  Share2        as Share2Icon,
  Star          as StarIcon,
  User          as UserIcon,
  Home          as HomeIcon,
} from 'lucide-vue-next';

const route   = useRoute();
const router  = useRouter();

// 'loading' | 'success' | 'pending' | 'error'
const status       = ref('loading');
const errorMessage = ref('Le lien a expiré ou est invalide. Demandez un nouveau lien ci-dessous.');
const resending    = ref(false);
const resendSuccess = ref(false);
const resendCooldown = ref(0);
let cooldownTimer = null;

const userEmail = ref(
  JSON.parse(localStorage.getItem('user') || 'null')?.email || ''
);

// â”FCFAâ”FCFAâ”FCFA Renvoi de l'email â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA
const startCooldown = () => {
  resendCooldown.value = 60;
  cooldownTimer = setInterval(() => {
    resendCooldown.value--;
    if (resendCooldown.value <= 0) clearInterval(cooldownTimer);
  }, 1000);
};

const resendEmail = async () => {
  if (resendCooldown.value > 0) return;
  resending.value = true;
  resendSuccess.value = false;
  try {
    await api.post('/email/verification-notification');
    resendSuccess.value = true;
    startCooldown();
    setTimeout(() => { resendSuccess.value = false; }, 5000);
  } catch (e) {
    // Si non connecté, on redirige vers la connexion
    if (e.response?.status === 401) {
      router.push({ name: 'Login', query: { redirect: '/verify-email' } });
    }
  } finally {
    resending.value = false;
  }
};

// â”FCFAâ”FCFAâ”FCFA Vérification au montage â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA
onMounted(async () => {
  const signedUrl = route.query.url;
  const token     = localStorage.getItem('token');

  // Cas 1 : lien de vérification fourni dans l'URL → vérification active
  if (signedUrl) {
    try {
      const res  = await fetch(decodeURIComponent(signedUrl), {
        headers: { Accept: 'application/json' },
      });
      const data = await res.json();
      if (res.ok) {
        status.value = 'success';
      } else {
        errorMessage.value = data.message || errorMessage.value;
        status.value = 'error';
      }
    } catch {
      errorMessage.value = 'Erreur réseau lors de la vérification.';
      status.value = 'error';
    }
    return;
  }

  // Cas 2 : utilisateur connecté mais email non vérifié → état pending
  if (token) {
    status.value = 'pending';
    return;
  }

  // Cas 3 : aucun token, aucun lien → erreur générique
  status.value = 'error';
});

onUnmounted(() => {
  if (cooldownTimer) clearInterval(cooldownTimer);
});
</script>

<style scoped>
.verify-page {
  min-height: calc(100vh - var(--nav-height));
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  position: relative;
  overflow: hidden;
}

/* Orbs */
.orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(90px);
  opacity: 0.2;
  pointer-events: none;
  animation: float 10s ease-in-out infinite;
}
.orb-1 { width: 350px; height: 350px; background: radial-gradient(circle, #6366F1, transparent); top: -60px; left: -80px; }
.orb-2 { width: 280px; height: 280px; background: radial-gradient(circle, #D4AF37, transparent); bottom: -60px; right: -60px; animation-delay: -4s; }
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-20px); }
}

/* Card */
.verify-card {
  position: relative;
  z-index: 1;
  max-width: 560px;
  width: 100%;
  padding: 52px 44px;
  border-radius: var(--radius-xl);
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  box-shadow: var(--shadow-xl);
}

.state-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 12px;
}

/* Icon badges */
.icon-badge {
  width: 80px; height: 80px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
}
.icon-loading { background: rgba(212,175,55,0.12); border: 2px solid var(--color-accent); color: var(--color-accent); }
.icon-success { background: rgba(16,185,129,0.12); border: 2px solid #10B981; color: #10B981; animation: pulse-success 2s ease-in-out infinite; }
.icon-pending { background: rgba(99,102,241,0.12); border: 2px solid var(--color-primary); color: var(--color-primary); }
.icon-error   { background: rgba(239,68,68,0.12);  border: 2px solid #EF4444; color: #EF4444; }

@keyframes pulse-success {
  0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.35); }
  50%       { box-shadow: 0 0 0 14px rgba(16,185,129,0); }
}

.spin-icon { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Titles */
.state-title { font-size: 1.7rem; font-weight: 800; font-family: var(--font-heading); color: var(--color-text); margin: 0; }
.state-desc  { color: var(--color-text-muted); font-size: 0.95rem; line-height: 1.7; margin: 0; max-width: 400px; }

/* Progress bar */
.progress-bar {
  width: 100%;
  max-width: 300px;
  height: 4px;
  background: var(--color-border);
  border-radius: 2px;
  overflow: hidden;
  margin-top: 8px;
}
.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--color-primary), var(--color-secondary));
  animation: progress-anim 2s ease-in-out infinite;
  border-radius: 2px;
}
@keyframes progress-anim {
  0%   { width: 0%; }
  50%  { width: 70%; }
  100% { width: 100%; }
}

/* Unlocked features grid */
.unlocked-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  width: 100%;
  margin: 8px 0;
}
.unlocked-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
  background: rgba(99,102,241,0.04);
  font-size: 0.83rem;
  font-weight: 600;
  color: var(--color-text);
  text-align: left;
}

/* Email hint box */
.email-hint {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px 18px;
  border-radius: var(--radius-md);
  border: 1px solid rgba(212,175,55,0.25);
  background: rgba(212,175,55,0.05);
  width: 100%;
  text-align: left;
}

/* Actions */
.action-row {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
  width: 100%;
  margin-top: 8px;
}
.verify-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 22px;
  border-radius: var(--radius-pill);
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  text-decoration: none;
  transition: all 200ms ease;
  border: none;
  font-family: var(--font-body);
}
.btn-primary.verify-btn  { background: var(--color-primary); color: var(--color-text); }
.btn-primary.verify-btn:hover  { background: var(--color-primary-hover); transform: translateY(-2px); box-shadow: var(--shadow-glow); }
.btn-primary.verify-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.btn-secondary.verify-btn { background: transparent; color: var(--color-text-muted); border: 1px solid var(--color-border); }
.btn-secondary.verify-btn:hover { color: var(--color-primary); border-color: var(--color-primary); transform: translateY(-2px); }

/* Resend success */
.resend-success {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #10B981;
  font-size: 0.85rem;
  font-weight: 700;
  margin-top: 4px;
  animation: fade-in-up 0.4s ease;
}
@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(6px); }
  to   { opacity: 1; transform: translateY(0); }
}

@media (max-width: 480px) {
  .verify-card { padding: 36px 22px; }
  .state-title { font-size: 1.4rem; }
  .unlocked-grid { grid-template-columns: 1fr; }
  .action-row { flex-direction: column; }
  .verify-btn { justify-content: center; }
}
</style>

