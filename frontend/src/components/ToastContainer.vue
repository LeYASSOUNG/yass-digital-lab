<template>
  <div class="toast-container">
    <TransitionGroup name="toast">
      <div 
        v-for="toast in toastStore.toasts" 
        :key="toast.id" 
        class="toast-item glass"
        :class="toast.type"
      >
        <span class="toast-icon">
          <CheckCircle2 v-if="toast.type === 'success'" :size="18" style="color: #10B981;" />
          <AlertCircle v-else-if="toast.type === 'error'" :size="18" style="color: #EF4444;" />
          <AlertTriangle v-else-if="toast.type === 'warning'" :size="18" style="color: #F59E0B;" />
          <Info v-else :size="18" style="color: var(--color-accent);" />
        </span>
        <span class="toast-message">{{ toast.message }}</span>
        <button @click="toastStore.removeToast(toast.id)" class="toast-close" title="Fermer">
          <X :size="14" />
        </button>

        <!-- Progress Bar Indicator -->
        <div class="toast-progress-bar" :class="toast.type"></div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useToastStore } from '../stores/toast';
import { CheckCircle2, AlertCircle, AlertTriangle, Info, X } from 'lucide-vue-next';

const toastStore = useToastStore();
</script>

<style scoped>
.toast-container {
  position: fixed;
  top: 80px;
  right: 24px;
  z-index: 99999;
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-width: 380px;
  pointer-events: none;
}

.toast-item {
  pointer-events: auto;
  position: relative;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 18px 18px;
  border-radius: 14px;
  font-size: 0.9rem;
  font-weight: 600;
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35);
  border-left: 4px solid var(--color-accent);
  overflow: hidden;
  backdrop-filter: blur(12px);
}

.toast-item.success {
  border-left-color: #10B981;
}

.toast-item.error {
  border-left-color: #EF4444;
}

.toast-item.warning {
  border-left-color: #F59E0B;
}

.toast-message {
  flex: 1;
  color: var(--color-text);
  line-height: 1.4;
}

.toast-close {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--color-text-light);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4px;
  border-radius: 4px;
  transition: background 0.2s, color 0.2s;
}

.toast-close:hover {
  background: rgba(255, 255, 255, 0.1);
  color: var(--color-text);
}

.toast-progress-bar {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 3px;
  background: var(--color-accent);
  width: 100%;
  animation: toastProgress 3s linear forwards;
}

.toast-progress-bar.success {
  background: #10B981;
}

.toast-progress-bar.error {
  background: #EF4444;
}

.toast-progress-bar.warning {
  background: #F59E0B;
}

@keyframes toastProgress {
  from { width: 100%; }
  to { width: 0%; }
}

/* Animations */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(40px) scale(0.9);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(40px) scale(0.9);
}
</style>

