<template>
  <div class="fade-in">
    <div class="glass" style="padding: 28px; border-radius: 20px;">
      <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 14px;">
        <div>
          <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">⭐ Modération des Avis Clients</h3>
          <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Consultez et supprimez les avis publiés sur la plateforme.</p>
        </div>
        <div class="flex items-center gap-3">
          <div style="position: relative; width: 220px;">
            <input v-model="searchReviewQuery" type="text" placeholder="Filtrer auteur, produit..." style="width: 100%; padding: 6px 12px 6px 15px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
          </div>
          <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 0.82rem;">
            {{ filteredReviews.length }} avis
          </span>
        </div>
      </div>

      <div v-if="filteredReviews.length === 0" class="text-center" style="padding: 40px; color: var(--color-text-light);">
        Aucun avis trouvé.
      </div>

      <div v-else style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 650px;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Auteur</th>
              <th style="padding: 12px; text-align: left;">Produit Concerné</th>
              <th style="padding: 12px; text-align: left;">Note</th>
              <th style="padding: 12px; text-align: left;">Commentaire</th>
              <th style="padding: 12px; text-align: left;">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="rev in filteredReviews" :key="rev.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ rev.id }}</td>
              <td style="padding: 12px; font-weight: 600;">{{ rev.name }}</td>
              <td style="padding: 12px; font-weight: 600; color: var(--color-text);">{{ rev.product?.title || 'Produit #' + rev.product_id }}</td>
              <td style="padding: 12px; color: var(--color-accent); font-weight: 800;">{{ '★'.repeat(rev.rating) }}</td>
              <td style="padding: 12px; max-width: 280px; font-size: 0.83rem; color: var(--color-text-light);">"{{ rev.comment }}"</td>
              <td style="padding: 12px;">
                <button @click="deleteReview(rev.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                  🗑️ Supprimer
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
import { ref, computed, onMounted } from 'vue';
import api from '../../api';
import { useToastStore } from '../../stores/toast';

const toastStore = useToastStore();
const reviewsList = ref([]);
const searchReviewQuery = ref('');
const emit = defineEmits(['count-updated']);

const filteredReviews = computed(() => {
  if (!searchReviewQuery.value.trim()) return reviewsList.value;
  const q = searchReviewQuery.value.toLowerCase();
  return reviewsList.value.filter(r => r.name?.toLowerCase().includes(q) || r.comment?.toLowerCase().includes(q));
});

const loadReviews = async () => {
  try {
    const res = await api.get('/reviews');
    reviewsList.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
    emit('count-updated', reviewsList.value.length);
  } catch(e) { 
    reviewsList.value = []; 
  }
};

const deleteReview = async (id) => {
  if (confirm('Supprimer cet avis client ?')) {
    try {
      await api.delete(`/reviews/${id}`);
      toastStore.showToast('Avis supprimé.', 'info');
      await loadReviews();
    } catch(e) { 
      toastStore.showToast('Erreur lors de la suppression.', 'error'); 
    }
  }
};

onMounted(() => {
  loadReviews();
});
</script>
