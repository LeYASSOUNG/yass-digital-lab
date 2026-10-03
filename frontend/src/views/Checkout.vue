<template>
  <div class="checkout-page container" style="margin-top: 30px; margin-bottom: 60px;">
    
    <!-- Progression Steps -->
    <div class="steps max-w-xl mx-auto mb-8">
      <div class="step completed">
        <div class="step-circle"><CheckCircle2 :size="18" /></div>
        <span class="step-label">Panier</span>
      </div>
      <div class="step active">
        <div class="step-circle">2</div>
        <span class="step-label">Paiement & Coordonnées</span>
      </div>
      <div class="step">
        <div class="step-circle">3</div>
        <span class="step-label">Confirmation & Accès</span>
      </div>
    </div>

    <div class="fade-in" style="padding: 42px 34px; border-radius: var(--radius-lg); background: transparent;">
      
      <!-- Header -->
      <div class="text-center mb-8">
        <span class="badge-pill badge-indigo mb-3">COMMANDER EN TOUTE SÉCURITÉ</span>
        <h1 class="mb-2" style="font-size: 2.1rem; font-weight: 800;">
          Finaliser votre Commande
        </h1>
        <p style="color: var(--color-text-muted); font-size: 0.95rem;">
          Accès immédiat à vos fichiers numériques après paiement sécurisé (Stripe ou Mobile Money).
        </p>
      </div>
      
      <!-- Panier vide -->
      <div v-if="cart.items.length === 0" class="text-center" style="padding: 60px;">
        <div style="display: inline-flex; width: 64px; height: 64px; border-radius: 50%; background: rgba(124,58,237,0.12); align-items: center; justify-content: center; color: var(--color-primary); margin-bottom: 16px;">
          <ShoppingBag :size="32" />
        </div>
        <h3 class="mb-2">Votre panier est actuellement vide</h3>
        <p style="color: var(--color-text-muted); margin-bottom: 24px; font-size: 1rem;">Explorez notre catalogue pour découvrir nos templates, packs et services.</p>
        <router-link to="/products" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
          <span>Découvrir le catalogue</span> <ArrowRight :size="16" />
        </router-link>
      </div>

      <!-- Résumé + Formulaire de paiement -->
      <div v-else class="grid checkout-grid" style="grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;">
        
        <!-- Résumé de la commande -->
        <div class="p-6" style="border-radius: var(--radius-lg); border: 1px solid var(--color-border); background: var(--color-bg-card); box-shadow: var(--shadow-sm);">
          <h2 class="mb-4 flex items-center gap-2" style="font-size: 1.25rem; font-weight: 700;">
            <ShoppingBag :size="20" style="color: var(--color-primary);" /> Résumé du Panier
          </h2>
          
          <div v-for="item in cart.items" :key="item.id" class="flex justify-between items-center mb-4" style="border-bottom: 1px solid var(--color-border); padding-bottom: 12px;">
            <div>
              <p style="font-weight: 700; color: var(--color-text); margin: 0 0 2px;">{{ item.title }}</p>
              <p style="font-size: 0.82rem; color: var(--color-text-muted); margin: 0;">Quantité : {{ item.quantity }}</p>
            </div>
            <div class="flex items-center gap-3">
              <span style="font-weight: 800; color: var(--color-primary); font-size: 1rem;">{{ currencyStore.formatDual(item.price * item.quantity) }}</span>
              <button @click="cart.removeItem(item.id)" style="background: rgba(239,68,68,0.1); border: 1px solid #EF4444; color: #EF4444; border-radius: 6px; padding: 4px 8px; cursor: pointer; font-size: 0.75rem; font-weight: 700;" title="Retirer de la commande">
                <Trash2 :size="14" />
              </button>
            </div>
          </div>

          <!-- Code Promo -->
          <div class="mb-6 p-4" style="border-radius: var(--radius-md); background: var(--color-bg-elevated); border: 1px solid var(--color-border);">
            <label style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 700; font-size: 0.88rem;">
              <Ticket :size="16" style="color: var(--color-primary);" /> Code Promo / Coupon
            </label>
            <div class="flex gap-2">
              <input v-model="couponCode" type="text" placeholder="ex: YASS20" style="flex: 1; padding: 9px 12px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); text-transform: uppercase; font-size: 0.88rem;" />
              <button @click="applyCoupon" type="button" class="btn btn-secondary" style="padding: 8px 14px; font-size: 0.85rem; font-weight: 700;" :disabled="applyingCoupon">
                {{ applyingCoupon ? '...' : 'Appliquer' }}
              </button>
            </div>
            <p v-if="couponMessage" :style="discount > 0 ? 'color: #10B981;' : 'color: #EF4444;'" style="font-size: 0.82rem; margin-top: 6px; font-weight: 600;">{{ couponMessage }}</p>
          </div>

          <div class="flex justify-between items-center mt-4">
            <span style="color: var(--color-text-muted); font-size: 0.9rem;">Sous-total :</span>
            <strong style="color: var(--color-text); font-size: 1rem;">{{ currencyStore.format(cart.totalPrice) }}</strong>
          </div>

          <div v-if="discount > 0" class="flex justify-between items-center mt-2" style="color: #10B981; font-size: 0.9rem;">
            <span>Réduction appliquée :</span>
            <strong>-{{ currencyStore.format(discount) }}</strong>
          </div>

          <div class="flex justify-between items-center mt-4" style="border-top: 2px solid var(--color-border); padding-top: 14px;">
            <span style="color: var(--color-text); font-weight: 800; font-size: 1.1rem;">Total à Réglier :</span>
            <h2 style="color: var(--color-primary); font-size: 1.8rem; font-weight: 800; margin: 0;">{{ currencyStore.formatDual(finalTotal) }}</h2>
          </div>
        </div>

        <!-- Formulaire et Paiement Sécurisé -->
        <div class="p-6" style="border-radius: var(--radius-lg); border: 1px solid var(--color-border); background: var(--color-bg-card); box-shadow: var(--shadow-sm);">
          <h2 class="mb-4 flex items-center gap-2" style="font-size: 1.25rem; font-weight: 700;">
            <CreditCard :size="20" style="color: var(--color-primary);" /> Coordonnées & Mode de Paiement
          </h2>
          
          <form @submit.prevent="processPayment">
            <div class="mb-4">
              <label style="display: block; margin-bottom: 6px; font-weight: 700; font-size: 0.88rem;">Adresse Email (Réception de vos accès) *</label>
              <input v-model="email" type="email" required style="width: 100%; padding: 11px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" placeholder="votre@email.com" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
              <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 700; font-size: 0.88rem;">Nom Complet *</label>
                <input v-model="fullName" type="text" required style="width: 100%; padding: 11px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" placeholder="Jean Dupont" />
              </div>
              <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 700; font-size: 0.88rem;">Numéro de Téléphone *</label>
                <input v-model="phone" type="text" required style="width: 100%; padding: 11px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" placeholder="+225 0102030405" />
              </div>
            </div>

            <!-- Choix du mode de paiement -->
            <div class="mb-4">
              <label style="display: block; margin-bottom: 6px; font-weight: 700; font-size: 0.88rem;">Méthode de Règlement</label>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <button 
                  type="button" 
                  @click="paymentMethod = 'stripe'"
                  :class="paymentMethod === 'stripe' ? 'btn-primary' : 'btn-secondary'"
                  style="padding: 12px; font-size: 0.9rem; justify-content: center; gap: 8px;"
                >
                  <CreditCard :size="18" /> Carte Bancaire (Stripe)
                </button>
                <button 
                  type="button" 
                  @click="paymentMethod = 'cinetpay'"
                  :class="paymentMethod === 'cinetpay' ? 'btn-primary' : 'btn-secondary'"
                  style="padding: 12px; font-size: 0.9rem; justify-content: center; gap: 8px;"
                >
                  <Smartphone :size="18" style="color: #FFCC00;" /> Mobile Money (CinetPay)
                </button>
                <button 
                  type="button" 
                  @click="paymentMethod = 'geniuspay'"
                  :class="paymentMethod === 'geniuspay' ? 'btn-primary' : 'btn-secondary'"
                  style="padding: 12px; font-size: 0.9rem; justify-content: center; gap: 8px; grid-column: 1 / -1;"
                >
                  <Zap :size="18" style="color: #7C3AED;" /> Mobile Money & Cartes (GeniusPay)
                </button>
              </div>
            </div>



            <!-- Trust Badge -->
            <div class="mb-5 flex items-center justify-center gap-3" style="font-size: 0.82rem; color: #10B981; font-weight: 700;">
              <span class="flex items-center gap-1"><Lock :size="14" /> Cryptage SSL 256-bit</span>
              <span>•</span>
              <span class="flex items-center gap-1"><ShieldCheck :size="14" /> Accès Immédiat 24/7</span>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 13px; font-size: 1rem; font-weight: 800;" :disabled="loading">
              <span v-if="loading">Validation de la transaction...</span>
              <span v-else-if="paymentMethod === 'stripe'">Payer {{ currencyStore.formatDual(finalTotal) }} via Stripe 🔒</span>
              <span v-else>Payer {{ currencyStore.format(finalTotal) }} avec {{ paymentMethod.toUpperCase() }} 📱</span>
            </button>
          </form>
        </div>

      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';
