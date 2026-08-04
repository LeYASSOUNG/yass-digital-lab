<template>
  <div class="sales-chart-card glass" style="padding: 28px; border-radius: 22px; background: var(--color-bg-card); border: 1px solid var(--color-border); box-shadow: 0 10px 35px rgba(0,0,0,0.06);">
    
    <!-- Chart Header -->
    <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 16px;">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <span style="font-size: 1.3rem;">📊</span>
          <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: var(--color-text); font-family: var(--font-heading);">
            Analyse des Ventes & Revenus
          </h3>
        </div>
        <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 0;">Évolution mensuelle du chiffre d'affaires (€)</p>
      </div>

      <div class="flex items-center gap-3" style="flex-wrap: wrap;">
        <!-- Filter Period Pills -->
        <div class="flex items-center gap-1" style="background: rgba(255,255,255,0.05); padding: 3px; border-radius: 999px; border: 1px solid var(--color-border);">
          <button 
            v-for="p in periods" 
            :key="p.id" 
            @click="selectedPeriod = p.id"
            :style="selectedPeriod === p.id ? 'background: var(--color-accent); color: #050811; font-weight: 800;' : 'color: var(--color-text-light); font-weight: 600; background: transparent;'"
            style="padding: 4px 12px; border-radius: 999px; border: none; font-size: 0.78rem; cursor: pointer; transition: all 0.2s;"
          >
            {{ p.label }}
          </button>
        </div>

        <div style="background: rgba(212,175,55,0.15); color: var(--color-accent); border: 1px solid rgba(212,175,55,0.3); padding: 5px 14px; border-radius: 999px; font-weight: 800; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 5px;">
          <span>📈</span> +34.8% ce mois-ci
        </div>
      </div>
    </div>

    <!-- Chart Container with Y-Axis and Floating Tooltip -->
    <div style="display: flex; gap: 12px; align-items: stretch; margin-bottom: 8px;">
      
      <!-- Y-Axis Scale Labels -->
      <div style="display: flex; flex-direction: column; justify-content: space-between; font-size: 0.72rem; color: var(--color-text-light); font-weight: 700; padding: 10px 0; text-align: right; min-width: 45px;">
        <span>120 €</span>
        <span>80 €</span>
        <span>40 €</span>
        <span>0 €</span>
      </div>

      <!-- Main Interactive SVG Chart -->
      <div class="chart-wrapper" style="flex: 1; height: 210px; position: relative;" @mouseleave="hoveredPoint = null">
        
        <!-- Floating Tooltip Box -->
        <div 
          v-if="hoveredPoint" 
          class="glass tooltip-card"
          :style="{ left: hoveredPoint.xPercent + '%', top: (hoveredPoint.y - 45) + 'px' }"
        >
          <strong style="color: var(--color-accent); display: block; font-size: 0.82rem;">{{ hoveredPoint.label }}</strong>
          <span style="font-weight: 800; font-size: 0.95rem; color: #FFFFFF;">{{ hoveredPoint.value.toFixed(2) }} €</span>
          <span style="display: block; font-size: 0.72rem; color: rgba(255,255,255,0.8);">{{ hoveredPoint.orders }} commande(s)</span>
        </div>

        <svg width="100%" height="100%" viewBox="0 0 520 190" preserveAspectRatio="none">
          <defs>
            <linearGradient id="chartGlow" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#D4AF37" stop-opacity="0.45"/>
              <stop offset="100%" stop-color="#D4AF37" stop-opacity="0.0"/>
            </linearGradient>
          </defs>

          <!-- Grid Lines -->
          <line x1="15" y1="20" x2="505" y2="20" stroke="var(--color-border)" stroke-dasharray="4" opacity="0.6"/>
          <line x1="15" y1="70" x2="505" y2="70" stroke="var(--color-border)" stroke-dasharray="4" opacity="0.6"/>
          <line x1="15" y1="120" x2="505" y2="120" stroke="var(--color-border)" stroke-dasharray="4" opacity="0.6"/>
          <line x1="15" y1="170" x2="505" y2="170" stroke="var(--color-border)" opacity="0.8"/>

          <!-- Area Fill -->
          <path d="M 15,160 Q 95,120 175,100 T 335,50 T 505,25 L 505,170 L 15,170 Z" fill="url(#chartGlow)" />

          <!-- Line Trend -->
          <path d="M 15,160 Q 95,120 175,100 T 335,50 T 505,25" fill="none" stroke="#D4AF37" stroke-width="4" stroke-linecap="round" />

          <!-- Interactive Hover Line -->
          <line 
            v-if="hoveredPoint"
            :x1="hoveredPoint.cx" 
            y1="15" 
            :x2="hoveredPoint.cx" 
            y2="170" 
            stroke="#D4AF37" 
            stroke-dasharray="3" 
            stroke-width="2" 
            opacity="0.8"
          />

          <!-- Data Points -->
          <g 
            v-for="(pt, idx) in points" 
            :key="idx" 
            @mouseenter="hoveredPoint = { ...pt, xPercent: (pt.cx / 520) * 100, y: pt.cy }"
            style="cursor: pointer;"
          >
            <!-- Outer Glow / Hit target area -->
            <circle :cx="pt.cx" :cy="pt.cy" r="14" fill="transparent" />
            
            <circle 
              v-if="idx === points.length - 1" 
              :cx="pt.cx" 
              :cy="pt.cy" 
              r="10" 
              fill="rgba(212,175,55,0.3)"
              class="pulse-ring"
            />
            
            <circle 
              :cx="pt.cx" 
              :cy="pt.cy" 
              :r="hoveredPoint?.label === pt.label ? 7 : (idx === points.length - 1 ? 6 : 5)" 
              :fill="idx === points.length - 1 ? 'var(--color-bg-card)' : '#D4AF37'" 
              stroke="#D4AF37" 
              :stroke-width="idx === points.length - 1 ? 3 : 1"
            />
          </g>
        </svg>

      </div>
    </div>

    <!-- Month Labels -->
    <div style="display: flex; gap: 12px; align-items: center;">
      <div style="min-width: 45px;"></div>
      <div class="flex justify-between flex-1" style="color: var(--color-text-light); font-size: 0.82rem; font-weight: 600; padding: 0 6px;">
        <span v-for="pt in points" :key="pt.label" :style="pt.label.includes('Actuel') ? 'color: var(--color-accent); font-weight: 800;' : ''">
          {{ pt.label }}
        </span>
      </div>
    </div>

    <!-- Financial Summary Bar -->
    <div class="grid mt-6 p-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; background: rgba(0,0,0,0.04); border-radius: 16px; border: 1px solid var(--color-border);">
      <div>
        <span style="display: block; font-size: 0.76rem; color: var(--color-text-light); font-weight: 600;">Moyenne Mensuelle</span>
        <strong style="font-size: 1rem; color: var(--color-text);">42.50 € / mois</strong>
      </div>
      <div>
        <span style="display: block; font-size: 0.76rem; color: var(--color-text-light); font-weight: 600;">Meilleure Performance</span>
        <strong style="font-size: 1rem; color: #10b981;">Juillet (109.97 €)</strong>
      </div>
      <div>
        <span style="display: block; font-size: 0.76rem; color: var(--color-text-light); font-weight: 600;">Panier Moyen Client</span>
        <strong style="font-size: 1rem; color: var(--color-accent);">36.65 €</strong>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue';

