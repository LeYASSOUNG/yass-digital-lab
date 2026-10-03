<template>
  <div class="sales-chart-card glass fade-in" style="padding: 28px; border-radius: var(--radius-lg); background: var(--color-bg-card); border: 1px solid var(--color-border); box-shadow: var(--shadow-md);">
    
    <!-- Chart Header -->
    <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 16px;">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <BarChart3 :size="22" style="color: var(--color-primary);" />
          <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: var(--color-text);">
            Analyse des Ventes & Revenus
          </h3>
        </div>
        <p style="color: var(--color-text-muted); font-size: 0.88rem; margin: 0;">Évolution du chiffre d'affaires en temps réel (FCFA)</p>
      </div>

      <div class="flex items-center gap-3" style="flex-wrap: wrap;">
        <!-- Filter Period Pills -->
        <div class="flex items-center gap-1" style="background: rgba(99,102,241,0.06); padding: 4px; border-radius: var(--radius-pill); border: 1px solid var(--color-border);">
          <button 
            v-for="p in periods" 
            :key="p.id" 
            @click="selectedPeriod = p.id"
            :class="selectedPeriod === p.id ? 'btn-primary' : 'btn-secondary'"
            style="padding: 4px 14px; border-radius: var(--radius-pill); border: none; font-size: 0.78rem; cursor: pointer;"
          >
            {{ p.label }}
          </button>
        </div>

        <span class="badge-pill badge-emerald" style="font-size: 0.82rem; padding: 6px 14px;">
          <TrendingUp :size="14" /> {{ growthText }}
        </span>
      </div>
    </div>

    <!-- Chart Container with Y-Axis and Floating Tooltip -->
    <div style="display: flex; gap: 12px; align-items: stretch; margin-bottom: 8px;">
      
      <!-- Y-Axis Scale Labels Dynamiques -->
      <div style="display: flex; flex-direction: column; justify-content: space-between; font-size: 0.75rem; color: var(--color-text-muted); font-weight: 700; padding: 10px 0; text-align: right; min-width: 55px;">
        <span>{{ (yMaxScale * 1.0).toFixed(0) }} FCFA</span>
        <span>{{ (yMaxScale * 0.66).toFixed(0) }} FCFA</span>
        <span>{{ (yMaxScale * 0.33).toFixed(0) }} FCFA</span>
        <span>0 FCFA</span>
      </div>

      <!-- Main Interactive SVG Chart -->
      <div class="chart-wrapper" style="flex: 1; height: 210px; position: relative;" @mouseleave="hoveredPoint = null">
        
        <!-- Floating Tooltip Box -->
        <div 
          v-if="hoveredPoint" 
          class="glass tooltip-card"
          :style="{ left: hoveredPoint.xPercent + '%', top: (hoveredPoint.cy - 45) + 'px' }"
          style="position: absolute; transform: translateX(-50%); background: rgba(15,23,42,0.95); border: 1px solid var(--color-primary); padding: 8px 14px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); pointer-events: none; z-index: 10;"
        >
          <strong style="color: #818CF8; display: block; font-size: 0.82rem;">{{ hoveredPoint.label }}</strong>
          <span style="font-weight: 800; font-size: 0.95rem; color: #FFFFFF;">{{ (hoveredPoint.value || 0).toLocaleString('fr-FR') }} FCFA</span>
          <span style="display: block; font-size: 0.72rem; color: #94A3B8;">{{ hoveredPoint.orders || 0 }} commande(s)</span>
        </div>

        <svg width="100%" height="100%" viewBox="0 0 520 190" preserveAspectRatio="none">
          <defs>
            <linearGradient id="chartGlow" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#6366F1" stop-opacity="0.45"/>
              <stop offset="100%" stop-color="#6366F1" stop-opacity="0.0"/>
            </linearGradient>
          </defs>

          <!-- Grid Lines -->
          <line x1="15" y1="20" x2="505" y2="20" stroke="var(--color-border)" stroke-dasharray="4" opacity="0.6"/>
          <line x1="15" y1="70" x2="505" y2="70" stroke="var(--color-border)" stroke-dasharray="4" opacity="0.6"/>
          <line x1="15" y1="120" x2="505" y2="120" stroke="var(--color-border)" stroke-dasharray="4" opacity="0.6"/>
          <line x1="15" y1="170" x2="505" y2="170" stroke="var(--color-border)" opacity="0.8"/>

          <!-- Area Fill (Dynamique) -->
          <path v-if="areaPathD" :d="areaPathD" fill="url(#chartGlow)" />

          <!-- Line Trend (Dynamique) -->
          <path v-if="linePathD" :d="linePathD" fill="none" stroke="#6366F1" stroke-width="4" stroke-linecap="round" />

          <!-- Interactive Hover Line -->
          <line 
            v-if="hoveredPoint"
            :x1="hoveredPoint.cx" 
            y1="15" 
            :x2="hoveredPoint.cx" 
            y2="170" 
            stroke="#6366F1" 
            stroke-dasharray="3" 
            stroke-width="2" 
            opacity="0.8"
          />

          <!-- Data Points -->
          <g 
            v-for="(pt, idx) in points" 
            :key="idx" 
            @mouseenter="hoveredPoint = { ...pt, xPercent: (pt.cx / 520) * 100 }"
            style="cursor: pointer;"
          >
            <circle :cx="pt.cx" :cy="pt.cy" r="14" fill="transparent" />
            <circle 
              :cx="pt.cx" 
              :cy="pt.cy" 
              :r="hoveredPoint?.label === pt.label ? 7 : (idx === points.length - 1 ? 6 : 5)" 
              :fill="idx === points.length - 1 ? 'var(--color-bg-card)' : '#6366F1'" 
              stroke="#6366F1" 
              :stroke-width="idx === points.length - 1 ? 3 : 1"
            />
          </g>
        </svg>

      </div>
    </div>

    <!-- Month Labels -->
    <div style="display: flex; gap: 12px; align-items: center;">
      <div style="min-width: 55px;"></div>
      <div class="flex justify-between flex-1" style="color: var(--color-text-muted); font-size: 0.82rem; font-weight: 600; padding: 0 6px;">
        <span v-for="pt in points" :key="pt.label" :style="pt.label.includes('Actuel') ? 'color: var(--color-primary); font-weight: 800;' : ''">
          {{ pt.label }}
        </span>
      </div>
    </div>

    <!-- Financial Summary Bar (Données Réelles) -->
    <div class="grid mt-6 p-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; background: rgba(99,102,241,0.06); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
      <div>
        <span style="display: block; font-size: 0.78rem; color: var(--color-text-muted); font-weight: 600;">Moyenne Mensuelle</span>
        <strong style="font-size: 1.05rem; color: var(--color-text);">{{ kpis.average_monthly_rev.toLocaleString('fr-FR') }} FCFA / mois</strong>
      </div>
      <div>
        <span style="display: block; font-size: 0.78rem; color: var(--color-text-muted); font-weight: 600;">Meilleure Performance</span>
        <strong style="font-size: 1.05rem; color: #10B981;">{{ kpis.best_month_name }} ({{ kpis.best_month_amount.toLocaleString('fr-FR') }} FCFA)</strong>
      </div>
      <div>
        <span style="display: block; font-size: 0.78rem; color: var(--color-text-muted); font-weight: 600;">Panier Moyen Client</span>
        <strong style="font-size: 1.05rem; color: var(--color-primary);">{{ kpis.average_order_value.toLocaleString('fr-FR') }} FCFA</strong>
      </div>
    </div>

    <!-- Top Produits & Devis Stats -->
    <div class="grid mt-6" style="grid-template-columns: 1fr 1fr; gap: 20px;">

      <!-- Top 5 Produits les plus vendus -->
      <div style="background: rgba(99,102,241,0.04); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 18px;">
        <h4 style="font-size: 0.88rem; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px; display: flex; align-items: center; gap: 6px;">
          <TrophyIcon :size="14" style="color: #F59E0B;" /> Top Produits Vendus
        </h4>
        <div v-if="topProducts.length === 0" style="color: var(--color-text-muted); font-size: 0.85rem; text-align: center; padding: 16px 0;">Aucune vente enregistrée.</div>
        <ul v-else style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px;">
          <li v-for="(prod, i) in topProducts" :key="i" style="display: flex; align-items: center; gap: 10px;">
            <span style="width: 22px; height: 22px; background: rgba(99,102,241,0.12); border-radius: 6px; font-size: 0.72rem; font-weight: 800; color: var(--color-primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">{{ i + 1 }}</span>
            <span style="flex: 1; font-size: 0.83rem; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ prod.product_title }}</span>
            <span style="font-size: 0.78rem; color: #10B981; font-weight: 700; white-space: nowrap;">{{ prod.total_sales }} vente(s)</span>
            <span style="font-size: 0.78rem; color: var(--color-text-muted); white-space: nowrap;">{{ Number(prod.total_revenue).toFixed(0) }} FCFA</span>
          </li>
        </ul>
      </div>

      <!-- Stats Devis -->
      <div style="background: rgba(99,102,241,0.04); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 18px;">
        <h4 style="font-size: 0.88rem; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px; display: flex; align-items: center; gap: 6px;">
          <FileTextIcon :size="14" style="color: #6366F1;" /> Demandes de Devis
        </h4>
        <div style="display: flex; flex-direction: column; gap: 10px;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.85rem;">En attente</span>
            <span class="badge-pill" style="background: rgba(245,158,11,0.15); color: #F59E0B; border: 1px solid rgba(245,158,11,0.3); font-size: 0.78rem; padding: 2px 10px;">{{ quoteStats.pending }}</span>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.85rem;">Contactés</span>
            <span class="badge-pill" style="background: rgba(99,102,241,0.12); color: #818CF8; border: 1px solid rgba(99,102,241,0.3); font-size: 0.78rem; padding: 2px 10px;">{{ quoteStats.contacted }}</span>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.85rem;">Complétés</span>
            <span class="badge-pill" style="background: rgba(16,185,129,0.12); color: #10B981; border: 1px solid rgba(16,185,129,0.3); font-size: 0.78rem; padding: 2px 10px;">{{ quoteStats.completed }}</span>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--color-border); padding-top: 8px; margin-top: 2px;">
            <span style="font-size: 0.85rem; font-weight: 700;">Total</span>
            <span style="font-size: 0.85rem; font-weight: 800; color: var(--color-primary);">{{ quoteStats.total }}</span>
          </div>
        </div>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { BarChart3, TrendingUp, Trophy as TrophyIcon, FileText as FileTextIcon } from 'lucide-vue-next';
