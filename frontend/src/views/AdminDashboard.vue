<template>
  <div class="admin-dashboard container" style="margin-top: 24px; margin-bottom: 50px;">
    
    <!-- Admin Header & Tabs Banner Card -->
    <div class="glass mb-8" style="padding: 28px 32px 14px; border-radius: 24px; background: rgba(5, 8, 17, 0.92); backdrop-filter: blur(20px); border: 1px solid var(--color-accent); box-shadow: 0 15px 45px rgba(0,0,0,0.5); color: #FFFFFF;">
      
      <!-- Header Top Row -->
      <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 16px;">
        <div class="flex items-center gap-4">
          <div style="width: 58px; height: 58px; border-radius: 50%; overflow: hidden; border: 2px solid var(--color-accent); flex-shrink: 0; box-shadow: 0 4px 14px rgba(212,175,55,0.4);">
            <img v-if="auth.user?.avatar" :src="auth.user.avatar" alt="Admin Avatar" style="width: 100%; height: 100%; object-fit: cover;" />
            <div v-else style="width: 100%; height: 100%; background: var(--color-accent); color: #050811; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.4rem;">
              {{ auth.user?.name ? auth.user.name.charAt(0).toUpperCase() : 'A' }}
            </div>
          </div>
          <div>
            <div class="flex items-center gap-3 mb-1" style="flex-wrap: wrap;">
              <h1 style="font-size: 1.8rem; margin: 0; font-weight: 800; color: #FFFFFF; font-family: var(--font-heading);">Tableau de bord Admin</h1>
              <span v-if="auth.user?.role === 'super_admin'" style="background: linear-gradient(135deg, #F0CC55, #D4AF37); color: #050811; padding: 4px 14px; border-radius: 999px; font-weight: 800; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 10px rgba(212,175,55,0.4);">
                <Crown :size="14" /> SUPER ADMIN
              </span>
              <span v-else-if="auth.user?.role === 'admin'" style="background: rgba(59,130,246,0.25); color: #60a5fa; border: 1px solid #3b82f6; padding: 4px 14px; border-radius: 999px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                <Shield :size="14" /> ADMIN
              </span>
              <span v-else-if="auth.user?.role === 'creator'" style="background: rgba(16,185,129,0.25); color: #34d399; border: 1px solid #10b981; padding: 4px 14px; border-radius: 999px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                <Wrench :size="14" /> CRÉATEUR
              </span>
              <span v-else-if="auth.user?.role === 'editor'" style="background: rgba(168,85,247,0.25); color: #c084fc; border: 1px solid #a855f7; padding: 4px 14px; border-radius: 999px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                <Feather :size="14" /> RÉDACTEUR
              </span>
              <span v-else-if="auth.user?.role === 'support'" style="background: rgba(236,72,153,0.25); color: #f472b6; border: 1px solid #ec4899; padding: 4px 14px; border-radius: 999px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                <Headphones :size="14" /> SUPPORT
              </span>
            </div>
            <p style="color: rgba(255,255,255,0.85); margin: 0; font-size: 0.92rem;">Bienvenue, {{ auth.user?.name || 'Administrateur' }} 👋 — Espace de gestion centralisé</p>
          </div>
        </div>
        <div class="flex gap-2" style="flex-wrap: wrap;">
          <button @click="generatePDFReport" class="btn btn-secondary flex items-center gap-2" style="font-size: 0.85rem; background: rgba(255,255,255,0.95); color: #050811; font-weight: 700;">
            <FileDown :size="15" /> Rapport Financier (PDF)
          </button>
          <button @click="handleLogout" class="btn btn-secondary flex items-center gap-2" style="font-size: 0.85rem; background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: #FFF;">
            <LogOut :size="15" /> Déconnexion
          </button>
        </div>
      </div>

      <!-- Onglets de navigation avec badges dynamiques -->
      <div class="flex gap-2" style="border-top: 1px solid rgba(212,175,55,0.25); padding-top: 14px; flex-wrap: wrap;">
        <button v-for="tab in visibleTabs" :key="tab.id" @click="activeTab = tab.id"
          :style="activeTab === tab.id ? 'background: rgba(212,175,55,0.22); color: #F0CC55; border: 1px solid var(--color-accent); font-weight: 800;' : 'color: rgba(255,255,255,0.85); border: 1px solid transparent; background: rgba(255,255,255,0.05);'"
          style="padding: 8px 16px; border-radius: 12px; cursor: pointer; font-size: 0.88rem; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px;">
          <span>{{ tab.icon }}</span> 
          <span>{{ tab.label }}</span>
          <span v-if="tabCounts[tab.id] !== null && tabCounts[tab.id] !== undefined" style="background: rgba(212,175,55,0.25); color: #F0CC55; padding: 1px 7px; border-radius: 999px; font-size: 0.72rem; font-weight: 800; border: 1px solid rgba(212,175,55,0.3);">
            {{ tabCounts[tab.id] }}
          </span>
        </button>
      </div>
    </div>

    <!-- Composants d'onglets dynamiques -->
    <AdminOverview v-if="activeTab === 'overview'" @data-loaded="handleDashboardData" />
    <AdminUsers v-if="activeTab === 'users' && auth.user?.role === 'super_admin'" :current-user-id="auth.user?.id" @count-updated="c => tabCounts.users = c" />
    <AdminProducts v-if="activeTab === 'products'" @count-updated="c => tabCounts.products = c" />
    <AdminQuotes v-if="activeTab === 'quotes'" @count-updated="c => tabCounts.quotes = c" />
    <AdminReviews v-if="activeTab === 'reviews'" @count-updated="c => tabCounts.reviews = c" />
    <AdminBlog v-if="activeTab === 'blog'" @count-updated="c => tabCounts.blog = c" />
    <AdminCoupons v-if="activeTab === 'coupons'" @count-updated="c => tabCounts.coupons = c" />
    <AdminNewsletter v-if="activeTab === 'newsletter'" @count-updated="c => tabCounts.newsletter = c" />

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useToastStore } from '../stores/toast';

