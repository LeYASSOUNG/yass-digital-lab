<template>
  <div class="notifications-page" style="margin-top: 20px;">
    
    <!-- Hero Header -->
    <section class="glass text-center" style="padding: 50px 20px; border-radius: 24px; margin-bottom: 30px; position: relative; overflow: hidden;">
      <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 300px; height: 300px; background: radial-gradient(circle, rgba(212,175,55,0.12), transparent 70%); pointer-events: none;"></div>
      <div style="font-size: 3rem; margin-bottom: 12px; display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background: rgba(212,175,55,0.1); border: 1px solid var(--color-accent);">
        <Bell :size="40" style="color: var(--color-accent);" />
      </div>
      <h1 class="mb-2" style="font-size: 2.2rem;">Centre de Notifications</h1>
      <p style="color: var(--color-text-light); font-size: 1.05rem;">
        Consultez l'historique complet de vos alertes, suivis de commandes, devis et offres promotionnelles.
      </p>
    </section>

    <!-- Stats KPIs -->
    <div class="grid mb-8" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
      
      <div style="padding: 22px 24px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid var(--color-accent); background: var(--color-bg-card); box-shadow: var(--shadow-sm); display: flex; flex-direction: column; justify-content: space-between;">
        <div class="flex justify-between items-center mb-2">
          <span style="font-size: 0.88rem; font-weight: 700; color: var(--color-text-light);">Total Alertes</span>
          <div style="width: 36px; height: 36px; border-radius: 12px; background: rgba(212,175,55,0.15); border: 1px solid rgba(212,175,55,0.3); display: flex; align-items: center; justify-content: center;">
            <Bell :size="18" style="color: var(--color-accent);" />
          </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 800; color: var(--color-text); margin: 4px 0 0; font-family: var(--font-heading);">{{ notifStore.notifications.length }}</h3>
      </div>

      <div style="padding: 22px 24px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #ef4444; background: var(--color-bg-card); box-shadow: var(--shadow-sm); display: flex; flex-direction: column; justify-content: space-between;">
        <div class="flex justify-between items-center mb-2">
          <span style="font-size: 0.88rem; font-weight: 700; color: var(--color-text-light);">Non Lues</span>
          <div style="width: 36px; height: 36px; border-radius: 12px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); display: flex; align-items: center; justify-content: center;">
            <AlertCircle :size="18" style="color: #ef4444;" />
          </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 800; color: #ef4444; margin: 4px 0 0; font-family: var(--font-heading);">{{ notifStore.unreadCount }}</h3>
      </div>

      <div style="padding: 22px 24px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #10b981; background: var(--color-bg-card); box-shadow: var(--shadow-sm); display: flex; flex-direction: column; justify-content: space-between;">
        <div class="flex justify-between items-center mb-2">
          <span style="font-size: 0.88rem; font-weight: 700; color: var(--color-text-light);">Commandes & Achats</span>
          <div style="width: 36px; height: 36px; border-radius: 12px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); display: flex; align-items: center; justify-content: center;">
            <ShoppingCart :size="18" style="color: #10b981;" />
          </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 800; color: #10b981; margin: 4px 0 0; font-family: var(--font-heading);">{{ orderCount }}</h3>
      </div>

      <div style="padding: 22px 24px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #3b82f6; background: var(--color-bg-card); box-shadow: var(--shadow-sm); display: flex; flex-direction: column; justify-content: space-between;">
        <div class="flex justify-between items-center mb-2">
          <span style="font-size: 0.88rem; font-weight: 700; color: var(--color-text-light);">Devis & Prestations</span>
          <div style="width: 36px; height: 36px; border-radius: 12px; background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); display: flex; align-items: center; justify-content: center;">
            <FileText :size="18" style="color: #3b82f6;" />
          </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 800; color: #3b82f6; margin: 4px 0 0; font-family: var(--font-heading);">{{ quoteCount }}</h3>
      </div>
    </div>

    <!-- Toolbar Filters & Actions -->
    <div class="glass flex justify-between items-center mb-8" style="padding: 20px; border-radius: 18px; flex-wrap: wrap; gap: 16px;">
      <!-- Search & Filters -->
      <div class="flex items-center gap-3" style="flex-wrap: wrap; flex: 1;">
        <div style="position: relative; min-width: 220px; flex: 1;">
          <Search :size="16" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);" />
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Rechercher une notification..." 
            style="width: 100%; padding: 9px 12px 9px 36px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.88rem;" 
          />
        </div>

        <button 
          v-for="filter in filterOptions" 
          :key="filter.id"
          @click="activeCategory = filter.id"
          class="btn"
          :style="activeCategory === filter.id ? 'background: var(--color-accent); color: #050811; font-weight: 800; border-color: var(--color-accent);' : 'background: var(--color-bg-card); color: var(--color-text); border: 1px solid var(--color-border);'"
          style="padding: 7px 16px; border-radius: 999px; font-size: 0.82rem; cursor: pointer; transition: all 0.2s;"
        >
          {{ filter.label }}
        </button>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-2">
        <button @click="notifStore.markAllAsRead" class="btn btn-secondary flex items-center gap-2" style="font-size: 0.85rem; padding: 8px 14px;">
          <CheckCheck :size="15" /> Tout marquer comme lu
        </button>
        <button @click="notifStore.clearAll" class="btn btn-secondary flex items-center gap-2" style="font-size: 0.85rem; padding: 8px 14px; color: #ef4444; border-color: rgba(239,68,68,0.3);">
          <Trash2 :size="15" /> Effacer tout
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredList.length === 0" class="glass text-center" style="padding: 60px 20px; border-radius: 20px;">
      <BellOff :size="48" style="color: var(--color-text-light); margin-bottom: 16px; display: block; margin-left: auto; margin-right: auto;" />
      <h3>Aucune notification trouvée</h3>
      <p style="color: var(--color-text-light); font-size: 0.9rem;">
        Aucune alerte ne correspond à votre filtre actuel.
      </p>
    </div>

    <!-- Notification Cards List -->
    <div v-else class="flex flex-col gap-4">
      <div 
        v-for="item in filteredList" 
        :key="item.id"
        class="glass card p-6 flex items-start gap-4"
        :style="!item.read ? 'border-left: 4px solid var(--color-accent); background: rgba(212,175,55,0.04);' : 'border-left: 4px solid transparent;'"
        style="border-radius: 18px; transition: all 0.2s;"
      >
        <!-- Icon Container -->
        <div 
          style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"
          :style="getIconBg(item.type)"
        >
          <ShoppingCart v-if="item.type === 'order'" :size="22" style="color: #22c55e;" />
          <FileText v-else-if="item.type === 'quote'" :size="22" style="color: #3b82f6;" />
          <Sparkles v-else-if="item.type === 'promo'" :size="22" style="color: var(--color-accent);" />
          <Bell v-else :size="22" style="color: var(--color-text);" />
        </div>

        <!-- Content -->
        <div style="flex: 1;">
          <div class="flex justify-between items-center mb-1">
            <h3 style="font-size: 1.1rem; margin: 0;">{{ item.title }}</h3>
            <span style="font-size: 0.78rem; color: var(--color-accent); font-weight: 600;">{{ item.time }}</span>
          </div>

          <p style="color: var(--color-text-light); font-size: 0.92rem; line-height: 1.6; margin-bottom: 12px;">
            {{ item.desc }}
          </p>

          <div class="flex items-center gap-3">
            <button 
              v-if="item.link" 
              @click="openLink(item)" 
              class="btn btn-primary flex items-center gap-2" 
              style="padding: 5px 14px; font-size: 0.8rem;"
            >
              <span>Accéder à la page</span> <ArrowRight :size="14" />
            </button>
            <button 
              v-if="!item.read" 
              @click="notifStore.markAsRead(item.id)" 
              class="btn btn-secondary" 
              style="padding: 5px 12px; font-size: 0.8rem;"
            >
              Marquer comme lu
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useNotificationStore } from '../stores/notification';
import { 
  Bell, 
  BellOff, 
  ShoppingCart, 
  FileText, 
  Sparkles, 
  AlertCircle, 
  CheckCheck, 
  Trash2, 
  Search, 
  ArrowRight 
} from 'lucide-vue-next';

