<template>
  <div class="sales-chart-card glass" style="padding: 28px; border-radius: 20px; background: var(--color-bg-card); border: 1px solid var(--color-border); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
    <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 12px;">
      <div>
        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--color-text);">📊 Analyse des Ventes & Revenus</h3>
        <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 3px 0 0;">Évolution mensuelle du chiffre d'affaires (€)</p>
      </div>
      <div style="background: rgba(212,175,55,0.15); color: var(--color-accent); border: 1px solid rgba(212,175,55,0.3); padding: 4px 14px; border-radius: 999px; font-weight: 800; font-size: 0.8rem;">
        +34.8% ce mois-ci
      </div>
    </div>

    <!-- SVG Interactive Chart -->
    <div class="chart-wrapper" style="height: 200px; position: relative;">
      <svg width="100%" height="100%" viewBox="0 0 520 190" preserveAspectRatio="none">
        <defs>
          <linearGradient id="chartGlow" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#D4AF37" stop-opacity="0.45"/>
            <stop offset="100%" stop-color="#D4AF37" stop-opacity="0.02"/>
          </linearGradient>
        </defs>

        <!-- Grid Lines -->
        <line x1="15" y1="40" x2="505" y2="40" stroke="var(--color-border)" stroke-dasharray="4"/>
        <line x1="15" y1="90" x2="505" y2="90" stroke="var(--color-border)" stroke-dasharray="4"/>
        <line x1="15" y1="140" x2="505" y2="140" stroke="var(--color-border)" stroke-dasharray="4"/>

        <!-- Area Fill -->
        <path d="M 15,160 Q 95,120 175,100 T 335,50 T 505,25 L 505,175 L 15,175 Z" fill="url(#chartGlow)" />

        <!-- Line Trend -->
        <path d="M 15,160 Q 95,120 175,100 T 335,50 T 505,25" fill="none" stroke="#D4AF37" stroke-width="4" stroke-linecap="round" />

        <!-- Data Points -->
        <circle cx="15" cy="160" r="5" fill="#D4AF37"/>
        <circle cx="113" cy="120" r="5" fill="#D4AF37"/>
        <circle cx="211" cy="92" r="5" fill="#D4AF37"/>
        <circle cx="309" cy="65" r="5" fill="#D4AF37"/>
        <circle cx="407" cy="42" r="5" fill="#D4AF37"/>
        <circle cx="505" cy="25" r="6" fill="var(--color-bg-card)" stroke="#D4AF37" stroke-width="3"/>
      </svg>
    </div>

    <!-- Month Labels -->
    <div class="flex justify-between mt-4" style="color: var(--color-text-light); font-size: 0.82rem; font-weight: 600; padding: 0 6px;">
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
  transition: transform 0.2s ease;
}
</style>
