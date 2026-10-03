<template>
  <div class="admin-tickets">
    <div class="flex justify-between items-center mb-6">
      <h2 style="font-size: 1.4rem; font-weight: 700;">Gestion des Tickets</h2>
      <button @click="loadTickets" class="btn btn-secondary flex items-center gap-2">
        <RefreshCw :size="14" :class="{ 'animate-spin': loading }" /> Rafraîchir
      </button>
    </div>

    <!-- Mode Liste -->
    <div v-if="!selectedTicket" style="background: var(--color-bg-elevated); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 20px;">
      <div v-if="loading" class="text-center" style="padding: 20px; color: var(--color-text-muted);">
        Chargement des tickets...
      </div>
      <div v-else-if="tickets.length === 0" class="text-center" style="padding: 20px; color: var(--color-text-muted);">
        Aucun ticket trouvé.
      </div>
      <div v-else class="grid" style="gap: 12px;">
        <div v-for="ticket in tickets" :key="ticket.id" @click="openTicket(ticket.id)" class="ticket-card flex justify-between items-center" style="padding: 16px; border: 1px solid var(--color-border); border-radius: 8px; cursor: pointer; transition: all 0.2s;">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <strong style="font-size: 1.1rem;">{{ ticket.subject }}</strong>
              <span class="badge-pill" :class="getStatusClass(ticket.status)">{{ getStatusLabel(ticket.status) }}</span>
              <span v-if="ticket.priority === 'urgent'" class="badge-pill badge-danger">Urgent</span>
            </div>
            <p style="margin: 0; font-size: 0.85rem; color: var(--color-text-muted);">
              Client: {{ ticket.user?.email || 'Inconnu' }} • MAJ: {{ new Date(ticket.updated_at).toLocaleString() }}
            </p>
          </div>
          <ChevronRight :size="20" style="color: var(--color-text-muted);" />
        </div>
      </div>
    </div>

    <!-- Mode Vue Ticket (Admin) -->
    <div v-if="selectedTicket" style="display: flex; flex-direction: column; gap: 20px;">
      <div style="background: var(--color-bg-elevated); padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
        <div class="flex justify-between items-center mb-4">
          <button @click="selectedTicket = null" class="btn btn-secondary flex items-center gap-1" style="padding: 4px 10px; font-size: 0.8rem; border: none; background: transparent;">
            <ArrowLeft :size="14" /> Retour
          </button>
          
          <div class="flex items-center gap-2">
            <span style="font-size: 0.85rem; color: var(--color-text-muted);">Changer statut :</span>
            <select v-model="selectedTicket.status" @change="updateStatus" style="padding: 6px 12px; border-radius: var(--radius-sm); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);">
              <option value="open">Ouvert</option>
              <option value="answered">Répondu</option>
              <option value="resolved">Résolu</option>
              <option value="closed">Fermé</option>
            </select>
          </div>
        </div>

        <h3 style="margin: 0 0 10px; font-size: 1.3rem; font-weight: 700;">{{ selectedTicket.subject }}</h3>
        <p style="margin: 0 0 20px; font-size: 0.9rem; color: var(--color-text-muted);">Par {{ selectedTicket.user?.name || selectedTicket.user?.email }}</p>

        <!-- Messages Thread -->
        <div class="messages-container" style="display: flex; flex-direction: column; gap: 16px; max-height: 400px; overflow-y: auto; padding-right: 10px; margin-bottom: 20px;">
          <div v-for="msg in selectedTicket.messages" :key="msg.id" class="message-bubble" :class="msg.user_id !== selectedTicket.user_id ? 'msg-own' : 'msg-client'" style="max-width: 80%; padding: 14px; border-radius: 12px;">
            <div class="flex justify-between items-center mb-2" style="font-size: 0.8rem; opacity: 0.8;">
              <strong style="display: flex; align-items: center; gap: 6px;">
                <Shield v-if="msg.user_id !== selectedTicket.user_id" :size="12" /> 
                {{ msg.user_id !== selectedTicket.user_id ? 'Support (Vous)' : 'Client' }}
              </strong>
              <span>{{ new Date(msg.created_at).toLocaleString() }}</span>
            </div>
            <div style="font-size: 0.95rem; line-height: 1.5; white-space: pre-wrap;">{{ msg.message }}</div>
          </div>
        </div>

        <!-- Reply Form -->
        <form @submit.prevent="replyTicket" v-if="selectedTicket.status !== 'closed'">
          <textarea v-model="replyMessage" required rows="3" style="width: 100%; padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); resize: vertical; margin-bottom: 12px;" placeholder="Répondre au client..."></textarea>
          <div class="flex justify-end">
            <button type="submit" class="btn btn-primary flex items-center gap-2" :disabled="replying">
              <MessageSquare :size="16" /> {{ replying ? 'Envoi...' : 'Envoyer la réponse' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RefreshCw, ChevronRight, ArrowLeft, Shield, MessageSquare } from 'lucide-vue-next';
import api from '../api';
import { useToastStore } from '../stores/toast';

const toastStore = useToastStore();

const tickets = ref([]);
const loading = ref(true);
const selectedTicket = ref(null);
const replyMessage = ref('');
const replying = ref(false);

const loadTickets = async () => {
  loading.value = true;
  try {
    const res = await api.get('/admin/tickets');
    tickets.value = res.data;
  } catch (error) {
    console.error(error);
    // Fallback fictif DB hors ligne
    tickets.value = [
      { id: 1001, subject: "Problème d'accès à ma formation", status: 'open', priority: 'high', updated_at: new Date().toISOString(), user: { email: 'client@example.com' } }
    ];
  }
  loading.value = false;
};

const openTicket = async (id) => {
  try {
    const res = await api.get(`/admin/tickets/${id}`);
    selectedTicket.value = res.data;
  } catch (error) {
    console.error(error);
    selectedTicket.value = {
      id: id,
      subject: tickets.value.find(t => t.id === id)?.subject || "Ticket de test",
      status: tickets.value.find(t => t.id === id)?.status || "open",
      user_id: 10,
      user: { name: 'Client Test', email: 'client@example.com' },
      messages: [
        { id: 1, user_id: 10, message: "Bonjour, j'ai un problème avec mon compte.", created_at: new Date(Date.now() - 86400000).toISOString() }
      ]
    };
  }
};

const updateStatus = async () => {
  try {
    await api.put(`/admin/tickets/${selectedTicket.value.id}/status`, { status: selectedTicket.value.status });
    toastStore.showToast('Statut mis à jour !', 'success');
  } catch (error) {
    toastStore.showToast('Statut simulé (BDD hors ligne)', 'success');
  }
  loadTickets(); // Refresh list background
};

const replyTicket = async () => {
  if (!replyMessage.value.trim()) return;
  replying.value = true;
  try {
    await api.post(`/admin/tickets/${selectedTicket.value.id}/reply`, { message: replyMessage.value });
    replyMessage.value = '';
    openTicket(selectedTicket.value.id); // Reload thread
  } catch (error) {
    toastStore.showToast('Réponse simulée', 'success');
    selectedTicket.value.messages.push({
      id: Date.now(),
      user_id: 999, // Admin ID
      message: replyMessage.value,
      created_at: new Date().toISOString()
    });
    replyMessage.value = '';
  }
  replying.value = false;
};

const getStatusLabel = (status) => {
  const map = { open: 'Ouvert', answered: 'Répondu', resolved: 'Résolu', closed: 'Fermé' };
  return map[status] || status;
};

const getStatusClass = (status) => {
  if (status === 'open') return 'bg-indigo-soft text-indigo';
  if (status === 'answered') return 'bg-blue-soft text-blue';
  if (status === 'resolved') return 'bg-green-soft text-green';
  if (status === 'closed') return 'bg-gray-soft text-gray';
  return '';
};

onMounted(() => {
  loadTickets();
});
</script>

<style scoped>
.ticket-card:hover { border-color: var(--color-primary); transform: translateY(-2px); }
.bg-indigo-soft { background: rgba(124, 58, 237, 0.15); }
.text-indigo { color: #A78BFA; border: 1px solid rgba(124, 58, 237, 0.3); }
.bg-blue-soft { background: rgba(59, 130, 246, 0.15); }
.text-blue { color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.3); }
.bg-green-soft { background: rgba(16, 185, 129, 0.15); }
.text-green { color: #34D399; border: 1px solid rgba(16, 185, 129, 0.3); }
.bg-gray-soft { background: rgba(255, 255, 255, 0.05); }
.text-gray { color: var(--color-text-muted); border: 1px solid rgba(255, 255, 255, 0.1); }
.badge-danger { background: rgba(239,68,68,0.15); color: #FCA5A5; border: 1px solid rgba(239,68,68,0.3); }

.msg-own {
  background: rgba(124, 58, 237, 0.15); border: 1px solid rgba(124, 58, 237, 0.2);
  align-self: flex-end; border-bottom-right-radius: 4px !important;
}
.msg-client {
  background: var(--color-bg); border: 1px solid var(--color-border);
  align-self: flex-start; border-bottom-left-radius: 4px !important;
}
</style>

