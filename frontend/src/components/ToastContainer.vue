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
          <template v-if="toast.type === 'success'">✅</template>
          <template v-else-if="toast.type === 'error'">⚠️</template>
          <template v-else>💡</template>
        </span>
        <span class="toast-message">{{ toast.message }}</span>
        <button @click="toastStore.removeToast(toast.id)" class="toast-close">✕</button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useToastStore } from '../stores/toast';

const toastStore = useToastStore();
</script>

<style scoped>
.toast-container {
  position: fixed;
  top: 80px;
  right: 24px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-width: 380px;
  pointer-events: none;
}

.toast-item {
  pointer-events: auto;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 18px;
  border-radius: 14px;
  font-size: 0.9rem;
  font-weight: 600;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
  border-left: 4px solid var(--color-accent);
}

.toast-item.success {
  border-left-color: #10B981;
}

.toast-item.error {
  border-left-color: #EF4444;
}

.toast-message {
  flex: 1;
  color: var(--color-text);
}

.toast-close {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--color-text-light);
  font-size: 1rem;
  padding: 0 4px;
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
