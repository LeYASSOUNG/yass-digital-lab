<template>
  <transition name="slide-up">
    <div 
      v-if="showPrompt" 
      class="pwa-prompt-card glass fade-in"
      style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; max-width: 400px; width: calc(100% - 48px); padding: 20px; border-radius: var(--radius-lg); background: rgba(15, 23, 42, 0.96); backdrop-filter: blur(20px); border: 1px solid var(--color-primary); box-shadow: 0 20px 40px rgba(0,0,0,0.4); color: #FFFFFF;"
    >
      <div class="flex items-start justify-between gap-3 mb-3">
        <div class="flex items-center gap-3">
          <div style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #6366F1, #8B5CF6); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 14px rgba(99,102,241,0.4);">
            <Smartphone :size="24" style="color: #FFFFFF;" />
          </div>
          <div>
            <h4 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #FFFFFF;">
              Installer Yass Digital Lab
            </h4>
            <span style="font-size: 0.76rem; color: #818CF8; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
              <Zap :size="12" /> Application Web Progressive (PWA)
            </span>
          </div>
        </div>
        
        <button @click="dismissPrompt" style="background: transparent; border: none; color: #94A3B8; cursor: pointer; padding: 4px;" title="Fermer">
          <X :size="18" />
        </button>
      </div>

      <p style="color: #94A3B8; font-size: 0.86rem; margin: 0 0 16px; line-height: 1.5;">
        Ajoutez l'application sur votre écran d'accueil pour un accès instantané en 1 clic, une expérience plein écran et le mode hors-ligne.
      </p>

      <div class="flex items-center gap-2">
        <button 
          @click="installPwa" 
          class="btn btn-primary flex items-center justify-center gap-2" 
          style="flex: 1; padding: 10px 16px; font-size: 0.88rem; font-weight: 800; border-radius: var(--radius-md);"
        >
          <Download :size="16" /> Installer maintenant
        </button>
        <button 
          @click="dismissPrompt" 
          class="btn btn-secondary" 
          style="padding: 10px 14px; font-size: 0.85rem; background: rgba(255,255,255,0.08); color: #CBD5E1; border: none; border-radius: var(--radius-md);"
        >
          Plus tard
        </button>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Smartphone, Download, X, Zap } from 'lucide-vue-next';

const showPrompt = ref(false);
let deferredPrompt = null;

const isStandalone = () => {
  return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
};

const handleBeforeInstallPrompt = (e) => {
  e.preventDefault();
  deferredPrompt = e;
  if (!sessionStorage.getItem('pwa_prompt_dismissed') && !isStandalone()) {
    showPrompt.value = true;
  }
};

const installPwa = async () => {
  if (!deferredPrompt) {
    showPrompt.value = false;
    return;
  }
  deferredPrompt.prompt();
  const choiceResult = await deferredPrompt.userChoice;
  if (choiceResult.outcome === 'accepted') {
    console.log('Installation PWA acceptée par l\'utilisateur');
  }
  deferredPrompt = null;
  showPrompt.value = false;
};

const dismissPrompt = () => {
  showPrompt.value = false;
  sessionStorage.setItem('pwa_prompt_dismissed', 'true');
};

onMounted(() => {
  window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
});

onUnmounted(() => {
  window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
});
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from,
.slide-up-leave-to {
  opacity: 0;
  transform: translateY(30px) scale(0.95);
}
</style>

