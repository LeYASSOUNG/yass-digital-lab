<template>
  <div class="notification-center-wrapper" style="position: relative;">
    <!-- Bell Trigger Button -->
    <button @click="goToNotifications" class="notif-btn" title="Voir la page de notifications">
      <Bell :size="19" style="color: var(--color-text);" />
      <span v-if="notifStore.unreadCount > 0" class="notif-badge">
        {{ notifStore.unreadCount }}
      </span>
    </button>

    <!-- Notification Dropdown Modal -->
    <Transition name="notif-pop">
      <div v-if="isOpen" class="notif-dropdown glass">
        <!-- Header -->
        <div class="notif-header">
          <div class="flex justify-between items-center mb-3">
            <div class="flex items-center gap-2">
              <Bell :size="16" style="color: var(--color-accent);" />
              <span style="font-weight: 800; font-size: 0.95rem; color: var(--color-text);">Notifications</span>
              <span v-if="notifStore.unreadCount > 0" style="background: var(--color-accent); color: #050811; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 999px;">
                {{ notifStore.unreadCount }} non lue(s)
              </span>
            </div>
            <button @click="isOpen = false" style="background: none; border: none; cursor: pointer; color: var(--color-text-light);">
              <X :size="16" />
            </button>
          </div>

          <!-- Tabs Filter -->
          <div class="flex gap-2" style="background: rgba(0,0,0,0.1); padding: 3px; border-radius: 8px;">
            <button 
              @click="activeFilter = 'all'"
              :style="activeFilter === 'all' ? 'background: var(--color-bg-card); color: var(--color-accent); font-weight: 700;' : 'color: var(--color-text-light);'"
              style="flex: 1; border: none; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;"
            >
              Toutes ({{ notifStore.notifications.length }})
            </button>
            <button 
              @click="activeFilter = 'unread'"
              :style="activeFilter === 'unread' ? 'background: var(--color-bg-card); color: var(--color-accent); font-weight: 700;' : 'color: var(--color-text-light);'"
              style="flex: 1; border: none; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;"
            >
              Non lues ({{ notifStore.unreadCount }})
            </button>
          </div>
        </div>

        <!-- Notification List -->
        <div class="notif-list">
          <div v-if="filteredNotifications.length === 0" class="text-center" style="padding: 30px 16px; color: var(--color-text-light); font-size: 0.85rem;">
            <BellOff :size="24" style="color: var(--color-text-light); margin-bottom: 8px; display: block; margin-left: auto; margin-right: auto;" />
            Aucune notification pour le moment.
          </div>

          <div 
            v-else
            v-for="item in filteredNotifications" 
            :key="item.id" 
            class="notif-item flex items-start gap-3"
            :class="{ 'unread': !item.read }"
            @click="handleNotificationClick(item)"
          >
            <div class="notif-icon-box">
              <ShoppingCart v-if="item.type === 'order'" :size="15" style="color: #22c55e;" />
              <FileText v-else-if="item.type === 'quote'" :size="15" style="color: #3b82f6;" />
              <Sparkles v-else-if="item.type === 'promo'" :size="15" style="color: var(--color-accent);" />
              <Bell v-else :size="15" style="color: var(--color-text-light);" />
            </div>

            <div style="flex: 1;">
              <div class="flex justify-between items-center">
                <p class="notif-title">{{ item.title }}</p>
                <span v-if="!item.read" style="width: 7px; height: 7px; background: var(--color-accent); border-radius: 50%;"></span>
              </div>
              <p class="notif-desc">{{ item.desc }}</p>
              <div class="flex justify-between items-center mt-1">
                <span class="notif-time">{{ item.time }}</span>
                <span v-if="item.link" style="font-size: 0.7rem; color: var(--color-accent); display: inline-flex; align-items: center; gap: 2px;">
                  Voir <ExternalLink :size="10" />
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div v-if="notifStore.notifications.length > 0" class="notif-footer flex justify-between items-center">
          <button @click="notifStore.markAllAsRead" style="background: none; border: none; color: var(--color-accent); font-size: 0.75rem; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
            <CheckCheck :size="13" /> Tout marquer comme lu
          </button>
          <button @click="notifStore.clearAll" style="background: none; border: none; color: #ef4444; font-size: 0.75rem; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
            <Trash2 :size="13" /> Effacer tout
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useNotificationStore } from '../stores/notification';
import { Bell, BellOff, CheckCheck, ShoppingCart, FileText, Sparkles, Trash2, X, ExternalLink } from 'lucide-vue-next';

const notifStore = useNotificationStore();
const router = useRouter();

const isOpen = ref(false);
const activeFilter = ref('all');

const filteredNotifications = computed(() => {
  if (activeFilter.value === 'unread') {
    return notifStore.notifications.filter(n => !n.read);
  }
  return notifStore.notifications;
});

const goToNotifications = () => {
  isOpen.value = false;
  router.push('/notifications');
};

const handleNotificationClick = (item) => {
  notifStore.markAsRead(item.id);
  if (item.link) {
    isOpen.value = false;
    router.push(item.link);
  }
};
</script>

<style scoped>
.notif-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  width: 40px;
  height: 40px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  transition: all 0.2s;
}

.notif-btn:hover {
  background: rgba(212, 175, 55, 0.1);
  border-color: var(--color-accent);
}

.notif-badge {
  position: absolute;
  top: -5px;
  right: -5px;
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
  top: 52px;
  right: 0;
  width: 350px;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 16px 50px rgba(0, 0, 0, 0.4);
  z-index: 9999;
}

.notif-header {
  padding: 14px 16px;
  border-bottom: 1px solid var(--color-border);
  background: var(--color-bg-card);
}

.notif-list {
  max-height: 330px;
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

.notif-icon-box {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(212, 175, 55, 0.12);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
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

.notif-footer {
  padding: 10px 16px;
  background: var(--color-bg-card);
  border-top: 1px solid var(--color-border);
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
