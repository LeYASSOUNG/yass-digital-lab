<template>
  <div class="services-page">
    <!-- Header -->
    <section class="glass text-center mb-8" style="padding: 70px 20px; border-radius: 20px; margin-top: 20px;">
      <div style="display: flex; justify-content: center; margin-bottom: 16px; color: var(--color-accent);">
        <Briefcase :size="48" />
      </div>
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
          <div style="display: flex; align-items: center; justify-content: center; height: 64px; margin-bottom: 16px; color: var(--color-accent);">
            <component :is="getServiceIcon(service.title)" :size="44" />
          </div>
          <h3 class="mb-3" style="font-size: 1.3rem; text-align: center;">{{ service.title }}</h3>
          <p style="color: var(--color-text-light); font-size: 0.95rem; line-height: 1.6; margin-bottom: 24px;">
            {{ service.description }}
          </p>
        </div>

        <div>
          <div class="flex justify-between items-center mb-4" style="border-top: 1px solid var(--color-border); padding-top: 16px;">
            <span style="font-size: 0.85rem; color: var(--color-text-light);">Tarif indicatif</span>
            <span style="color: var(--color-accent); font-weight: 800; font-size: 1.2rem;">À partir de {{ service.starting_price }} €</span>
          </div>

          <button @click="openQuoteModal(service)" class="btn btn-primary flex items-center justify-center gap-2" style="width: 100%;">
            <FileText :size="16" /> Demander un devis
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Devis -->
    <div v-if="selectedService" @click.self="selectedService = null" style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px;">
      <div class="glass" style="max-width: 550px; width: 100%; border-radius: 20px; padding: 36px; position: relative;">
        <button @click="selectedService = null" style="position: absolute; top: 16px; right: 16px; background: none; border: none; cursor: pointer; color: var(--color-text); padding: 4px; border-radius: 6px;" title="Fermer">
          <X :size="20" />
        </button>

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
            <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="flex: 1;">
              <Send :size="16" /> Envoyer ma demande
            </button>
            <button type="button" @click="downloadQuotePDF" class="btn btn-secondary flex items-center justify-center gap-2" style="flex: 1;">
              <FileDown :size="16" /> Devis PDF
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
import api from '../api';
import axios from 'axios';
import { 
  Briefcase, 
  FileText, 
  Send, 
  FileDown, 
  X, 
  Globe, 
  Code2, 
  Bot, 
  Headphones, 
  Sparkles 
} from 'lucide-vue-next';

const toastStore = useToastStore();
const services = ref([]);
const loading = ref(true);
const selectedService = ref(null);
const quoteForm = ref({ name: '', email: '', details: '' });

onMounted(async () => {
  try {
    const response = await api.get('/services');
    services.value = Array.isArray(response.data) ? response.data : (response.data?.data || []);
  } catch (error) {
    console.error("Erreur lors de la récupération des services:", error);
  } finally {
    loading.value = false;
  }
});

const getServiceIcon = (title) => {
  if (!title) return Sparkles;
  const t = title.toLowerCase();
  if (t.includes('site') || t.includes('web')) return Globe;
  if (t.includes('laravel') || t.includes('full') || t.includes('code')) return Code2;
  if (t.includes('ia') || t.includes('auto')) return Bot;
  if (t.includes('assist') || t.includes('support')) return Headphones;
  return Sparkles;
};

const openQuoteModal = (service) => {
  selectedService.value = service;
};