import api from '../api';

const selectedPeriod = ref('6m');

const periods = [
  { id: '1m', label: '30 jours' },
  { id: '6m', label: '6 mois' },
  { id: '1y', label: '1 an' }
];

const hoveredPoint = ref(null);
const points = ref([]);
const topProducts = ref([]);
const quoteStats = ref({ pending: 0, contacted: 0, completed: 0, total: 0 });
const kpis = ref({
  total_revenue: 0,
  average_order_value: 0,
  average_monthly_rev: 0,
  best_month_name: 'N/A',
  best_month_amount: 0
});

const growthText = computed(() => {
  if (points.value.length < 2) return '+0% ce mois-ci';
  const current = points.value[points.value.length - 1]?.value || 0;
  const previous = points.value[points.value.length - 2]?.value || 0;
  if (previous === 0) return current > 0 ? '+100% ce mois-ci' : '0% ce mois-ci';
  const rate = (((current - previous) / previous) * 100).toFixed(1);
  return `${rate >= 0 ? '+' : ''}${rate}% vs mois dernier`;
});

const yMaxScale = computed(() => {
  if (!points.value.length) return 100;
  const max = Math.max(...points.value.map(p => p.value));
  return max > 0 ? Math.ceil(max / 10) * 10 : 100;
});

const linePathD = computed(() => {
  if (!points.value || points.value.length === 0) return '';
  return 'M ' + points.value.map(p => `${p.cx},${p.cy}`).join(' L ');
});

const areaPathD = computed(() => {
  if (!points.value || points.value.length === 0) return '';
  const firstX = points.value[0].cx;
  const lastX = points.value[points.value.length - 1].cx;
  const linePoints = points.value.map(p => `${p.cx},${p.cy}`).join(' L ');
  return `M ${firstX},170 L ${linePoints} L ${lastX},170 Z`;
});

const fetchAnalytics = async () => {
  try {
    const res = await api.get(`/admin/analytics?period=${selectedPeriod.value}`);
    if (res.data) {
      if (res.data.chart && res.data.chart.points) {
        points.value = res.data.chart.points;
      }
      if (res.data.kpis) {
        kpis.value = res.data.kpis;
      }
      if (res.data.top_products) {
        topProducts.value = res.data.top_products;
      }
      if (res.data.quote_stats) {
        quoteStats.value = res.data.quote_stats;
      }
    }
  } catch (err) {
    console.error('Erreur chargement analytics SalesChart:', err);
  }
};

onMounted(() => {
  fetchAnalytics();
});

watch(selectedPeriod, () => {
  fetchAnalytics();
});
</script>