// Icons
import { Crown, Shield, Wrench, Feather, Headphones, LogOut, FileDown } from 'lucide-vue-next';

// Components
import AdminOverview from '../components/admin/AdminOverview.vue';
import AdminUsers from '../components/admin/AdminUsers.vue';
import AdminProducts from '../components/admin/AdminProducts.vue';
import AdminQuotes from '../components/admin/AdminQuotes.vue';
import AdminReviews from '../components/admin/AdminReviews.vue';
import AdminBlog from '../components/admin/AdminBlog.vue';
import AdminCoupons from '../components/admin/AdminCoupons.vue';
import AdminNewsletter from '../components/admin/AdminNewsletter.vue';

const auth = useAuthStore();
const router = useRouter();
const toastStore = useToastStore();

const activeTab = ref('overview');

const dashboardData = ref({ orders: [], revenue: 0 });

const handleDashboardData = (data) => {
  dashboardData.value = data;
};

// Tabs counts overrides updated by subcomponents
const tabCounts = ref({
  users: 0,
  products: 0,
  quotes: 0,
  reviews: 0,
  blog: 0,
  coupons: 0,
  newsletter: 0
});

const visibleTabs = computed(() => {
  const role = auth.user?.role;
  if (role === 'super_admin') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'users', icon: '👑', label: 'Utilisateurs' },
      { id: 'products', icon: '🛍️', label: 'Produits' },
      { id: 'quotes', icon: '📋', label: 'Devis' },
      { id: 'reviews', icon: '⭐', label: 'Avis' },
      { id: 'blog', icon: '📝', label: 'Blog' },
      { id: 'coupons', icon: '🎟️', label: 'Coupons' },
      { id: 'newsletter', icon: '📧', label: 'Newsletter' },
    ];
  }
  if (role === 'creator') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'products', icon: '🛍️', label: 'Mes Produits' },
      { id: 'quotes', icon: '📋', label: 'Demandes Devis' },
      { id: 'reviews', icon: '⭐', label: 'Avis Produits' },
    ];
  }
  if (role === 'editor') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'blog', icon: '📝', label: 'Articles Blog' },
    ];
  }
  if (role === 'support') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'quotes', icon: '📋', label: 'Demandes Devis' },
      { id: 'reviews', icon: '⭐', label: 'Modération Avis' },
      { id: 'newsletter', icon: '📧', label: 'Abonnés' },
    ];
  }
  // Par défaut admin normal
  return [
    { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
    { id: 'products', icon: '🛍️', label: 'Produits' },
    { id: 'quotes', icon: '📋', label: 'Devis' },
    { id: 'reviews', icon: '⭐', label: 'Avis' },
    { id: 'blog', icon: '📝', label: 'Blog' },
    { id: 'coupons', icon: '🎟️', label: 'Coupons' },
    { id: 'newsletter', icon: '📧', label: 'Newsletter' },
  ];
});

onMounted(() => {
  const allowedRoles = ['admin', 'super_admin', 'creator', 'editor', 'support'];
  if (!auth.token || !allowedRoles.includes(auth.user?.role)) { 
    router.push('/login'); 
  }
});

const handleLogout = async () => {
  await auth.logout();
  router.push('/');
};

const generatePDFReport = () => {
  toastStore.showToast('Génération du rapport financier PDF...', 'info');
  const reportHTML = `<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Rapport Financier - Yass Digital Lab</title>
  <style>
    body { font-family: sans-serif; padding: 40px; color: #0f172a; }
    h1 { color: #d4af37; border-bottom: 2px solid #d4af37; padding-bottom: 10px; }
    .kpi { display: flex; gap: 20px; margin: 20px 0; }
    .card { background: #f8fafc; border: 1px solid #cbd5e1; padding: 15px 25px; border-radius: 10px; flex: 1; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
    th { background: #f1f5f9; }
  </style>
</head>
<body>
  <h1>Yass Digital Lab — Rapport financier & Ventes</h1>
  <p>Généré le: ${new Date().toLocaleString('fr-FR')}</p>
  <div class="kpi">
    <div class="card"><h3>Revenu Total</h3><h2>${dashboardData.value.revenue.toFixed(2)} FCFA</h2></div>
    <div class="card"><h3>Total Commandes</h3><h2>${dashboardData.value.orders.length}</h2></div>
  </div>
  <h3>Dernières Transactions</h3>
  <table>
    <thead><tr><th>ID</th><th>Client</th><th>Montant</th><th>Statut</th></tr></thead>
    <tbody>
      ${dashboardData.value.orders.map(o => `<tr><td>#${o.id}</td><td>${o.email}</td><td>${o.total_amount} FCFA</td><td>${o.status}</td></tr>`).join('')}
    </tbody>
  </table>
</body>
</html>`;
  const blob = new Blob([reportHTML], { type: 'text/html;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `rapport_financier_yassdigitallab_${new Date().toISOString().slice(0,10)}.html`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
  toastStore.showToast('Rapport financier généré !', 'success');
};
</script>

<style scoped>
</style>
