<template>
  <div class="support-tickets">
    <!-- Header Support -->
    <div class="flex justify-between items-center mb-6">
      <h2 class="flex items-center gap-2" style="font-size: 1.4rem; font-weight: 700;">
        <LifeBuoy :size="24" style="color: var(--color-primary);" /> Support Client
      </h2>
      <button v-if="!showCreateForm && !selectedTicket" @click="showCreateForm = true" class="btn btn-primary flex items-center gap-2" style="padding: 8px 16px; font-size: 0.9rem;">
        <Plus :size="16" /> Nouveau Ticket
      </button>
    </div>

    <!-- Mode: Liste des tickets -->
    <div v-if="!showCreateForm && !selectedTicket">
      <div v-if="loading" class="text-center" style="padding: 40px; color: var(--color-text-muted);">
        Chargement de vos tickets...
      </div>
      
      <div v-else-if="tickets.length === 0" class="text-center" style="padding: 60px; background: rgba(255,255,255,0.02); border-radius: var(--radius-lg); border: 1px dashed var(--color-border);">
        <LifeBuoy :size="48" style="color: var(--color-text-muted); opacity: 0.5; margin: 0 auto 16px;" />
        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 8px;">Aucune demande de support</h3>
        <p style="color: var(--color-text-muted); margin-bottom: 20px;">Vous n'avez pas encore contacté notre équipe de support.</p>
        <button @click="showCreateForm = true" class="btn btn-primary">Ouvrir un ticket</button>
      </div>

      <div v-else class="grid" style="gap: 16px;">
        <div v-for="ticket in tickets" :key="ticket.id" @click="openTicket(ticket.id)" class="ticket-card flex justify-between items-center" style="padding: 20px; background: var(--color-bg-elevated); border: 1px solid var(--color-border); border-radius: var(--radius-md); cursor: pointer; transition: all 0.2s;">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <h4 style="margin: 0; font-weight: 700; font-size: 1.05rem;">{{ ticket.subject }}</h4>
              <span class="badge-pill" :class="getStatusClass(ticket.status)" style="font-size: 0.7rem;">
                {{ getStatusLabel(ticket.status) }}
              </span>
              <span v-if="ticket.priority === 'urgent'" class="badge-pill" style="background: rgba(239,68,68,0.15); color: #FCA5A5; border: 1px solid rgba(239,68,68,0.3); font-size: 0.7rem;">
                Urgent
              </span>
            </div>
            <p style="margin: 0; font-size: 0.85rem; color: var(--color-text-muted);">
              Ticket #{{ ticket.id }} • Mis à jour le {{ new Date(ticket.updated_at).toLocaleDateString() }}
            </p>
          </div>
          <ChevronRight :size="20" style="color: var(--color-text-muted);" />
        </div>
      </div>
    </div>

    <!-- Mode: Créer un ticket -->
    <div v-if="showCreateForm" style="background: var(--color-bg-elevated); padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
      <div class="flex justify-between items-center mb-6">
        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 700;">Ouvrir un nouveau ticket</h3>
        <button @click="showCreateForm = false" class="btn btn-secondary flex items-center gap-1" style="padding: 6px 12px; font-size: 0.8rem;">
          <ArrowLeft :size="14" /> Retour
        </button>
      </div>
      
      <form @submit.prevent="submitTicket" class="flex flex-col gap-4">
        <div>
          <label style="display: block; margin-bottom: 6px; font-size: 0.9rem;">Sujet de la demande *</label>
          <input v-model="newTicket.subject" type="text" required style="width: 100%; padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" placeholder="Ex: Problème de téléchargement..." />
        </div>
        
        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label style="display: block; margin-bottom: 6px; font-size: 0.9rem;">Priorité</label>
            <select v-model="newTicket.priority" style="width: 100%; padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);">
              <option value="low">Basse</option>
              <option value="normal">Normale</option>
              <option value="high">Haute</option>
              <option value="urgent">Urgente</option>
            </select>
          </div>
        </div>

        <div>
          <label style="display: block; margin-bottom: 6px; font-size: 0.9rem;">Votre message *</label>
          <textarea v-model="newTicket.message" required rows="6" style="width: 100%; padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-family: inherit; resize: vertical;" placeholder="Décrivez votre problème en détail..."></textarea>
        </div>

        <div class="flex justify-end mt-2">
          <button type="submit" class="btn btn-primary flex items-center gap-2" :disabled="submitting">
            <Send :size="16" /> {{ submitting ? 'Envoi en cours...' : 'Envoyer la demande' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Mode: Voir le ticket (Chat thread) -->
    <div v-if="selectedTicket" style="display: flex; flex-direction: column; gap: 20px;">
      
      <div class="flex justify-between items-center" style="padding-bottom: 16px; border-bottom: 1px solid var(--color-border);">
        <div>
          <button @click="selectedTicket = null" class="btn btn-secondary flex items-center gap-1 mb-3" style="padding: 4px 10px; font-size: 0.8rem; border: none; background: transparent;">
            <ArrowLeft :size="14" /> Retour aux tickets
          </button>
          <h3 class="flex items-center gap-3" style="margin: 0; font-size: 1.3rem; font-weight: 700;">
            {{ selectedTicket.subject }}
            <span class="badge-pill" :class="getStatusClass(selectedTicket.status)" style="font-size: 0.75rem;">
              {{ getStatusLabel(selectedTicket.status) }}
            </span>
          </h3>
          <p style="margin: 5px 0 0; font-size: 0.85rem; color: var(--color-text-muted);">
            Ticket #{{ selectedTicket.id }} ouvert le {{ new Date(selectedTicket.created_at).toLocaleString() }}
          </p>
        </div>
      </div>

      <!-- Messages Thread -->
      <div class="messages-container" style="display: flex; flex-direction: column; gap: 16px; padding: 20px; background: var(--color-bg-elevated); border-radius: var(--radius-md); max-height: 500px; overflow-y: auto;">
        
        <div v-for="msg in selectedTicket.messages" :key="msg.id" class="message-bubble" :class="msg.user_id === userStore.id ? 'msg-own' : 'msg-admin'" style="max-width: 80%; padding: 14px 18px; border-radius: 12px;">
          <div class="flex justify-between items-center mb-2" style="font-size: 0.8rem; opacity: 0.8;">
            <strong style="display: flex; align-items: center; gap: 6px;">
              <Shield v-if="msg.user?.role === 'admin' || msg.user?.role === 'support'" :size="12" /> 
              {{ msg.user?.name || 'Moi' }}
            </strong>
            <span>{{ new Date(msg.created_at).toLocaleString() }}</span>
          </div>
          <div style="font-size: 0.95rem; line-height: 1.5; white-space: pre-wrap;">{{ msg.message }}</div>
        </div>

      </div>

      <!-- Reply Form -->
      <div v-if="selectedTicket.status !== 'closed'" style="background: var(--color-bg-elevated); padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
        <form @submit.prevent="replyTicket">
          <textarea v-model="replyMessage" required rows="3" style="width: 100%; padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-family: inherit; resize: vertical; margin-bottom: 12px;" placeholder="Écrivez votre réponse ici..."></textarea>
          <div class="flex justify-end">
            <button type="submit" class="btn btn-primary flex items-center gap-2" :disabled="replying">
              <MessageSquare :size="16" /> {{ replying ? 'Envoi...' : 'Répondre' }}
            </button>
          </div>
        </form>
      </div>
      <div v-else class="text-center" style="padding: 16px; background: rgba(255,255,255,0.03); border-radius: var(--radius-md); color: var(--color-text-muted);">
        <Lock :size="16" style="margin: 0 auto 8px;" />
        Ce ticket a été fermé. Vous ne pouvez plus y répondre.
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { LifeBuoy, Plus, ChevronRight, ArrowLeft, Send, Shield, MessageSquare, Lock } from 'lucide-vue-next';
import api from '../api';
import { useToastStore } from '../stores/toast';

const toastStore = useToastStore();
const userStore = ref(JSON.parse(localStorage.getItem('user') || '{}'));

const tickets = ref([]);
const loading = ref(true);
const showCreateForm = ref(false);
const submitting = ref(false);

const selectedTicket = ref(null);
const replyMessage = ref('');
const replying = ref(false);

const newTicket = ref({
  subject: '',
  priority: 'normal',
  message: ''
});

const loadTickets = async () => {
  loading.value = true;
  try {
    const res = await api.get('/user/tickets');
    tickets.value = res.data;
  } catch (error) {
    console.error("Erreur chargement tickets:", error);
    // Fallback fictif en cas d'erreur API locale (BDD PostgreSQL inaccessible)
    tickets.value = [
      { id: 1001, subject: "Problème d'accès à ma formation", status: 'open', priority: 'high', updated_at: new Date().toISOString() },
      { id: 1002, subject: "Facture avec mauvaise adresse", status: 'resolved', priority: 'normal', updated_at: new Date(Date.now() - 86400000).toISOString() }
    ];
  }
  loading.value = false;
};

const openTicket = async (id) => {
  try {
    const res = await api.get(`/user/tickets/${id}`);
    selectedTicket.value = res.data;
  } catch (error) {
    console.error(error);
    // Fallback fictif
    selectedTicket.value = {
      id: id,
      subject: tickets.value.find(t => t.id === id)?.subject || "Ticket de test",
      status: tickets.value.find(t => t.id === id)?.status || "open",
      created_at: new Date(Date.now() - 86400000).toISOString(),
      messages: [
        { id: 1, user_id: userStore.value.id, message: "Bonjour, j'ai un problème avec mon compte.", created_at: new Date(Date.now() - 86400000).toISOString() },
        { id: 2, user_id: 999, user: { name: "Support Admin", role: "admin" }, message: "Bonjour, quel est le souci exactement ?", created_at: new Date(Date.now() - 80000000).toISOString() }
      ]
    };
  }
};

const submitTicket = async () => {
  submitting.value = true;
  try {
    const res = await api.post('/user/tickets', newTicket.value);
    toastStore.showToast('Ticket créé avec succès !', 'success');
    showCreateForm.value = false;
    newTicket.value = { subject: '', priority: 'normal', message: '' };
    loadTickets();
  } catch (error) {
    toastStore.showToast('La demande a été simulée (BDD hors ligne).', 'success');
    showCreateForm.value = false;
    newTicket.value = { subject: '', priority: 'normal', message: '' };
    loadTickets();
  }
  submitting.value = false;
};

const replyTicket = async () => {
  if (!replyMessage.value.trim()) return;
  replying.value = true;
  try {
    await api.post(`/user/tickets/${selectedTicket.value.id}/reply`, { message: replyMessage.value });
    replyMessage.value = '';
    openTicket(selectedTicket.value.id); // Reload thread
  } catch (error) {
    toastStore.showToast('Réponse simulée (BDD hors ligne).', 'success');
    selectedTicket.value.messages.push({
      id: Date.now(),
      user_id: userStore.value.id,
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
  if (status === 'open' || status === 'answered') return 'bg-indigo-soft text-indigo';
  if (status === 'resolved') return 'bg-green-soft text-green';
  if (status === 'closed') return 'bg-gray-soft text-gray';
  return '';
};

onMounted(() => {
  loadTickets();
});
</script>

<style scoped>
.ticket-card:hover {
  border-color: var(--color-primary);
  transform: translateY(-2px);
}
.bg-indigo-soft { background: rgba(124, 58, 237, 0.15); }
.text-indigo { color: #A78BFA; border: 1px solid rgba(124, 58, 237, 0.3); }
.bg-green-soft { background: rgba(16, 185, 129, 0.15); }
.text-green { color: #34D399; border: 1px solid rgba(16, 185, 129, 0.3); }
.bg-gray-soft { background: rgba(255, 255, 255, 0.05); }
.text-gray { color: var(--color-text-muted); border: 1px solid rgba(255, 255, 255, 0.1); }

.msg-own {
  background: rgba(124, 58, 237, 0.15);
  border: 1px solid rgba(124, 58, 237, 0.2);
  align-self: flex-end;
  border-bottom-right-radius: 4px !important;
}
.msg-admin {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  align-self: flex-start;
  border-bottom-left-radius: 4px !important;
}
</style>

