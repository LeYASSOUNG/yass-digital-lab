<template>
  <div class="fade-in">
    <div class="glass" style="padding: 28px; border-radius: 20px;">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">📧 Liste des Abonnés Newsletter</h3>
          <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Téléchargez la liste pour vos campagnes de mailing.</p>
        </div>
        <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 0.82rem;">
          {{ subscribers.length }} abonné(s)
        </span>
      </div>

      <div v-if="subscribers.length === 0" class="text-center" style="padding: 40px; color: var(--color-text-light);">
        Aucun abonné pour le moment.
      </div>

      <div v-else style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 500px;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Email Abonné</th>
              <th style="padding: 12px; text-align: left;">Date d'inscription</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sub in subscribers" :key="sub.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ sub.id }}</td>
              <td style="padding: 12px; font-weight: 600;">{{ sub.email }}</td>
              <td style="padding: 12px; color: var(--color-text-light); font-size: 0.82rem;">{{ sub.created_at ? new Date(sub.created_at).toLocaleDateString('fr-FR') : 'Récent' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const subscribers = ref([]);
const emit = defineEmits(['count-updated']);

const loadSubscribers = async () => {
  try {
    const res = await api.get('/newsletter');
    subscribers.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
    emit('count-updated', subscribers.value.length);
  } catch(e) { 
    subscribers.value = []; 
  }
};

onMounted(() => {
  loadSubscribers();
});
</script>
