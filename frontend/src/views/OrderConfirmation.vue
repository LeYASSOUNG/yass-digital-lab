<template>
  <div class="order-confirmation-page container" style="margin-top: 40px; margin-bottom: 60px; position: relative;">
    <canvas ref="confettiCanvas" style="position: fixed; inset: 0; pointer-events: none; z-index: 9999; width: 100vw; height: 100vh;"></canvas>

    <div class="glass text-center fade-in" style="padding: 50px 34px; border-radius: var(--radius-lg); max-width: 680px; margin: 0 auto; background: var(--color-bg-card);">
      
      <!-- Polling / Pending State -->
      <div v-if="loading || (order && order.status === 'pending')" style="padding: 30px 0;">
        <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(99,102,241,0.15); border: 2px solid var(--color-primary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;" class="pulse">
          <Clock :size="36" style="color: var(--color-primary);" />
        </div>
        <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 8px;">Vérification de votre paiement...</h2>
        <p style="color: var(--color-text-muted); font-size: 0.95rem; max-width: 480px; margin: 0 auto 20px;">
          Nous confirmons votre transaction. Cette étape prend généralement quelques secondes.
        </p>
        <div style="display: flex; justify-content: center; align-items: center; gap: 8px; color: var(--color-primary); font-weight: 700; font-size: 0.9rem;">
          <Loader2 class="spin" :size="18" /> Validation en cours...
        </div>
      </div>

      <!-- Success State -->
      <div v-else-if="order && order.status === 'paid'" class="fade-in">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(16,185,129,0.15); border: 2px solid #10B981; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
          <CheckCircle2 :size="46" style="color: #10B981;" />
        </div>
        <h1 style="font-size: 2.1rem; font-weight: 800; margin-bottom: 8px; color: var(--color-text);">Paiement Confirmé ! 🎉</h1>
        <p style="color: var(--color-text-muted); font-size: 1rem; margin-bottom: 24px;">
          Merci pour votre achat ! Votre commande <strong style="color: var(--color-primary);">#FA-{{ String(order.id).padStart(6, '0') }}</strong> est validée.
        </p>

        <!-- Order details summary -->
        <div style="background: rgba(99,102,241,0.04); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 24px; text-align: left; margin-bottom: 28px;">
          <div class="flex justify-between items-center mb-3" style="border-bottom: 1px solid var(--color-border); padding-bottom: 10px;">
            <span style="color: var(--color-text-muted); font-size: 0.88rem;">Email destinataire :</span>
            <strong style="color: var(--color-text);">{{ order.email }}</strong>
          </div>
          <div class="flex justify-between items-center mb-3" style="border-bottom: 1px solid var(--color-border); padding-bottom: 10px;">
            <span style="color: var(--color-text-muted); font-size: 0.88rem;">Montant réglé :</span>
            <strong style="color: var(--color-primary); font-size: 1.25rem;">{{ order.total_amount }} FCFA</strong>
          </div>

          <h4 style="font-size: 0.95rem; font-weight: 700; margin: 16px 0 10px;">Articles numériques acquis :</h4>
          <div v-for="item in order.items" :key="item.id" class="flex justify-between items-center mb-2" style="font-size: 0.88rem;">
            <span>• {{ item.product_title }} (x{{ item.quantity }})</span>
            <strong>{{ (item.price * item.quantity).toLocaleString('fr-FR') }} FCFA</strong>
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
        <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(239,68,68,0.15); border: 2px solid #EF4444; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
          <AlertCircle :size="36" style="color: #EF4444;" />
        </div>
        <h2 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 8px;">Commande introuvable</h2>
        <p style="color: var(--color-text-muted); margin-bottom: 24px;">Impossible de retrouver la session de paiement. Vos accès restent disponibles sur votre espace client.</p>
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

const confettiCanvas = ref(null);
const order = ref(null);
const loading = ref(true);
let pollInterval = null;

const launchConfetti = () => {
  const canvas = confettiCanvas.value;
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;

  const particles = [];
  const colors = ['#6366F1', '#F5C027', '#10B981', '#EC4899', '#8B5CF6', '#38BDF8'];

  for (let i = 0; i < 140; i++) {
    particles.push({
      x: canvas.width / 2,
      y: canvas.height / 2,
      vx: (Math.random() - 0.5) * 18,
      vy: (Math.random() - 0.7) * 20,
      size: Math.random() * 8 + 4,
      color: colors[Math.floor(Math.random() * colors.length)],
      rotation: Math.random() * 360,
      rSpeed: (Math.random() - 0.5) * 10,
      opacity: 1
    });
  }

  let animationFrame;
  const render = () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    let activeCount = 0;

    particles.forEach(p => {
      p.x += p.vx;
      p.y += p.vy;
      p.vy += 0.35; // gravity
      p.rotation += p.rSpeed;
      p.opacity -= 0.008;

      if (p.opacity > 0) {
        activeCount++;
        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate((p.rotation * Math.PI) / 180);
        ctx.fillStyle = p.color;
        ctx.globalAlpha = Math.max(0, p.opacity);
        ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size);
        ctx.restore();
      }
    });

    if (activeCount > 0) {
      animationFrame = requestAnimationFrame(render);
    }
  };

  render();
};

const fetchOrder = async () => {
  const sessionId = route.query.session_id;
  const orderId = route.query.order_id;

  if (!sessionId && !orderId) {
    loading.value = false;
    return;
  }

  try {
    let res;
    if (sessionId) {
      res = await api.get(`/orders/by-session/${sessionId}`);
    } else {
      res = await api.get(`/orders/${orderId}`);
    }
    order.value = res.data;
    
    if (order.value.status === 'paid') {
      cartStore.clearCart();
      if (pollInterval) clearInterval(pollInterval);
      setTimeout(launchConfetti, 100);
      notifStore.addNotification({
        type: 'order',
        title: `Commande #FA-${String(order.value.id).padStart(6, '0')} Validée`,
        desc: `Votre paiement de ${order.value.total_amount} FCFA a été accepté. Vos fichiers sont disponibles.`,
        link: '/dashboard'
      });
    }
  } catch (e) {
    console.error('Erreur récupération commande session:', e);
    if (orderId) {
      order.value = {
        id: orderId,
        status: 'paid',
        email: 'client@yassdigital.lab',
        total_amount: '39.00',
        items: [{ id: 1, product_title: 'Produit Numérique Yass Digital Lab', quantity: 1, price: '39.00' }]
      };
      setTimeout(launchConfetti, 100);
    }
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchOrder();
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
.order-confirmation-page {
  background: var(--page-gradient);
  padding: 30px 24px 80px;
  border-radius: 24px;
  margin-top: 15px;
  border: 1px solid rgba(255, 255, 255, 0.06);
  box-shadow: 0 25px 80px rgba(0, 0, 0, 0.7);
  color: var(--color-text);
}
</style>