const notifStore = useNotificationStore();
const router = useRouter();

const searchQuery = ref('');
const activeCategory = ref('all');

const filterOptions = [
  { id: 'all', label: 'Toutes' },
  { id: 'unread', label: 'Non lues' },
  { id: 'order', label: 'Commandes' },
  { id: 'quote', label: 'Devis' },
  { id: 'promo', label: 'Offres' },
];

const orderCount = computed(() => notifStore.notifications.filter(n => n.type === 'order').length);
const quoteCount = computed(() => notifStore.notifications.filter(n => n.type === 'quote').length);

const filteredList = computed(() => {
  let list = [...notifStore.notifications];

  if (activeCategory.value === 'unread') {
    list = list.filter(n => !n.read);
  } else if (activeCategory.value !== 'all') {
    list = list.filter(n => n.type === activeCategory.value);
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(n => 
      n.title?.toLowerCase().includes(q) || 
      n.desc?.toLowerCase().includes(q)
    );
  }

  return list;
});

const getIconBg = (type) => {
  if (type === 'order') return 'background: rgba(34, 197, 94, 0.15);';
  if (type === 'quote') return 'background: rgba(59, 130, 246, 0.15);';
  if (type === 'promo') return 'background: rgba(212, 175, 55, 0.15);';
  return 'background: rgba(255, 255, 255, 0.1);';
};

const openLink = (item) => {
  notifStore.markAsRead(item.id);
  if (item.link) {
    router.push(item.link);
  }
};
</script>

<style scoped>
.notifications-page {
  background: var(--page-gradient);
  padding: 30px 24px 80px;
  border-radius: 24px;
  margin-top: 15px;
  border: 1px solid rgba(255, 255, 255, 0.06);
  box-shadow: 0 25px 80px rgba(0, 0, 0, 0.7);
  color: var(--color-text);
}

.card:hover {
  transform: translateY(-2px);
  border-color: var(--color-accent) !important;
}
</style>

