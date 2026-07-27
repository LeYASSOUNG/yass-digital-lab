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
              <h4 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: #FFF;">Support Yass Digital Lab</h4>
              <p style="font-size: 0.75rem; color: rgba(255,255,255,0.7); margin: 0;">En ligne • Réponse rapide</p>
            </div>
          </div>
          <button @click="isOpen = false" class="chat-close-btn">✕</button>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" ref="messagesContainer">
          <div 
            v-for="(msg, idx) in messages" 
            :key="idx" 
            class="chat-bubble"
            :class="msg.sender === 'user' ? 'user-msg' : 'support-msg'"
          >
            <p style="margin: 0; font-size: 0.88rem; line-height: 1.4;">{{ msg.text }}</p>
            <span class="msg-time">{{ msg.time }}</span>
          </div>
        </div>

        <!-- Input Area -->
        <form @submit.prevent="sendMessage" class="chat-input-form flex gap-2">
          <input 
            v-model="inputMsg" 
            type="text" 
            placeholder="Posez votre question ici..." 
            class="chat-input"
          />
          <button type="submit" class="btn btn-primary chat-send-btn">
            ➔
          </button>
        </form>
      </div>
    </Transition>

    <!-- Trigger Button -->
    <button @click="isOpen = !isOpen" class="chat-trigger-btn glass" :class="{ 'unread': hasUnread }">
      <span v-if="!isOpen" style="font-size: 1.5rem;">💬</span>
      <span v-else style="font-size: 1.3rem;">✕</span>
    </button>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';

const isOpen = ref(false);
const hasUnread = ref(true);
const inputMsg = ref('');
const messagesContainer = ref(null);

const messages = ref([
  { 
    sender: 'support', 
    text: 'Bonjour ! 👋 Bienvenue sur Yass Digital Lab. Comment puis-je vous aider aujourd\'hui ?', 
    time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) 
  }
]);

const sendMessage = async () => {
  if (!inputMsg.value.trim()) return;
  const userText = inputMsg.value.trim();
  const now = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

  messages.value.push({ sender: 'user', text: userText, time: now });
  inputMsg.value = '';

  await scrollToBottom();

  // Simple automated reply
  setTimeout(async () => {
    messages.value.push({
      sender: 'support',
      text: 'Merci pour votre message ! Un membre de notre équipe Support va vous répondre sous peu. Vous pouvez également nous contacter directement via la page Contact.',
      time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
    });
    await scrollToBottom();
  }, 1200);
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
  width: 58px;
  height: 58px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary), #1e3a5f);
  border: 2px solid var(--color-accent);
  color: #FFF;
  cursor: pointer;
  box-shadow: 0 10px 30px rgba(212, 175, 55, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.chat-trigger-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 14px 40px rgba(212, 175, 55, 0.5);
}

.chat-window {
  position: absolute;
  bottom: 74px;
  right: 0;
  width: 350px;
  height: 440px;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
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
  font-size: 1.1rem;
  cursor: pointer;
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
  max-width: 82%;
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
  opacity: 0.7;
  text-align: right;
}

.chat-input-form {
  padding: 12px;
  background: var(--color-bg-card);
  border-top: 1px solid var(--color-border);
}

.chat-input {
  flex: 1;
  padding: 8px 14px;
  border-radius: 999px;
  border: 1px solid var(--color-border);
  background: var(--color-bg);
  color: var(--color-text);
  font-size: 0.88rem;
}

.chat-input:focus {
  outline: none;
  border-color: var(--color-accent);
}

.chat-send-btn {
  border-radius: 50%;
  width: 36px;
  height: 36px;
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
