<template>
  <div class="not-found-page text-center" style="padding: 100px 20px;">
    <div class="glass" style="max-width: 500px; margin: 0 auto; padding: 60px 40px; border-radius: 24px;">
      <div style="font-size: 5rem; margin-bottom: 20px;">🧭</div>
      <h1 style="font-size: 3rem; color: var(--color-accent); font-weight: 800; margin-bottom: 10px;">404</h1>
      <h2 class="mb-4">Page introuvable</h2>
      <p style="color: var(--color-text-light); margin-bottom: 30px;">
        La page que vous recherchez n'existe pas ou a été déplacée.
      </p>
      <router-link to="/" class="btn btn-primary" style="padding: 0.8rem 2rem;">
        🏠 Retour à l'accueil
      </router-link>
    </div>
  </div>
</template>

<script setup>
</script>