const selectedPeriod = ref('7m');
const hoveredPoint = ref(null);

const periods = [
  { id: '7m', label: '7 Mois' },
  { id: '30d', label: '30 Jours' },
  { id: 'year', label: '2026' }
];

const points = [
  { cx: 15,  cy: 160, label: 'Jan',           value: 15.00,  orders: 1 },
  { cx: 113, cy: 120, label: 'Fév',           value: 35.00,  orders: 2 },
  { cx: 211, cy: 92,  label: 'Mar',           value: 52.00,  orders: 2 },
  { cx: 309, cy: 65,  label: 'Avr',           value: 70.00,  orders: 3 },
  { cx: 407, cy: 42,  label: 'Mai',           value: 88.50,  orders: 3 },
  { cx: 460, cy: 30,  label: 'Juin',          value: 95.00,  orders: 3 },
  { cx: 505, cy: 25,  label: 'Juil (Actuel)', value: 109.97, orders: 3 }
];
</script>

<style scoped>
.sales-chart-card {
  transition: transform 0.2s ease;
}

.tooltip-card {
  position: absolute;
  transform: translate(-50%, -100%);
  background: rgba(15, 23, 42, 0.95);
  border: 1px solid var(--color-accent);
  color: #FFFFFF;
  padding: 8px 14px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.4);
  pointer-events: none;
  z-index: 10;
  text-align: center;
  white-space: nowrap;
}

@keyframes pulse {
  0% { transform: scale(1); opacity: 0.7; }
  50% { transform: scale(1.6); opacity: 0; }
  100% { transform: scale(1); opacity: 0; }
}

.pulse-ring {
  transform-origin: center;
  animation: pulse 2s infinite ease-in-out;
}
</style>
