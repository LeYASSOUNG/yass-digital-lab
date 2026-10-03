<template>
  <div class="fade-in">
    <div class="glass" style="padding: 28px; border-radius: 20px;">
      <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 14px;">
        <div>
          <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">📋 Demandes de Devis Client</h3>
          <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Consultez et répondez aux projets soumis par les utilisateurs.</p>
        </div>
        <div class="flex items-center gap-3">
          <div style="position: relative; width: 220px;">
            <input v-model="searchQuoteQuery" type="text" placeholder="Filtrer nom, service..." style="width: 100%; padding: 6px 12px 6px 15px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
          </div>
          <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 0.82rem;">
            {{ filteredQuoteRequests.length }} demande(s)
          </span>
        </div>
      </div>

      <div v-if="filteredQuoteRequests.length === 0" class="text-center" style="padding: 40px; color: var(--color-text-light);">
        Aucune demande de devis enregistrée.
      </div>

      <div v-else style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Demandeur</th>
              <th style="padding: 12px; text-align: left;">Service Vise</th>
              <th style="padding: 12px; text-align: left;">Budget & Message</th>
              <th style="padding: 12px; text-align: left;">Statut</th>
              <th style="padding: 12px; text-align: left;">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="q in filteredQuoteRequests" :key="q.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ q.id }}</td>
              <td style="padding: 12px;">
                <strong style="display: block; color: var(--color-text);">{{ q.name }}</strong>
                <span style="font-size: 0.8rem; color: var(--color-text-light);">{{ q.email }} • {{ q.phone || 'N/C' }}</span>
              </td>
              <td style="padding: 12px; font-weight: 700; color: var(--color-accent);">{{ q.service_title || 'Général' }}</td>
              <td style="padding: 12px; max-width: 260px; font-size: 0.83rem;">
                <span style="background: rgba(255,255,255,0.08); padding: 2px 8px; border-radius: 6px; font-size: 0.76rem; display: inline-block; margin-bottom: 4px;">💰 Budget: {{ q.budget || 'Non précisé' }}</span>
                <p style="margin: 0; color: var(--color-text-light); overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">"{{ q.description }}"</p>
              </td>
              <td style="padding: 12px;">
                <select :value="q.status || 'pending'" @change="updateQuoteStatus(q.id, $event.target.value)" style="padding: 4px 8px; border-radius: 6px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.78rem;">
                  <option value="pending">⏳ En attente</option>
                  <option value="in_progress">⚙️ En cours</option>
                  <option value="completed">✅ Traitée</option>
                  <option value="rejected">❌ Refusée</option>
                </select>
              </td>
              <td style="padding: 12px;">
                <button @click="deleteQuoteRequest(q.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
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
const quoteRequests = ref([]);
const searchQuoteQuery = ref('');
const emit = defineEmits(['count-updated']);

const filteredQuoteRequests = computed(() => {
  if (!searchQuoteQuery.value.trim()) return quoteRequests.value;
  const q = searchQuoteQuery.value.toLowerCase();
  return quoteRequests.value.filter(r => r.name?.toLowerCase().includes(q) || r.email?.toLowerCase().includes(q) || r.service_title?.toLowerCase().includes(q));
});

const loadQuoteRequests = async () => {
  try {
    const res = await api.get('/quote-requests');
    quoteRequests.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
    emit('count-updated', quoteRequests.value.length);
  } catch(e) { 
    quoteRequests.value = []; 
  }
};

const updateQuoteStatus = async (id, status) => {
  try {
    await api.put(`/quote-requests/${id}/status`, { status });
    toastStore.showToast('Statut de devis mis à jour !', 'success');
    await loadQuoteRequests();
  } catch(e) { 
    toastStore.showToast('Erreur lors de la mise à jour.', 'error'); 
  }
};

const deleteQuoteRequest = async (id) => {
  if (confirm('Supprimer cette demande de devis ?')) {
    try {
      await api.delete(`/quote-requests/${id}`);
      toastStore.showToast('Demande supprimée.', 'info');
      await loadQuoteRequests();
    } catch(e) { 
      toastStore.showToast('Erreur lors de la suppression.', 'error'); 
    }
  }
};

onMounted(() => {
  loadQuoteRequests();
});
</script>
