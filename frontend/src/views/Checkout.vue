<template>
  <div class="checkout-page">
    <div class="glass" style="margin-top: 40px; padding: 40px; border-radius: 20px;">
      <h1 class="mb-4 text-center">Finaliser la commande</h1>
      
      <div v-if="cart.items.length === 0" class="text-center" style="padding: 40px;">
        <p style="color: var(--color-text-light); margin-bottom: 20px;">Votre panier est vide.</p>
        <router-link to="/products" class="btn btn-primary">Retour aux produits</router-link>
      </div>

      <div v-else class="grid" style="grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;">
        <!-- Résumé de la commande -->
        <div>
          <h2 class="mb-4">Résumé</h2>
          <div v-for="item in cart.items" :key="item.id" class="flex justify-between items-center mb-4" style="border-bottom: 1px solid var(--color-border); padding-bottom: 10px;">
            <div>
              <p style="font-weight: bold;">{{ item.title }}</p>
              <p style="font-size: 0.9rem; color: var(--color-text-light);">Quantité: {{ item.quantity }}</p>
            </div>
            <div class="flex items-center gap-4">
              <span style="font-weight: bold; color: var(--color-primary);">{{ (item.price * item.quantity).toFixed(2) }} €</span>
              <button @click="cart.removeItem(item.id)" style="background: none; border: none; color: red; cursor: pointer; font-size: 0.8rem;">Retirer</button>
            </div>
          </div>

          <!-- Code Promo -->
          <div class="mb-6 p-4 glass" style="border-radius: 12px; background: rgba(212,175,55,0.05);">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">🎟️ Code Promo / Coupon</label>
            <div class="flex gap-2">
              <input v-model="couponCode" type="text" placeholder="ex: YASS20" style="flex: 1; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); text-transform: uppercase;" />
              <button @click="applyCoupon" type="button" class="btn btn-secondary" style="padding: 8px 14px; font-size: 0.85rem;" :disabled="applyingCoupon">
                {{ applyingCoupon ? '...' : 'Appliquer' }}
              </button>
            </div>
            <p v-if="couponMessage" :style="discount > 0 ? 'color: #22c55e;' : 'color: #ef4444;'" style="font-size: 0.85rem; margin-top: 6px;">{{ couponMessage }}</p>
          </div>

          <div class="flex justify-between items-center mt-4">
            <h3 style="color: var(--color-text-light);">Sous-total:</h3>
            <h3 style="color: var(--color-text);">{{ cart.totalPrice.toFixed(2) }} €</h3>
          </div>
          <div v-if="discount > 0" class="flex justify-between items-center mt-2" style="color: #22c55e;">
            <span>Réduction appliquée:</span>
            <span>-{{ discount.toFixed(2) }} €</span>
          </div>
          <div class="flex justify-between items-center mt-4" style="border-top: 2px solid var(--color-border); padding-top: 10px;">
            <h3 style="color: var(--color-text-light);">Total Final:</h3>
            <h2 style="color: var(--color-accent); font-size: 2rem;">{{ finalTotal.toFixed(2) }} €</h2>
          </div>
        </div>

        <!-- Formulaire et Paiement -->
        <div class="glass" style="padding: 30px; border-radius: 12px; background: rgba(0,0,0,0.02);">
          <h2 class="mb-4">Informations & Paiement</h2>
          <form @submit.prevent="processPayment">
            <div class="mb-4">
              <label style="display: block; margin-bottom: 8px; font-weight: bold;">Email</label>
              <input v-model="email" type="email" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" placeholder="votre@email.com" />
            </div>
            
            <div class="mb-4">
              <label style="display: block; margin-bottom: 8px; font-weight: bold;">Méthode de paiement</label>
              <select v-model="paymentMethod" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);">
                <option value="orange">Orange Money</option>
                <option value="mtn">MTN Mobile Money</option>
                <option value="wave">Wave</option>
                <option value="stripe">Carte Bancaire (Stripe)</option>
              </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 20px;" :disabled="loading">
              {{ loading ? 'Chargement...' : `Payer ${finalTotal.toFixed(2)} €` }}
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
import axios from 'axios';

const cart = useCartStore();
const router = useRouter();
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
    const res = await axios.post('http://localhost:8000/api/coupons/validate', { code: couponCode.value.trim() });
    if (res.data.discount_amount) {
      discount.value = parseFloat(res.data.discount_amount);
    } else if (res.data.discount_percentage) {
      discount.value = (cart.totalPrice * parseFloat(res.data.discount_percentage)) / 100;
    }
    couponMessage.value = res.data.message || 'Coupon appliqué !';
  } catch (e) {
    discount.value = 0;
    couponMessage.value = e.response?.data?.message || 'Code promo invalide.';
  } finally {
    applyingCoupon.value = false;
  }
};

const processPayment = async () => {
  loading.value = true;
  
  // Enregistrer la commande dans la BD
  try {
    await axios.post('http://localhost:8000/api/orders', {
      email: email.value,
      items: cart.items,
      total_amount: finalTotal.value
    });
  } catch(e) {
    console.error("Erreur enregistrement commande", e);
  }

  if (paymentMethod.value === 'stripe') {
    try {
      const response = await axios.post('http://localhost:8000/api/create-checkout-session', {
        items: cart.items,
        email: email.value
      });
      if (response.data.url) {
        window.location.href = response.data.url;
      }
    } catch (error) {
      console.error("Erreur de paiement", error);
      alert('Une erreur est survenue lors de l\'initialisation du paiement Stripe.');
    }
  } else {
    alert(`Paiement simulé avec ${paymentMethod.value} ! Merci de votre commande.`);
    cart.clearCart();
    router.push('/dashboard');
  }
  loading.value = false;
};
</script>

<style scoped>
@media (max-width: 768px) {
  .grid {
    grid-template-columns: 1fr !important;
  }
}
</style>
