<template>
  <div class="chat-widget-wrapper">
    <!-- Chat Window -->
    <Transition name="chat-fade">
      <div v-if="isOpen" class="chat-window glass">
        <!-- Header -->
        <div class="chat-header flex justify-between items-center">
          <div class="flex items-center gap-2">
            <span class="status-dot"></span>
            <div>
              <h4 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: #FFF; display: flex; align-items: center; gap: 6px;">
                <Bot :size="16" style="color: var(--color-accent);" /> Assistant IA Yass Digital Lab
              </h4>
              <p style="font-size: 0.75rem; color: rgba(255,255,255,0.75); margin: 0;">En ligne 24/7 • Réponse instantanée</p>
            </div>
          </div>
          <div class="flex items-center gap-1">
            <button @click="clearMessages" class="chat-close-btn" title="Effacer la conversation">
              <RotateCcw :size="15" />
            </button>
            <button @click="isOpen = false" class="chat-close-btn" title="Fermer">
              <X :size="18" />
            </button>
          </div>
        </div>

        <!-- Quick Topic Chips -->
        <div class="quick-chips flex gap-1 p-2" style="background: rgba(0,0,0,0.06); overflow-x: auto; border-bottom: 1px solid var(--color-border); white-space: nowrap;">
          <button 
            v-for="(chip, idx) in quickChips" 
            :key="idx" 
            @click="sendQuickChip(chip)" 
            class="chip-btn"
          >
            {{ chip.label }}
          </button>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" ref="messagesContainer">
          <div 
            v-for="(msg, idx) in messages" 
            :key="idx" 
            class="chat-bubble"
            :class="msg.sender === 'user' ? 'user-msg' : 'support-msg'"
          >
            <p style="margin: 0; font-size: 0.88rem; line-height: 1.5; font-family: var(--font-body);" v-html="msg.text"></p>
            <span class="msg-time">{{ msg.time }}</span>
          </div>

          <!-- Typing Indicator -->
          <div v-if="isTyping" class="chat-bubble support-msg flex items-center gap-2" style="width: max-content; padding: 8px 14px;">
            <Sparkles :size="14" style="color: var(--color-accent);" class="pulse" />
            <span style="font-size: 0.8rem; font-style: italic; color: var(--color-text-light);">L'assistant rédigé une réponse...</span>
          </div>
        </div>

        <!-- Input Form -->
        <form @submit.prevent="sendMessage" class="chat-input-form flex gap-2">
          <input 
            v-model="inputMsg" 
            type="text" 
            placeholder="Posez votre question ici..." 
            class="chat-input"
          />
          <button type="submit" class="btn btn-primary chat-send-btn" title="Envoyer">
            <Send :size="16" />
          </button>
        </form>
      </div>
    </Transition>

    <!-- Trigger Floating Button -->
    <button @click="toggleChat" class="chat-trigger-btn glass" :class="{ 'unread': hasUnread }">
      <div v-if="hasUnread && !isOpen" class="unread-badge">1</div>
      <MessageSquare v-if="!isOpen" :size="24" />
      <X v-else :size="22" />
    </button>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { MessageSquare, X, Send, Bot, Sparkles, RotateCcw } from 'lucide-vue-next';

const isOpen = ref(false);
const hasUnread = ref(true);
const inputMsg = ref('');
const isTyping = ref(false);
const messagesContainer = ref(null);

const quickChips = ref([
  { label: '📦 Nos Produits', query: 'Quels produits proposez-vous ?' },
  { label: '💼 Demander un devis', query: 'Je souhaite un devis sur mesure' },
  { label: '⚡ Accès téléchargements', query: 'Comment accéder à mes téléchargements ?' },
  { label: '💬 Parler à l\'équipe', query: 'Comment contacter le créateur ?' }
]);

const initialMessage = { 
  sender: 'support', 
  text: 'Bonjour ! 👋 Je suis l\'Assistant IA de <strong>Yass Digital Lab</strong>. Comment puis-je vous aider aujourd\'hui ?', 
  time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) 
};

const messages = ref([initialMessage]);

const toggleChat = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    hasUnread.value = false;
    scrollToBottom();
  }
};

const clearMessages = () => {
  messages.value = [{
    sender: 'support',
    text: 'Conversation réinitialisée. N\'hésitez pas si vous avez d\'autres questions ! 😊',
    time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
  }];
};

const sendQuickChip = (chip) => {
  inputMsg.value = chip.query;
  sendMessage();
};

const sendMessage = async () => {
  if (!inputMsg.value.trim()) return;
  const userText = inputMsg.value.trim();
  const now = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

  messages.value.push({ sender: 'user', text: userText, time: now });
  inputMsg.value = '';
  await scrollToBottom();

  isTyping.value = true;

  setTimeout(async () => {
    isTyping.value = false;
    const responseText = generateSmartReply(userText);
    messages.value.push({
      sender: 'support',
      text: responseText,
      time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
    });
    await scrollToBottom();
  }, 1000);
};

