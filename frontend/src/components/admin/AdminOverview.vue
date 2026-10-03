<template>
  <div class="fade-in">
    <!-- Cartes KPIs avec Tendances -->
    <div class="grid mb-8" style="grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 20px;">
      
      <!-- KPI 1 : Revenu Total -->
      <div class="glass" style="padding: 24px 26px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid var(--color-accent); background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
        <div class="flex justify-between items-center mb-3">
          <span style="color: var(--color-text-light); font-size: 0.88rem; font-weight: 700;">Revenu Total</span>
          <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(212,175,55,0.15); border: 1px solid rgba(212,175,55,0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <DollarSign :size="20" style="color: var(--color-accent);" />
          </div>
        </div>
        <h2 style="color: var(--color-text); font-size: 2.1rem; font-weight: 800; margin: 4px 0 10px; font-family: var(--font-heading);">{{ totalRevenue.toFixed(2) }} FCFA</h2>
        <div>
          <span style="font-size: 0.76rem; color: #10b981; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25); padding: 4px 10px; border-radius: 999px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
            <TrendingUp :size="13" /> +15.4% vs mois dernier
          </span>
        </div>
      </div>

      <!-- KPI 2 : Commandes -->
      <div class="glass" style="padding: 24px 26px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #3b82f6; background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
        <div class="flex justify-between items-center mb-3">
          <span style="color: var(--color-text-light); font-size: 0.88rem; font-weight: 700;">Commandes</span>
          <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <ShoppingBag :size="20" style="color: #3b82f6;" />
          </div>
        </div>
        <h2 style="color: var(--color-text); font-size: 2.1rem; font-weight: 800; margin: 4px 0 10px; font-family: var(--font-heading);">{{ orders.length }}</h2>
        <div>
          <span style="font-size: 0.76rem; color: #3b82f6; background: rgba(59,130,246,0.12); border: 1px solid rgba(59,130,246,0.25); padding: 4px 10px; border-radius: 999px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
            <CheckCircle2 :size="13" /> {{ orders.filter(o => o.status === 'paid').length }} payée(s)
          </span>
        </div>
      </div>

      <!-- KPI 3 : Catalogue Produits -->
      <div class="glass" style="padding: 24px 26px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #10b981; background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
        <div class="flex justify-between items-center mb-3">
          <span style="color: var(--color-text-light); font-size: 0.88rem; font-weight: 700;">Catalogue Produits</span>
          <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <Package :size="20" style="color: #10b981;" />
          </div>
        </div>
        <h2 style="color: var(--color-text); font-size: 2.1rem; font-weight: 800; margin: 4px 0 10px; font-family: var(--font-heading);">{{ productsCount }}</h2>
        <div>
          <span style="font-size: 0.76rem; color: #10b981; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25); padding: 4px 10px; border-radius: 999px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
            ⚡ Prêts au téléchargement
          </span>
        </div>
      </div>

      <!-- KPI 4 : Abonnés Newsletter -->
      <div class="glass" style="padding: 24px 26px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #a855f7; background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
        <div class="flex justify-between items-center mb-3">
          <span style="color: var(--color-text-light); font-size: 0.88rem; font-weight: 700;">Abonnés Newsletter</span>
          <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(168,85,247,0.15); border: 1px solid rgba(168,85,247,0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <Mail :size="20" style="color: #a855f7;" />
          </div>
        </div>
        <h2 style="color: var(--color-text); font-size: 2.1rem; font-weight: 800; margin: 4px 0 10px; font-family: var(--font-heading);">{{ subscribersCount }}</h2>
        <div>
          <span style="font-size: 0.76rem; color: #a855f7; background: rgba(168,85,247,0.12); border: 1px solid rgba(168,85,247,0.25); padding: 4px 10px; border-radius: 999px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
            📧 Audience active
          </span>
        </div>
      </div>

    </div>

    <!-- Graphique d'Analyse des Ventes -->
    <div class="mb-8">
      <SalesChart />
    </div>

    <!-- Dernières commandes avec filtre -->
    <div class="glass" style="padding: 28px; border-radius: 20px;">
      <div class="flex justify-between items-center mb-4" style="flex-wrap: wrap; gap: 12px;">
        <h3 class="flex items-center gap-2" style="font-size: 1.25rem; font-weight: 700; margin: 0;">
          <ShoppingBag :size="20" style="color: var(--color-accent);" /> Dernières Commandes Clients
        </h3>
        <div class="flex items-center gap-2">
          <div style="position: relative; width: 220px;">
            <input v-model="searchOrderQuery" type="text" placeholder="Filtrer commande/email..." style="width: 100%; padding: 6px 12px 6px 15px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
          </div>
          <button @click="exportOrdersCSV" class="btn btn-secondary flex items-center gap-2" style="padding: 6px 14px; font-size: 0.82rem;">
            <Download :size="14" /> Exporter CSV
          </button>
        </div>
      </div>

      <div v-if="filteredOrders.length === 0" style="color: var(--color-text-light); text-align: center; padding: 30px;">Aucune commande trouvée.</div>
      <div v-else style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Email Client</th>
              <th style="padding: 12px; text-align: left;">Montant</th>
              <th style="padding: 12px; text-align: left;">Statut</th>
              <th style="padding: 12px; text-align: left;">Facture</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in paginatedOrders" :key="order.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; font-weight: 700; color: var(--color-text-light);">#{{ order.id }}</td>
              <td style="padding: 12px; font-weight: 600;">{{ order.email }}</td>
              <td style="padding: 12px; color: var(--color-accent); font-weight: 800;">{{ order.total_amount }} FCFA</td>
              <td style="padding: 12px;">
                <span :style="order.status === 'paid' ? 'background: rgba(34,197,94,0.15); color: #22c55e; border: 1px solid rgba(34,197,94,0.3);' : 'background: rgba(234,179,8,0.15); color: #eab308; border: 1px solid rgba(234,179,8,0.3);'" style="padding: 3px 10px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                  <CheckCircle2 v-if="order.status === 'paid'" :size="12" />
                  <Clock v-else :size="12" />
                  {{ order.status === 'paid' ? 'Payée' : 'En attente' }}
                </span>
              </td>
              <td style="padding: 12px;">
                <button @click="downloadAdminInvoice(order.id)" class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                  <Download :size="12" /> PDF
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Navigation Pagination Commandes -->
        <div class="flex justify-between items-center mt-4 pt-3" style="border-top: 1px solid var(--color-border); font-size: 0.85rem;">
          <span style="color: var(--color-text-light);">Page {{ orderPage }} sur {{ totalOrderPages }} ({{ filteredOrders.length }} commandes)</span>
          <div class="flex gap-2">
            <button @click="orderPage = Math.max(1, orderPage - 1)" :disabled="orderPage === 1" class="btn btn-secondary" style="padding: 4px 12px; font-size: 0.8rem;">
              ← Précédent
            </button>
            <button @click="orderPage = Math.min(totalOrderPages, orderPage + 1)" :disabled="orderPage === totalOrderPages" class="btn btn-secondary" style="padding: 4px 12px; font-size: 0.8rem;">
              Suivant →
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { 
  DollarSign, 
  ShoppingBag, 
  Package, 
  Mail, 
  Download, 
  CheckCircle2, 
  Clock, 
  TrendingUp 
} from 'lucide-vue-next';
import api from '../../api';
import SalesChart from '../SalesChart.vue';
import { useToastStore } from '../../stores/toast';

const toastStore = useToastStore();
const orders = ref([]);
const searchOrderQuery = ref('');
const productsCount = ref(0);
const subscribersCount = ref(0);

const orderPage = ref(1);
const orderPerPage = 5;

const emit = defineEmits(['data-loaded']);

const filteredOrders = computed(() => {
  if (!searchOrderQuery.value.trim()) return orders.value;
  const q = searchOrderQuery.value.toLowerCase();
  return orders.value.filter(o => String(o.id).includes(q) || o.email?.toLowerCase().includes(q) || o.status?.toLowerCase().includes(q));
});

const paginatedOrders = computed(() => {
  const start = (orderPage.value - 1) * orderPerPage;
  return filteredOrders.value.slice(start, start + orderPerPage);
});

const totalOrderPages = computed(() => Math.ceil(filteredOrders.value.length / orderPerPage) || 1);

const totalRevenue = computed(() => {
  return orders.value.reduce((sum, o) => sum + parseFloat(o.total_amount || 0), 0);
});

const loadOrders = async () => {
  try {
    const res = await api.get('/orders');
    orders.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
    emit('data-loaded', { orders: orders.value, revenue: totalRevenue.value });
  } catch(e) { 
    orders.value = []; 
  }
};

const loadKPIs = async () => {
  try {
    const [prodRes, subRes] = await Promise.all([
      api.get('/products'),
      api.get('/newsletter')
    ]);
    const prodList = Array.isArray(prodRes.data) ? prodRes.data : (prodRes.data.data || []);
    const subList = Array.isArray(subRes.data) ? subRes.data : (subRes.data.data || []);
    productsCount.value = prodList.length;
    subscribersCount.value = subList.length;
  } catch(e) { 
    // Silently fail for KPIs if error
  }
};

const downloadAdminInvoice = async (orderId) => {
  try {
    const response = await api.get(`/orders/${orderId}/invoice`, {
      responseType: 'blob',
    });
    const blob = new Blob([response.data], { type: 'application/pdf' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `facture-admin-${String(orderId).padStart(6, '0')}.pdf`;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
    toastStore.showToast(`Facture #${String(orderId).padStart(6, '0')} téléchargée !`, 'success');
  } catch (error) {
    toastStore.showToast('Erreur lors du téléchargement de la facture.', 'error');
  }
};

const exportOrdersCSV = () => {
  if (orders.value.length === 0) {
    toastStore.showToast('Aucune commande à exporter.', 'info');
    return;
  }
  const headers = ['ID', 'Email Client', 'Montant XOF', 'Statut', 'Date'];
  const rows = orders.value.map(o => [o.id, o.email, o.total_amount, o.status, o.created_at || '']);
  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `rapport_ventes_${new Date().toISOString().slice(0,10)}.csv`);
  document.body.appendChild(link);
  link.click();
  link.remove();
  toastStore.showToast('Export CSV des ventes téléchargé avec succès !', 'success');
};

onMounted(() => {
  loadOrders();
  loadKPIs();
});
</script>
