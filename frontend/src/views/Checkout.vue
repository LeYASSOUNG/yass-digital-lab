<template>
  <div class="checkout-page container" style="margin-top: 40px; margin-bottom: 60px;">
    <div class="glass" style="padding: 40px 32px; border-radius: 24px; background: var(--color-bg-card);">
      <h1 class="mb-6 text-center" style="font-size: 2.1rem; font-weight: 800; font-family: var(--font-heading);">
        Finaliser votre Commande
      </h1>
      
      <div v-if="cart.items.length === 0" class="text-center" style="padding: 60px;">
        <div style="font-size: 3rem; margin-bottom: 12px;">🛍️</div>
        <p style="color: var(--color-text-light); margin-bottom: 20px; font-size: 1.1rem;">Votre panier est actuellement vide.</p>
        <router-link to="/products" class="btn btn-primary">Découvrir le catalogue →</router-link>
      </div>

      <div v-else class="grid" style="grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;">
        
        <!-- Résumé de la commande -->
        <div class="glass p-6" style="border-radius: 20px; border: 1px solid var(--color-border);">
          <h2 class="mb-4" style="font-size: 1.25rem; font-weight: 700;">Résumé du Panier</h2>
          
          <div v-for="item in cart.items" :key="item.id" class="flex justify-between items-center mb-4" style="border-bottom: 1px solid var(--color-border); padding-bottom: 12px;">
            <div>
              <p style="font-weight: 700; color: var(--color-text); margin: 0 0 2px;">{{ item.title }}</p>
              <p style="font-size: 0.82rem; color: var(--color-text-light); margin: 0;">Quantité : {{ item.quantity }}</p>
            </div>
            <div class="flex items-center gap-3">
              <span style="font-weight: 800; color: var(--color-accent); font-size: 1rem;">{{ (item.price * item.quantity).toFixed(2) }} €</span>
              <button @click="cart.removeItem(item.id)" style="background: rgba(239,68,68,0.1); border: 1px solid #ef4444; color: #ef4444; border-radius: 6px; padding: 4px 8px; cursor: pointer; font-size: 0.75rem; font-weight: 700;" title="Retirer de la commande">
                ✕
              </button>
            </div>
          </div>

          <!-- Code Promo -->
          <div class="mb-6 p-4" style="border-radius: 14px; background: rgba(212,175,55,0.06); border: 1px solid rgba(212,175,55,0.22);">
            <label style="display: block; margin-bottom: 6px; font-weight: 700; font-size: 0.88rem;">🎟️ Code Promo / Coupon</label>
            <div class="flex gap-2">
              <input v-model="couponCode" type="text" placeholder="ex: YASS20" style="flex: 1; padding: 9px 12px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); text-transform: uppercase; font-size: 0.88rem;" />
              <button @click="applyCoupon" type="button" class="btn btn-secondary" style="padding: 8px 14px; font-size: 0.85rem; font-weight: 700;" :disabled="applyingCoupon">
                {{ applyingCoupon ? '...' : 'Appliquer' }}
              </button>
            </div>
            <p v-if="couponMessage" :style="discount > 0 ? 'color: #22c55e;' : 'color: #ef4444;'" style="font-size: 0.82rem; margin-top: 6px; font-weight: 600;">{{ couponMessage }}</p>
          </div>

          <div class="flex justify-between items-center mt-4">
            <span style="color: var(--color-text-light); font-size: 0.9rem;">Sous-total :</span>
            <strong style="color: var(--color-text); font-size: 1rem;">{{ cart.totalPrice.toFixed(2) }} €</strong>
          </div>

          <div v-if="discount > 0" class="flex justify-between items-center mt-2" style="color: #22c55e; font-size: 0.9rem;">
            <span>Réduction appliquée :</span>
            <strong>-{{ discount.toFixed(2) }} €</strong>
          </div>

          <div class="flex justify-between items-center mt-4" style="border-top: 2px solid var(--color-border); padding-top: 14px;">
            <span style="color: var(--color-text); font-weight: 800; font-size: 1.1rem;">Total à Réglier :</span>
            <h2 style="color: var(--color-accent); font-size: 2.1rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">{{ finalTotal.toFixed(2) }} €</h2>
          </div>
        </div>

        <!-- Formulaire et Paiement Sécurisé -->
        <div class="glass p-6" style="border-radius: 20px; border: 1px solid var(--color-border); background: var(--color-bg-card);">
          <h2 class="mb-4" style="font-size: 1.25rem; font-weight: 700;">Coordonnées & Paiement</h2>
          
          <form @submit.prevent="processPayment">
            <div class="mb-4">
              <label style="display: block; margin-bottom: 6px; font-weight: 700; font-size: 0.88rem;">Adresse Email (Réception de vos accès) *</label>
              <input v-model="email" type="email" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" placeholder="votre@email.com" />
            </div>
            
            <div class="mb-4">
              <label style="display: block; margin-bottom: 6px; font-weight: 700; font-size: 0.88rem;">Méthode de Règlement</label>
              <select v-model="paymentMethod" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem; cursor: pointer;">
                <option value="stripe">💳 Carte Bancaire (Stripe 100% Sécurisé)</option>
                <option value="orange">🍊 Orange Money (En cours de déploiement)</option>
                <option value="mtn">🟡 MTN Mobile Money (En cours de déploiement)</option>
                <option value="wave">🌊 Wave (En cours de déploiement)</option>
              </select>
            </div>

            <!-- Notice Mobile Money non intégrée -->
            <div v-if="paymentMethod !== 'stripe'" class="mb-4 p-4" style="background: rgba(234,179,8,0.12); border: 1px solid rgba(234,179,8,0.3); border-radius: 12px; color: #eab308; font-size: 0.82rem; line-height: 1.5;">
              ℹ️ <strong>Information Mobile Money :</strong> L'intégration marchande directe avec {{ paymentMethod.toUpperCase() }} est en cours de finalisation par nos développeurs. Pour débloquer votre commande instantanément, veuillez choisir le paiement par **Carte Bancaire (Stripe)**.
            </div>

            <!-- Trust Badge -->
            <div class="mb-4 flex items-center justify-center gap-2" style="font-size: 0.78rem; color: #10b981; font-weight: 700;">
              <span>🔒 Cryptage SSL 256-bit</span> • <span>Accès Immédiat 24/7</span>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px; font-size: 1rem; font-weight: 800;" :disabled="loading || paymentMethod !== 'stripe'">
              {{ loading ? 'Initialisation...' : `Payer ${finalTotal.toFixed(2)} € via Stripe 🔒` }}
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '../stores/cart';
import { useRouter } from 'vue-router';
import { ref, computed } from 'vue';
import api from '../api';
import { useToastStore } from '../stores/toast';