import { useToastStore } from '../stores/toast';
import { useCurrencyStore } from '../stores/currency';
import api from '../api';
import { 
  ShoppingBag, 
  ArrowRight, 
  Trash2, 
  Ticket, 
  CreditCard, 
  Smartphone, 
  Phone, 
  Lock, 
  ShieldCheck, 
  CheckCircle2,
  Zap
} from 'lucide-vue-next';

const router = useRouter();
const cart = useCartStore();
const auth = useAuthStore();
const toastStore = useToastStore();
const currencyStore = useCurrencyStore();

const email = ref('');
const fullName = ref('');
const phone = ref('');
const paymentMethod = ref('stripe');
const momoPhone = ref('');
const loading = ref(false);

const couponCode = ref('');
const discount = ref(0);
const couponMessage = ref('');
const applyingCoupon = ref(false);

onMounted(() => {
  if (auth.user?.email) {
    email.value = auth.user.email;
  }
  // Auto-apply referral code if stored
  const refCode = localStorage.getItem('referral_code');
  if (refCode) {
    couponCode.value = 'YASS20';
    applyCoupon();
  }
});

const finalTotal = computed(() => {
  return Math.max(0, cart.totalPrice - discount.value);
});

const applyCoupon = async () => {
  if (!couponCode.value.trim()) return;
  applyingCoupon.value = true;
  couponMessage.value = '';

  try {
    const res = await api.post('/coupons/validate', { code: couponCode.value, total: cart.totalPrice });
    discount.value = res.data.discount_amount || (cart.totalPrice * 0.2); // Fallback 20%
    couponMessage.value = `Code "${couponCode.value.toUpperCase()}" appliqué (-${discount.value.toFixed(2)} FCFA) !`;
    toastStore.showToast(`Code promo appliqué : -${discount.value.toFixed(2)} FCFA !`, 'success');
  } catch (e) {
    if (couponCode.value.toUpperCase() === 'YASS20') {
      discount.value = cart.totalPrice * 0.2;
      couponMessage.value = 'Code promo YASS20 valide (-20%) !';
      toastStore.showToast('Code YASS20 (-20%) appliqué !', 'success');
    } else {
      discount.value = 0;
      couponMessage.value = 'Code promo invalide ou expiré.';
      toastStore.showToast('Code promo invalide.', 'error');
    }
  } finally {
    applyingCoupon.value = false;
  }
};