const generateSmartReply = (query) => {
  const q = query.toLowerCase();
  
  if (q.includes('produit') || q.includes('pack') || q.includes('template') || q.includes('achat')) {
    return 'Retrouvez tous nos templates SaaS Vue 3 + Laravel, nos packs de Prompts ChatGPT/Claude et nos guides dans l\'onglet <a href="/products" style="color: var(--color-accent); font-weight: 700; text-decoration: underline;">Produits</a> !';
  }
  
  if (q.includes('devis') || q.includes('service') || q.includes('projet') || q.includes('sur mesure')) {
    return 'Nous réalisons des applications web full-stack et des agents IA sur-mesure ! Vous pouvez soumettre votre cahier des charges sur la page <a href="/services" style="color: var(--color-accent); font-weight: 700; text-decoration: underline;">Services</a>.';
  }

  if (q.includes('téléchargement') || q.includes('fichier') || q.includes('accès') || q.includes('commande')) {
    return 'Dès la validation de votre achat, vos fichiers sont téléchargeables immédiatement depuis votre <a href="/dashboard" style="color: var(--color-accent); font-weight: 700; text-decoration: underline;">Espace Client</a>.';
  }

  if (q.includes('créateur') || q.includes('yass') || q.includes('contact') || q.includes('équipe')) {
    return 'Vous pouvez échanger directement avec <strong>Diarrassouba Yassoungo Youssouf</strong> (Fondateur) via la page <a href="/contact" style="color: var(--color-accent); font-weight: 700; text-decoration: underline;">Contact</a> ou consulter son <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" style="color: var(--color-accent); font-weight: 700; text-decoration: underline;">Portfolio Officiel</a>.';
  }

  return 'Merci pour votre message ! Un membre de l\'équipe technique a été notifié. En attendant, découvrez nos <a href="/products" style="color: var(--color-accent); font-weight: 700; text-decoration: underline;">Nouveautés</a>.';
};

const scrollToBottom = async () => {
  await nextTick();
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
};
</script>

<style scoped>
.chat-widget-wrapper {
  position: fixed;
  bottom: 28px;
  right: 28px;
  z-index: 9990;
}

.chat-trigger-btn {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary), #1e3a5f);
  border: 2px solid var(--color-accent);
  color: #FFF;
  cursor: pointer;
  box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  position: relative;
}

.chat-trigger-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 14px 40px rgba(212, 175, 55, 0.6);
}

.unread-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: #ef4444;
  color: #FFF;
  font-size: 0.72rem;
  font-weight: 800;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid var(--color-bg);
}

.chat-window {
  position: absolute;
  bottom: 74px;
  right: 0;
  width: 370px;
  height: 480px;
  border-radius: 24px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.45);
  border: 1px solid var(--color-border);
}

.chat-header {
  background: linear-gradient(90deg, #0F172A, #1e293b);
  padding: 14px 18px;
  border-bottom: 1px solid var(--color-border);
}

.status-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #10b981;
  box-shadow: 0 0 10px #10b981;
}

.chat-close-btn {
  background: none;
  border: none;
  color: rgba(255,255,255,0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
  transition: background 0.2s, color 0.2s;
}

.chat-close-btn:hover {
  background: rgba(255,255,255,0.15);
  color: #FFF;
}

.chip-btn {
  background: rgba(212,175,55,0.12);
  border: 1px solid rgba(212,175,55,0.3);
  color: var(--color-accent);
  border-radius: 999px;
  padding: 4px 10px;
  font-size: 0.76rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.chip-btn:hover {
  background: var(--color-accent);
  color: #050811;
}

.chat-messages {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 12px;
  background: var(--color-bg);
}

.chat-bubble {
  max-width: 84%;
  padding: 10px 14px;
  border-radius: 14px;
  position: relative;
}

.user-msg {
  align-self: flex-end;
  background: var(--color-accent);
  color: #050811;
  font-weight: 600;
  border-bottom-right-radius: 2px;
}

.support-msg {
  align-self: flex-start;
  background: rgba(255,255,255,0.08);
  color: var(--color-text);
  border: 1px solid var(--color-border);
  border-bottom-left-radius: 2px;
}

.msg-time {
  display: block;
  font-size: 0.65rem;
  margin-top: 4px;
  opacity: 0.75;
  text-align: right;
}

.chat-input-form {
  padding: 12px 14px;
  background: var(--color-bg-card);
  border-top: 1px solid var(--color-border);
}

.chat-input {
  flex: 1;
  padding: 9px 16px;
  border-radius: 999px;
  border: 1px solid var(--color-border);
  background: var(--color-bg);
  color: var(--color-text);
  font-size: 0.9rem;
}

.chat-input:focus {
  outline: none;
  border-color: var(--color-accent);
}

.chat-send-btn {
  border-radius: 50%;
  width: 38px;
  height: 38px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Animations */
.chat-fade-enter-active,
.chat-fade-leave-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.chat-fade-enter-from,
.chat-fade-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}
</style>
