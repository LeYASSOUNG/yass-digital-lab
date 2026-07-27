<template>
  <div class="notification-center-wrapper" style="position: relative;">
    <!-- Bell Trigger Button -->
    <button @click="isOpen = !isOpen" class="notif-btn" title="Centre de notifications">
      <span style="font-size: 1.1rem;">🔔</span>
      <span v-if="unreadCount > 0" class="notif-badge">
        {{ unreadCount }}
      </span>
    </button>

    <!-- Notification Dropdown -->
    <Transition name="notif-pop">
      <div v-if="isOpen" class="notif-dropdown glass">
        <div class="notif-header flex justify-between items-center">
          <div class="flex items-center gap-2">
            <span style="font-weight: 700; font-size: 0.95rem;">Notifications</span>
            <span style="background: var(--color-accent); color: #050811; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">
              {{ unreadCount }} Nouveaux
            </span>
          </div>
          <button @click="markAllAsRead" style="background: none; border: none; color: var(--color-accent); font-size: 0.78rem; cursor: pointer; text-decoration: underline;">
            Tout marquer comme lu
          </button>
        </div>

        <div class="notif-list">
          <div 
            v-for="item in notifications" 
            :key="item.id" 
            class="notif-item flex items-start gap-3"
            :class="{ 'unread': !item.read }"
            @click="item.read = true"
          >
            <span class="notif-icon">{{ item.icon }}</span>
            <div style="flex: 1;">
              <p class="notif-title">{{ item.title }}</p>
              <p class="notif-desc">{{ item.desc }}</p>
              <span class="notif-time">{{ item.time }}</span>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const isOpen = ref(false);

const notifications = ref([
  { id: 1, icon: '🛒', title: 'Nouvelle Commande Enregistrée', desc: 'Votre commande #FA-000001 a été validée avec succès.', time: 'Il y a 5 min', read: false },
  { id: 2, icon: '🎉', title: 'Offre de Bienvenue', desc: 'Utilisez le code YASS20 pour 20% de réduction sur tout le site.', time: 'Il y a 1h', read: false },
  { id: 3, icon: '💡', title: 'Mise à jour Prompt Pack', desc: 'De nouveaux prompts ChatGPT 4.5 ont été ajoutés à votre espace.', time: 'Hier', read: true }
]);

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

const markAllAsRead = () => {
  notifications.value.forEach(n => n.read = true);
};
</script>

<style scoped>
.notif-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  width: 38px;
  height: 38px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  transition: all 0.2s;
}

.notif-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  background: #EF4444;
  color: #FFF;
  font-size: 0.7rem;
  font-weight: 800;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
}

.notif-dropdown {
  position: absolute;
  top: 50px;
  right: 0;
  width: 330px;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 16px 50px rgba(0, 0, 0, 0.35);
  z-index: 9999;
}

.notif-header {
  padding: 14px 16px;
  border-bottom: 1px solid var(--color-border);
  background: var(--color-bg-card);
}

.notif-list {
  max-height: 320px;
  overflow-y: auto;
  background: var(--color-bg);
}

.notif-item {
  padding: 12px 16px;
  border-bottom: 1px solid var(--color-border);
  cursor: pointer;
  transition: background 0.2s;
}

.notif-item:hover {
  background: rgba(212, 175, 55, 0.06);
}

.notif-item.unread {
  background: rgba(212, 175, 55, 0.1);
}

.notif-icon {
  font-size: 1.2rem;
  margin-top: 2px;
}

.notif-title {
  margin: 0;
  font-weight: 700;
  font-size: 0.85rem;
  color: var(--color-text);
}

.notif-desc {
  margin: 2px 0 4px;
  font-size: 0.78rem;
  color: var(--color-text-light);
  line-height: 1.35;
}

.notif-time {
  font-size: 0.68rem;
  color: var(--color-accent);
  font-weight: 600;
}

/* Animation */
.notif-pop-enter-active, .notif-pop-leave-active {
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.notif-pop-enter-from, .notif-pop-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}
</style>
