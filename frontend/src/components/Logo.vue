<template>
  <div class="brand-logo flex items-center gap-3">
    <router-link to="/" class="flex items-center gap-3" style="text-decoration: none;">
      
      <!-- Emblem YDL Graphic Box -->
      <div class="logo-box" :style="{ width: size + 'px', height: size + 'px' }">
        <svg viewBox="0 0 100 100" width="100%" height="100%">
          <defs>
            <linearGradient id="goldGradientLogo" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#F5D77F"/>
              <stop offset="50%" stop-color="#D4AF37"/>
              <stop offset="100%" stop-color="#C59B27"/>
            </linearGradient>
          </defs>
          
          <!-- Background Shape -->
          <rect width="100" height="100" rx="22" fill="#060B18"/>
          <rect width="94" height="94" x="3" y="3" rx="19" fill="none" stroke="rgba(212,175,55,0.3)" stroke-width="2"/>

          <!-- Pixel Blocks Top-Left -->
          <rect x="18" y="16" width="10" height="10" fill="url(#goldGradientLogo)" rx="1"/>
          <rect x="32" y="16" width="10" height="10" fill="url(#goldGradientLogo)" rx="1"/>
          <rect x="32" y="30" width="10" height="10" fill="url(#goldGradientLogo)" rx="1"/>

          <!-- Y Monogram (Gold) -->
          <path d="M 22 34 L 42 60 L 42 82 L 54 82 L 54 60 L 74 34 L 60 34 L 48 50 L 36 34 Z" fill="url(#goldGradientLogo)"/>

          <!-- D Arch (White) -->
          <path d="M 52 34 H 74 C 88 34 96 44 96 58 C 96 72 88 82 74 82 H 52 Z M 64 44 V 72 H 72 C 80 72 84 66 84 58 C 84 50 80 44 72 44 Z" fill="#FFFFFF"/>
        </svg>
      </div>

      <!-- High Contrast Brand Text -->
      <div v-if="mode !== 'icon'" class="logo-text flex flex-col justify-center">
        <div class="brand-title flex items-center gap-1" style="font-family: var(--font-heading); font-weight: 800; font-size: 1.35rem; letter-spacing: -0.02em; line-height: 1.1;">
          <span class="text-gold">YASS</span>
          <span class="text-main">DIGITAL</span>
          <span class="text-gold">LAB</span>
        </div>
        <span v-if="showTagline" class="brand-sub" style="font-size: 0.65rem; color: var(--color-text-light); letter-spacing: 0.12em; text-transform: uppercase; font-weight: 700; margin-top: 2px;">
          Outils Numériques Intelligents
        </span>
      </div>

    </router-link>
  </div>
</template>

<script setup>
defineProps({
  size: { type: Number, default: 44 },
  mode: { type: String, default: 'full' },
  showTagline: { type: Boolean, default: true }
});
</script>

<style scoped>
.brand-logo {
  display: inline-flex;
}
.logo-box {
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(212, 175, 55, 0.3);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  flex-shrink: 0;
}
.brand-logo:hover .logo-box {
  transform: scale(1.06);
  box-shadow: 0 6px 22px rgba(212, 175, 55, 0.5);
}

.text-gold {
  color: var(--color-accent);
  text-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
}

.text-main {
  color: #0F172A;
}

[data-theme="dark"] .text-main {
  color: #FFFFFF;
}
</style>
