<template>
  <div class="sales-chart-card glass" style="padding: 28px; border-radius: 20px;">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h3 style="font-size: 1.25rem;">📊 Analyse des Ventes & Revenus</h3>
        <p style="color: var(--color-text-light); font-size: 0.85rem;">Évolution mensuelle du chiffre d'affaires (€)</p>
      </div>
      <div style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 4px 12px; border-radius: 999px; font-weight: 700; font-size: 0.8rem;">
        +34.8% ce mois-ci
      </div>
    </div>

    <!-- SVG Interactive Chart -->
    <div class="chart-wrapper" style="height: 200px; position: relative;">
      <svg width="100%" height="100%" viewBox="0 0 500 180" preserveAspectRatio="none">
        <defs>
          <linearGradient id="chartGlow" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#D4AF37" stop-opacity="0.4"/>
            <stop offset="100%" stop-color="#D4AF37" stop-opacity="0.0"/>
          </linearGradient>
        </defs>

        <!-- Grid Lines -->
        <line x1="0" y1="40" x2="500" y2="40" stroke="rgba(255,255,255,0.06)" stroke-dasharray="4"/>
        <line x1="0" y1="90" x2="500" y2="90" stroke="rgba(255,255,255,0.06)" stroke-dasharray="4"/>
        <line x1="0" y1="140" x2="500" y2="140" stroke="rgba(255,255,255,0.06)" stroke-dasharray="4"/>

        <!-- Area Fill -->
        <path d="M 0,160 Q 80,120 160,100 T 320,50 T 500,20 L 500,180 L 0,180 Z" fill="url(#chartGlow)" />

        <!-- Line Trend -->
        <path d="M 0,160 Q 80,120 160,100 T 320,50 T 500,20" fill="none" stroke="#D4AF37" stroke-width="4" stroke-linecap="round" />

        <!-- Data Points -->
        <circle cx="0" cy="160" r="5" fill="#D4AF37"/>
        <circle cx="120" cy="115" r="5" fill="#D4AF37"/>
        <circle cx="240" cy="80" r="5" fill="#D4AF37"/>
        <circle cx="360" cy="45" r="5" fill="#D4AF37"/>
        <circle cx="500" cy="20" r="6" fill="#FFF" stroke="#D4AF37" stroke-width="3"/>
      </svg>
    </div>

    <!-- Month Labels -->
    <div class="flex justify-between mt-4" style="color: var(--color-text-light); font-size: 0.8rem; font-weight: 600;">
      <span>Jan</span>
      <span>Fév</span>
      <span>Mar</span>
      <span>Avr</span>
      <span>Mai</span>
      <span>Juin</span>
      <span>Juil (Actuel)</span>
    </div>
  </div>
</template>

<style scoped>
.sales-chart-card {
  border: 1px solid var(--color-border);
}
</style>