const cart = useCartStore();
const router = useRouter();
const toastStore = useToastStore();

const paymentMethod = ref('stripe');
const email = ref('');
const loading = ref(false);

const couponCode = ref('');
const discount = ref(0);
const couponMessage = ref('');
const applyingCoupon = ref(false);

const finalTotal = computed(() => {
  return Math.max(0, cart.totalPrice - discount.value);
});

const applyCoupon = async () => {
  if (!couponCode.value.trim()) return;
  applyingCoupon.value = true;
  couponMessage.value = '';
  try {
    const res = await api.post('/coupons/validate', { code: couponCode.value.trim() });
    if (res.data.discount_amount) {
      discount.value = parseFloat(res.data.discount_amount);
    } else if (res.data.discount_percentage) {
      discount.value = (cart.totalPrice * parseFloat(res.data.discount_percentage)) / 100;
    }
    couponMessage.value = res.data.message || 'Coupon appliqué avec succès !';
    toastStore.showToast('Coupon appliqué !', 'success');
  } catch (e) {
    discount.value = 0;
    couponMessage.value = e.response?.data?.message || 'Code promo invalide.';
    toastStore.showToast(couponMessage.value, 'error');
  } finally {
    applyingCoupon.value = false;
  }
};

const processPayment = async () => {
  if (!email.value) {
    toastStore.showToast('Veuillez renseigner votre adresse email.', 'error');
    return;
  }

  if (paymentMethod.value !== 'stripe') {
    toastStore.showToast('Veuillez sélectionner le paiement par Carte Bancaire (Stripe).', 'info');
    return;
  }

  loading.value = true;

  try {
    const response = await api.post('/create-checkout-session', {
      items: cart.items,
      email: email.value
    });

    if (response.data.url) {
      window.location.href = response.data.url;
    } else {
      toastStore.showToast('Erreur lors de la redirection vers Stripe.', 'error');
    }
  } catch (error) {
    console.error("Erreur de paiement", error);
    toastStore.showToast(error.response?.data?.error || 'Une erreur est survenue lors du paiement.', 'error');
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
@media (max-width: 768px) {
  .grid {
    grid-template-columns: 1fr !important;
  }
}
</style>
