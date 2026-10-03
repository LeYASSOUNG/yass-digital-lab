<template>
  <div class="fade-in">
    <div class="glass" style="padding: 28px; border-radius: 20px;">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">🎟️ Codes Promos & Réductions</h3>
          <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Créez des coupons pour stimuler vos ventes au checkout.</p>
        </div>
        <button @click="showCouponForm = !showCouponForm" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.88rem;">
          {{ showCouponForm ? '✕ Annuler' : '+ Nouveau Coupon' }}
        </button>
      </div>

      <div v-if="showCouponForm" class="mb-8" style="background: rgba(212,175,55,0.05); border: 1px solid rgba(212,175,55,0.25); padding: 24px; border-radius: 16px;">
        <h4 class="mb-4" style="font-size: 1.1rem; font-weight: 700;">Créer un Code Promo</h4>
        <form @submit.prevent="createCoupon" class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Code (ex: YASS20) *</label>
            <input v-model="newCoupon.code" type="text" required class="form-input" placeholder="YASS20" />
          </div>
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Pourcentage de Réduction (%)</label>
            <input v-model="newCoupon.discount_percentage" type="number" step="1" min="1" max="100" class="form-input" placeholder="20" />
          </div>
          <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="grid-column: span 2; padding: 11px; font-weight: 800;">
            🎟️ Enregistrer le coupon
          </button>
        </form>
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 550px;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Code Promo</th>
              <th style="padding: 12px; text-align: left;">Réduction</th>
              <th style="padding: 12px; text-align: left;">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in coupons" :key="c.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ c.id }}</td>
              <td style="padding: 12px; font-weight: 800; color: var(--color-accent); font-family: monospace; font-size: 0.95rem;">{{ c.code }}</td>
              <td style="padding: 12px; font-weight: 700;">{{ c.discount_percentage ? c.discount_percentage + '%' : (c.discount_amount + ' FCFA') }}</td>
              <td style="padding: 12px;">
                <button @click="deleteCoupon(c.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                  <Trash2 :size="13" /> Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Trash2 } from 'lucide-vue-next';
import api from '../../api';
import { useToastStore } from '../../stores/toast';

const toastStore = useToastStore();
const coupons = ref([]);
const showCouponForm = ref(false);
const newCoupon = ref({ code: '', discount_amount: '', discount_percentage: '', expires_at: '' });

const emit = defineEmits(['count-updated']);

const loadCoupons = async () => {
  try {
    const res = await api.get('/coupons');
    coupons.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
    emit('count-updated', coupons.value.length);
  } catch(e) { 
    coupons.value = []; 
  }
};

const createCoupon = async () => {
  try {
    await api.post('/coupons', newCoupon.value);
    showCouponForm.value = false;
    newCoupon.value = { code: '', discount_amount: '', discount_percentage: '', expires_at: '' };
    toastStore.showToast('Code promo créé avec succès !', 'success');
    await loadCoupons();
  } catch(e) { 
    toastStore.showToast('Erreur création du coupon.', 'error'); 
  }
};

const deleteCoupon = async (id) => {
  if (confirm('Supprimer ce coupon ?')) {
    try {
      await api.delete(`/coupons/${id}`);
      toastStore.showToast('Coupon supprimé.', 'info');
      await loadCoupons();
    } catch(e) { 
      toastStore.showToast('Erreur lors de la suppression.', 'error'); 
    }
  }
};

onMounted(() => {
  loadCoupons();
});
</script>

<style scoped>
.form-label {
  display: block;
  margin-bottom: 6px;
  color: var(--color-text);
}
.form-input {
  width: 100%;
  padding: 10px 14px;
  border-radius: 10px;
  border: 1px solid var(--color-border);
  background: var(--color-bg);
  color: var(--color-text);
  font-size: 0.9rem;
}
.form-input:focus {
  outline: none;
  border-color: var(--color-accent);
}
</style>