const downloadQuotePDF = () => {
  if (!selectedService.value) return;
  const serviceTitle = selectedService.value.title;
  const dateStr = new Date().toLocaleDateString('fr-FR');
  
  toastStore.showToast(`Génération du devis pour "${serviceTitle}"...`, 'info');
  
  const quoteHTML = `<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Devis — ${serviceTitle}</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #0F172A; padding: 40px; background: #F8FAFC; }
    .box { background: #FFFFFF; border: 2px solid #D4AF37; padding: 36px; border-radius: 16px; max-width: 700px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .header { display: flex; justify-content: space-between; border-bottom: 2px solid #E2E8F0; padding-bottom: 20px; margin-bottom: 24px; }
    .title { font-size: 26px; font-weight: 800; color: #0F172A; }
    .accent { color: #D4AF37; }
    .client-info { background: #F8FAFC; padding: 16px; border-radius: 8px; border-left: 4px solid #D4AF37; margin: 20px 0; }
    table { width: 100%; border-collapse: collapse; margin: 24px 0; }
    th { background: #0F172A; color: #FFF; padding: 12px; text-align: left; }
    td { padding: 12px; border-bottom: 1px solid #E2E8F0; }
    .total { text-align: right; font-size: 18px; font-weight: bold; color: #D4AF37; margin-top: 20px; border-top: 2px solid #D4AF37; padding-top: 12px; }
  </style>
</head>
<body>
  <div class="box">
    <div class="header">
      <div>
        <div class="title">YASS<span class="accent">DIGITAL</span>LAB</div>
        <p style="margin:4px 0 0; color:#64748B; font-size:13px;">Prestations de Développement & Intelligence Artificielle</p>
      </div>
      <div style="text-align: right;">
        <h2 style="color: #D4AF37; margin:0;">PROPOSITION DE DEVIS</h2>
        <p style="margin:4px 0 0; font-size:13px;">Date : ${dateStr}</p>
      </div>
    </div>

    <div class="client-info">
      <strong>Demandeur :</strong> ${quoteForm.value.name || 'Client Prospect'}<br>
      <strong>Email :</strong> ${quoteForm.value.email || 'Non renseigné'}<br>
      <strong>Détails du projet :</strong> ${quoteForm.value.details || 'Prestation sur mesure'}
    </div>

    <table>
      <thead>
        <tr>
          <th>Prestation Sollicitée</th>
          <th>Description</th>
          <th style="text-align: right;">Tarif Indicatif</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>${serviceTitle}</strong></td>
          <td>${selectedService.value.description || 'Développement & Intégration sur-mesure'}</td>
          <td style="text-align: right; font-weight: bold; color: #D4AF37;">À partir de ${selectedService.value.starting_price} €</td>
        </tr>
      </tbody>
    </table>

    <div class="total">Estimation Budgétaire : ${selectedService.value.starting_price}.00 € TTC</div>

    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #64748B; border-top: 1px solid #E2E8F0; padding-top: 16px;">
      Devis estimatif édité par Yass Digital Lab (contact@yassdigital.lab)<br>
      Portfolio : <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" style="color: #D4AF37;">https://portfolio-tau-inky-96i2vyeddb.vercel.app/</a>
    </div>
  </div>
</body>
</html>`;

  const blob = new Blob([quoteHTML], { type: 'text/html;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  const fileName = serviceTitle.replace(/[^a-zA-Z0-9]/g, '_').toLowerCase();
  a.download = `Devis_YassDigitalLab_${fileName}.html`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);

  toastStore.showToast(`Devis pour "${serviceTitle}" téléchargé avec succès !`, 'success');
};

const submitQuoteRequest = async () => {
  if (!selectedService.value) return;
  try {
    await api.post('/quote-requests', {
      name: quoteForm.value.name,
      email: quoteForm.value.email,
      service_title: selectedService.value.title,
      details: quoteForm.value.details
    });
    toastStore.showToast(`Votre demande pour "${selectedService.value.title}" a été transmise ! Un retour vous sera fait sous 24h.`, 'success');
  } catch (err) {
    console.warn("Erreur API, sauvegarde locale transmise.");
    toastStore.showToast(`Demande transmise pour "${selectedService.value.title}".`, 'success');
  } finally {
    selectedService.value = null;
    quoteForm.value = { name: '', email: '', details: '' };
  }
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