const processPayment = async () => {
  if (!email.value) {
    toastStore.showToast('Veuillez saisir votre adresse email.', 'error');
    return;
  }

  loading.value = true;

  try {
    const affiliateCode = localStorage.getItem('referral_code') || localStorage.getItem('yass_affiliate_ref') || '';

    if (paymentMethod.value === 'stripe') {
      const res = await api.post('/create-checkout-session', {
        email: email.value,
        name: fullName.value,
        phone: phone.value,
        affiliate_code: affiliateCode,
        items: cart.items.map(i => ({ title: i.title, price: i.price, quantity: i.quantity }))
      });
      if (res.data.url) {
        window.location.href = res.data.url;
      }
    } else if (paymentMethod.value === 'cinetpay') {
      // Mobile Money via CinetPay
      const res = await api.post('/payment/cinetpay/initiate', {
        email: email.value,
        name: fullName.value,
        phone: phone.value,
        amount: finalTotal.value,
        affiliate_code: affiliateCode,
        items: cart.items.map(i => ({ title: i.title, price: i.price, quantity: i.quantity }))
      });
      
      if (res.data.payment_url) {
        cart.clearCart(); // On vide le panier avant de rediriger car CinetPay ramène sur order-confirmation
        window.location.href = res.data.payment_url;
      } else {
        throw new Error('URL de paiement non reçue');
      }
    } else if (paymentMethod.value === 'geniuspay') {
      // Paiement via GeniusPay
      const res = await api.post('/payment/geniuspay/initiate', {
        email: email.value,
        name: fullName.value,
        phone: phone.value,
        amount: finalTotal.value,
        affiliate_code: affiliateCode,
        items: cart.items.map(i => ({ title: i.title, price: i.price, quantity: i.quantity }))
      });
      
      if (res.data.payment_url) {
        cart.clearCart();
        window.location.href = res.data.payment_url;
      } else {
        throw new Error('URL de paiement non reçue');
      }
    }
  } catch (e) {
    console.error('Erreur checkout:', e);
    toastStore.showToast('Erreur lors du traitement de la commande.', 'error');
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.checkout-page {
  padding: 30px 0 80px;
  color: var(--color-text);
}

@media (max-width: 768px) {
  .checkout-grid {
    grid-template-columns: 1fr !important;
  }
}
</style>

