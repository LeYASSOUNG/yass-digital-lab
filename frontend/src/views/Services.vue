<template>
  <div class="services-page">
    <!-- Header -->
    <section class="glass text-center mb-8" style="padding: 70px 20px; border-radius: 20px; margin-top: 20px;">
      <div style="font-size: 3rem; margin-bottom: 12px;">💼</div>
      <h1 class="mb-2" style="font-size: 2.2rem;">Services & Prestations Sur Mesure</h1>
      <p style="color: var(--color-text-light); font-size: 1.05rem; max-width: 600px; margin: 0 auto;">
        Développement web, intégration d'IA et automatisation d'entreprises par Yass Digital Lab.
      </p>
    </section>

    <!-- State : Chargement -->
    <div v-if="loading" class="text-center" style="padding: 80px;">
      <div class="spinner"></div>
      <p style="color: var(--color-accent); margin-top: 16px;">Chargement des services...</p>
    </div>

    <!-- Grille de services -->
    <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
      <div
        v-for="service in services"
        :key="service.id"
        class="service-card glass flex flex-col justify-between"
        style="padding: 36px 28px; border-radius: 18px; transition: transform 0.3s, border-color 0.3s; border: 1px solid var(--color-border);"
      >
        <div>
          <div style="font-size: 3rem; margin-bottom: 16px;">{{ serviceEmoji(service.title) }}</div>
          <h3 class="mb-3" style="font-size: 1.3rem;">{{ service.title }}</h3>
          <p style="color: var(--color-text-light); font-size: 0.95rem; line-height: 1.6; margin-bottom: 24px;">
            {{ service.description }}
          </p>
        </div>

        <div>
          <div class="flex justify-between items-center mb-4" style="border-top: 1px solid var(--color-border); padding-top: 16px;">
            <span style="font-size: 0.85rem; color: var(--color-text-light);">Tarif indicatif</span>
            <span style="color: var(--color-accent); font-weight: 800; font-size: 1.2rem;">À partir de {{ service.starting_price }} €</span>
          </div>

          <button @click="openQuoteModal(service)" class="btn btn-primary" style="width: 100%; justify-content: center;">
            📝 Demander un devis
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Devis -->
    <div v-if="selectedService" @click.self="selectedService = null" style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px;">
      <div class="glass" style="max-width: 550px; width: 100%; border-radius: 20px; padding: 36px; position: relative;">
        <button @click="selectedService = null" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 1.4rem; cursor: pointer; color: var(--color-text);">✕</button>

        <h2 class="mb-2" style="font-size: 1.5rem;">Demande de Devis</h2>
        <p style="color: var(--color-accent); font-weight: 600; margin-bottom: 20px;">Service : {{ selectedService.title }}</p>

        <form @submit.prevent="submitQuoteRequest" class="flex flex-col gap-4">
          <div>
            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Votre Nom</label>
            <input v-model="quoteForm.name" type="text" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" placeholder="John Doe" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Votre Email</label>
            <input v-model="quoteForm.email" type="email" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" placeholder="john@example.com" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Détails de votre besoin</label>
            <textarea v-model="quoteForm.details" required rows="4" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" placeholder="Expliquez brièvement votre projet, budget ou délai souhaité..."></textarea>
          </div>

          <div class="flex gap-2">
            <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
              🚀 Envoyer ma demande
            </button>
            <button type="button" @click="downloadQuotePDF" class="btn btn-secondary" style="flex: 1; justify-content: center;">
              📄 Devis PDF
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useToastStore } from '../stores/toast';
import axios from 'axios';

const toastStore = useToastStore();
const services = ref([]);
const loading = ref(true);
const selectedService = ref(null);
const quoteForm = ref({ name: '', email: '', details: '' });

onMounted(async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/services');
    services.value = response.data;
  } catch (error) {
    console.error("Erreur lors de la récupération des services:", error);
  } finally {
    loading.value = false;
  }
});

const serviceEmoji = (title) => {
  if (!title) return '⚡';
  if (title.toLowerCase().includes('site') || title.toLowerCase().includes('web')) return '🌐';
  if (title.toLowerCase().includes('laravel') || title.toLowerCase().includes('full')) return '⚙️';
  if (title.toLowerCase().includes('ia') || title.toLowerCase().includes('auto')) return '🤖';
  if (title.toLowerCase().includes('assist')) return '🎧';
  return '⚡';
};

const openQuoteModal = (service) => {
  selectedService.value = service;
};

const downloadQuotePDF = () => {
  toastStore.showToast(`Génération du devis PDF pour "${selectedService.value?.title}"... 📄`, 'info');
  window.print();
};

const submitQuoteRequest = () => {
  toastStore.showToast(`Votre demande pour "${selectedService.value.title}" a été transmise ! Un retour vous sera fait sous 24h.`, 'success');
  selectedService.value = null;
  quoteForm.value = { name: '', email: '', details: '' };
};
</script>

<style scoped>
.service-card:hover {
  transform: translateY(-6px);
  border-color: var(--color-accent) !important;
}
.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid rgba(212, 175, 55, 0.2);
  border-top-color: var(--color-accent);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
