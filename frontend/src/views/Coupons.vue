<template>
  <div class="coupons-page container" style="margin-top: 40px; margin-bottom: 80px;">
    
    <!-- Hero Section -->
    <div class="text-center mb-10 fade-in">
      <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: rgba(212,175,55,0.15); border: 1px solid rgba(212,175,55,0.3); border-radius: 16px; margin-bottom: 20px; box-shadow: 0 8px 32px rgba(212,175,55,0.15);">
        <Ticket :size="32" style="color: var(--color-accent);" />
      </div>
      <h1 style="font-size: 2.8rem; font-weight: 800; margin-bottom: 16px; font-family: var(--font-heading); text-transform: uppercase; letter-spacing: -0.02em;">
        Codes <span class="text-gradient-gold">Promos</span> & Offres
      </h1>
      <p style="color: var(--color-text-light); font-size: 1.1rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
        Profitez de nos réductions exclusives sur les templates et packs d'assets. <br/>
        Copiez le code et appliquez-le lors du paiement !
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
      <div v-for="i in 3" :key="i" style="height: 180px; border-radius: 20px; animation: pulse 1.5s infinite; background: var(--color-bg-card); border: 1px solid var(--color-border);"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="coupons.length === 0" class="glass text-center" style="padding: 60px 20px; border-radius: 24px; border: 1px solid var(--color-border);">
      <Tag :size="48" style="color: var(--color-text-muted); margin: 0 auto 16px;" />
      <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 10px;">Aucune offre active pour le moment</h3>
      <p style="color: var(--color-text-light); margin-bottom: 24px;">Abonnez-vous à notre newsletter pour être alerté des prochaines réductions exclusives !</p>
      <router-link to="/products" class="btn btn-primary" style="padding: 12px 24px; font-size: 1rem;">Voir le catalogue</router-link>
    </div>

    <!-- Coupons Grid -->
    <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
      <div v-for="coupon in coupons" :key="coupon.id" class="glass coupon-card slide-up" style="position: relative; border-radius: 24px; overflow: hidden; padding: 2px;">
        
        <!-- Glowing Border Effect -->
        <div style="position: absolute; inset: 0; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary), transparent); opacity: 0.15; z-index: 0;"></div>
        
        <!-- Card Content -->
        <div style="position: relative; z-index: 1; background: var(--color-bg-card); border-radius: 22px; padding: 24px; height: 100%; display: flex; flex-direction: column;">
          
          <div class="flex justify-between items-start mb-4">
            <span class="badge-pill" style="background: rgba(212,175,55,0.15); color: var(--color-accent); font-weight: 800; font-size: 0.8rem;">
              <Zap :size="12" style="display: inline; margin-right: 2px; transform: translateY(-1px);" /> OFFRE SPÉCIALE
            </span>
            <div v-if="coupon.expires_at" style="font-size: 0.75rem; color: #ef4444; background: rgba(239,68,68,0.1); padding: 4px 10px; border-radius: 999px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
              <Clock :size="12" /> Expire le {{ new Date(coupon.expires_at).toLocaleDateString('fr-FR') }}
            </div>
          </div>

          <h3 style="font-size: 2.2rem; font-weight: 900; margin: 0 0 4px; font-family: var(--font-heading);">
            {{ coupon.discount_percentage ? `-${coupon.discount_percentage}%` : `-${coupon.discount_amount}FCFA` }}
          </h3>
          <p style="color: var(--color-text-light); font-size: 0.95rem; margin-bottom: 24px; line-height: 1.5;">
            Réduction valable sur toutes vos commandes avec ce code.
          </p>

          <!-- Code Box -->
          <div style="margin-top: auto;">
            <div style="background: rgba(0,0,0,0.2); border: 2px dashed rgba(212,175,55,0.4); border-radius: 12px; padding: 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; transition: all 0.3s ease;" class="code-box">
              <span style="font-family: monospace; font-size: 1.4rem; font-weight: 800; letter-spacing: 0.1em; color: var(--color-text);">
                {{ coupon.code }}
              </span>
              <button @click="copyCode(coupon.code)" class="btn btn-secondary" style="padding: 8px; border-radius: 8px; background: rgba(212,175,55,0.15); border: 1px solid rgba(212,175,55,0.3); color: var(--color-accent);" title="Copier le code">
                <Copy :size="18" />
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useToastStore } from '../stores/toast';
import api from '../api';
import { Ticket, Tag, Clock, Zap, Copy } from 'lucide-vue-next';

const coupons = ref([]);
const loading = ref(true);
const toastStore = useToastStore();

const loadCoupons = async () => {
  loading.value = true;
  try {
    const res = await api.get('/coupons/active');
    coupons.value = res.data;
  } catch (error) {
    console.error('Error loading coupons:', error);
    toastStore.showToast('Erreur lors du chargement des offres.', 'error');
  } finally {
    loading.value = false;
  }
};

const copyCode = async (code) => {
  try {
    await navigator.clipboard.writeText(code);
    toastStore.showToast(`Code promo ${code} copié ! 🎉`, 'success');
  } catch (err) {
    toastStore.showToast('Erreur lors de la copie', 'error');
  }
};

onMounted(() => {
  loadCoupons();
});
</script>

<style scoped>
.coupons-page {
  background: var(--page-gradient);
  padding: 30px 24px 80px;
  border-radius: 24px;
  margin-top: 15px;
  border: 1px solid rgba(255, 255, 255, 0.06);
  box-shadow: 0 25px 80px rgba(0, 0, 0, 0.7);
  color: var(--color-text);
}

.coupon-card {
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
}
.coupon-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}
.coupon-card:hover .code-box {
  border-color: var(--color-accent) !important;
  background: rgba(212,175,55,0.05) !important;
}

@keyframes pulse {
  0% { opacity: 0.6; }
  50% { opacity: 1; }
  100% { opacity: 0.6; }
}
</style>

