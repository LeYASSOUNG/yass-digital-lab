<template>
  <div class="app-layout flex flex-col min-h-screen">
    <!-- Background Video -->
    <video class="bg-video" autoplay loop muted playsinline>
      <source src="/background.mp4" type="video/mp4" />
    </video>
    
    <!-- Global Notifications -->
    <ToastContainer />
    
    <!-- Extracted Header (Nav, Mobile Menu, User) -->
    <AppHeader />

    <!-- Main View -->
    <main class="container" style="flex: 1; padding-bottom: 60px;">
      <router-view v-slot="{ Component }">
        <Transition name="page" mode="out-in">
          <component :is="Component" />
        </Transition>
      </router-view>
    </main>

    <!-- Extracted Footer -->
    <AppFooter @open-status-modal="showStatusModal = true" />

    <!-- Modals & Overlays -->
    <SystemStatusModal v-if="showStatusModal" @close="showStatusModal = false" />
    <ChatWidget />
    <PwaInstallPrompt />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from './stores/auth';
import { useToastStore } from './stores/toast';
import { useNotificationStore } from './stores/notification';

// Core Layout
import AppHeader from './components/layout/AppHeader.vue';
import AppFooter from './components/layout/AppFooter.vue';
import SystemStatusModal from './components/layout/SystemStatusModal.vue';

// Global Overlays
import ToastContainer from './components/ToastContainer.vue';
import ChatWidget from './components/ChatWidget.vue';
import PwaInstallPrompt from './components/PwaInstallPrompt.vue';

const showStatusModal = ref(false);
const auth = useAuthStore();
const toastStore = useToastStore();

onMounted(() => {
  // Start Notification Polling if logged in
  if (auth.token) {
    useNotificationStore().startPolling();
  }

  // Network Listeners
  const handleOffline = () => toastStore.showToast('⚡ Mode Hors-Ligne — Vos données sont préservées.', 'info');
  const handleOnline = () => toastStore.showToast('🟢 Connexion Internet rétablie !', 'success');
  window.addEventListener('offline', handleOffline);
  window.addEventListener('online', handleOnline);

  // Referral System
  const urlParams = new URLSearchParams(window.location.search);
  const refCode = urlParams.get('ref');
  if (refCode) {
    localStorage.setItem('referral_code', refCode);
    toastStore.showToast(`🎁 Bienvenue ! Code parrain ${refCode} — 20% avec YASS20.`, 'success');
  }
});
</script>

<style scoped>
.app-layout { min-height: 100vh; }

/* Page transitions */
.page-enter-active, .page-leave-active { transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1); }
.page-enter-from { opacity: 0; transform: translateY(12px); }
.page-leave-to   { opacity: 0; transform: translateY(-8px); }
</style>
