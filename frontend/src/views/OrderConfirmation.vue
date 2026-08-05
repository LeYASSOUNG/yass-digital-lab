<template>
  <div class="order-confirmation-page container" style="margin-top: 40px; margin-bottom: 60px;">
    <div class="glass text-center" style="padding: 50px 30px; border-radius: 24px; max-width: 680px; margin: 0 auto; background: var(--color-bg-card);">
      
      <!-- Polling / Pending State -->
      <div v-if="loading || (order && order.status === 'pending')" style="padding: 30px 0;">
        <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(212,175,55,0.15); border: 2px solid var(--color-accent); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;" class="pulse">
          <Clock :size="36" style="color: var(--color-accent);" />
        </div>
        <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 8px;">Vérification de votre paiement...</h2>
        <p style="color: var(--color-text-light); font-size: 1rem; max-width: 480px; margin: 0 auto 20px;">
          Nous confirmons votre transaction auprès de Stripe. Cette étape prend généralement quelques secondes.
        </p>
        <div style="display: flex; justify-content: center; align-items: center; gap: 8px; color: var(--color-accent); font-weight: 700; font-size: 0.9rem;">
          <Loader2 class="spin" :size="18" /> Validation en cours...
        </div>
      </div>

      <!-- Success State -->
      <div v-else-if="order && order.status === 'paid'" class="fade-in">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(16,185,129,0.15); border: 2px solid #10b981; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
          <CheckCircle2 :size="46" style="color: #10b981;" />
        </div>
        <h1 style="font-size: 2.1rem; font-weight: 800; margin-bottom: 8px; color: var(--color-text);">Paiement Confirmé ! 🎉</h1>
        <p style="color: var(--color-text-light); font-size: 1rem; margin-bottom: 24px;">
          Merci pour votre achat ! Votre commande <strong style="color: var(--color-accent);">#FA-{{ String(order.id).padStart(6, '0') }}</strong> est validée.
        </p>

        <!-- Order details summary -->
        <div style="background: rgba(0,0,0,0.04); border: 1px solid var(--color-border); border-radius: 16px; padding: 24px; text-align: left; margin-bottom: 28px;">
          <div class="flex justify-between items-center mb-3" style="border-bottom: 1px solid var(--color-border); padding-bottom: 10px;">
            <span style="color: var(--color-text-light); font-size: 0.88rem;">Email destinataire :</span>
            <strong style="color: var(--color-text);">{{ order.email }}</strong>
          </div>
          <div class="flex justify-between items-center mb-3" style="border-bottom: 1px solid var(--color-border); padding-bottom: 10px;">
            <span style="color: var(--color-text-light); font-size: 0.88rem;">Montant réglé :</span>
            <strong style="color: var(--color-accent); font-size: 1.2rem;">{{ order.total_amount }} €</strong>
          </div>

          <h4 style="font-size: 0.95rem; font-weight: 700; margin: 16px 0 10px;">Articles numériques acquis :</h4>
          <div v-for="item in order.items" :key="item.id" class="flex justify-between items-center mb-2" style="font-size: 0.88rem;">
            <span>• {{ item.product_title }} (x{{ item.quantity }})</span>
            <strong>{{ (item.price * item.quantity).toFixed(2) }} €</strong>
          </div>
        </div>

        <div class="flex gap-3 justify-center" style="flex-wrap: wrap;">
          <router-link to="/dashboard" class="btn btn-primary flex items-center gap-2" style="padding: 12px 24px;">
            <Download :size="16" /> Accéder à mes fichiers & licences
          </router-link>
          <a :href="`/api/orders/${order.id}/invoice`" target="_blank" class="btn btn-secondary flex items-center gap-2" style="padding: 12px 20px;">
            <FileText :size="16" /> Télécharger la Facture PDF
          </a>
        </div>
      </div>

      <!-- Error / Not found state -->
      <div v-else class="fade-in">
        <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(239,68,68,0.15); border: 2px solid #ef4444; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
          <AlertCircle :size="36" style="color: #ef4444;" />
        </div>
        <h2 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 8px;">Commande introuvable</h2>
        <p style="color: var(--color-text-light); margin-bottom: 24px;">Impossible de retrouver la session de paiement. Si votre paiement a été débité, vos accès restent valides sur votre espace client.</p>
        <router-link to="/dashboard" class="btn btn-primary">Aller à mon Espace Client</router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../api';
import { useCartStore } from '../stores/cart';
import { useNotificationStore } from '../stores/notification';
import { CheckCircle2, Clock, AlertCircle, Loader2, Download, FileText } from 'lucide-vue-next';

const route = useRoute();
const cartStore = useCartStore();
const notifStore = useNotificationStore();

const order = ref(null);
const loading = ref(true);
let pollInterval = null;

const fetchOrder = async () => {
  const sessionId = route.query.session_id;
  if (!sessionId) {
    loading.value = false;
    return;
  }

  try {
    const res = await api.get(`/orders/by-session/${sessionId}`);
    order.value = res.data;
    
    if (order.value.status === 'paid') {
      cartStore.clearCart();
      if (pollInterval) clearInterval(pollInterval);
      // Déclencher une notification système pour la commande validée
      notifStore.addNotification({
        type: 'order',
        title: `Commande #FA-${String(order.value.id).padStart(6, '0')} Validée ✅`,
        desc: `Votre paiement de ${order.value.total_amount} € a été accepté. Vos fichiers sont disponibles.`,
        link: '/dashboard'
      });
    }
  } catch (e) {
    console.error('Erreur récupération commande session:', e);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchOrder();
  // Polling toutes les 2 secondes tant que le webhook Stripe n'a pas validé
  pollInterval = setInterval(() => {
    if (!order.value || order.value.status === 'pending') {
      fetchOrder();
    }
  }, 2000);
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
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
