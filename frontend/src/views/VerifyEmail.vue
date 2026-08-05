<template>
  <div class="verify-email-page container" style="margin-top: 50px; margin-bottom: 60px;">
    <div class="glass text-center" style="padding: 50px 30px; border-radius: 24px; max-width: 540px; margin: 0 auto; background: var(--color-bg-card);">
      
      <!-- Verification in progress -->
      <div v-if="loading" style="padding: 20px 0;">
        <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(212,175,55,0.15); border: 2px solid var(--color-accent); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;" class="pulse">
          <Loader2 class="spin" :size="36" style="color: var(--color-accent);" />
        </div>
        <h2 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 8px;">Vérification de votre adresse email...</h2>
        <p style="color: var(--color-text-light); font-size: 0.9rem;">Veuillez patienter pendant la validation de votre lien d'activation.</p>
      </div>

      <!-- Success state -->
      <div v-else-if="success" class="fade-in">
        <div style="width: 74px; height: 74px; border-radius: 50%; background: rgba(16,185,129,0.15); border: 2px solid #10b981; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
          <CheckCircle2 :size="42" style="color: #10b981;" />
        </div>
        <h1 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 8px;">Email Vérifié avec Succès ! 🎉</h1>
        <p style="color: var(--color-text-light); font-size: 0.95rem; margin-bottom: 24px;">
          Votre compte est désormais totalement actif. Vous pouvez profiter de l'ensemble des fonctionnalités de Yass Digital Lab.
        </p>
        <router-link to="/dashboard" class="btn btn-primary">Accéder à mon Espace Client</router-link>
      </div>

      <!-- Error state -->
      <div v-else class="fade-in">
        <div style="width: 74px; height: 74px; border-radius: 50%; background: rgba(239,68,68,0.15); border: 2px solid #ef4444; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
          <AlertCircle :size="42" style="color: #ef4444;" />
        </div>
        <h2 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 8px;">Lien de Vérification Invalide</h2>
        <p style="color: var(--color-text-light); font-size: 0.9rem; margin-bottom: 24px;">{{ errorMessage }}</p>
        <router-link to="/dashboard" class="btn btn-secondary">Retour à l'Espace Client</router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../api';
import { CheckCircle2, AlertCircle, Loader2 } from 'lucide-vue-next';

const route = useRoute();
const loading = ref(true);
const success = ref(false);
const errorMessage = ref('Le lien a expiré ou est invalide.');

onMounted(async () => {
  const signedUrl = route.query.url;
  if (!signedUrl) {
    loading.value = false;
    return;
  }

  try {
    const res = await fetch(decodeURIComponent(signedUrl), {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();

    if (res.ok) {
      success.value = true;
    } else {
      errorMessage.value = data.message || 'Échec de la vérification de l\'email.';
    }
  } catch (e) {
    errorMessage.value = 'Erreur réseau lors de la vérification.';
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.spin {
  animation: spin 1s linear infinite;
}
@keyframes spin {
  100% { transform: rotate(360deg); }
}
</style>
