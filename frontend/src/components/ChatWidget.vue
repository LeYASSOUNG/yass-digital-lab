<template>
  <div class="chat-widget-root">
    
    <!-- Chat Window Modal / Floating Box -->
    <Transition name="chat-spring">
      <div 
        v-if="isOpen" 
        class="chat-window-box glass-panel"
        :class="{ 'expanded-mode': isExpanded }"
      >
        
        <!-- Header Bar -->
        <header class="chat-header-bar flex justify-between items-center">
          <div class="header-ai-identity flex items-center gap-3">
            <div class="ai-avatar-halo">
              <Bot :size="18" class="text-gold" />
              <span class="ai-online-ping"></span>
            </div>
            <div>
              <h4 class="ai-name">
                Assistant IA <span class="ai-badge">Yass Lab</span>
              </h4>
              <p class="ai-status-text">
                <Sparkles :size="11" class="text-emerald" /> Modèle Contextuel v2.4 • En direct
              </p>
            </div>
          </div>

          <!-- Header Actions -->
          <div class="header-controls flex items-center gap-1">
            <button 
              @click="isExpanded = !isExpanded" 
              class="hdr-btn" 
              :title="isExpanded ? 'Réduire la fenêtre' : 'Agrandir la fenêtre'"
            >
              <Minimize2 v-if="isExpanded" :size="15" />
              <Maximize2 v-else :size="15" />
            </button>
            <button 
              @click="clearMessages" 
              class="hdr-btn" 
              title="Réinitialiser la discussion"
            >
              <RotateCcw :size="15" />
            </button>
            <button 
              @click="isOpen = false" 
              class="hdr-btn close-btn" 
              title="Fermer"
            >
              <X :size="18" />
            </button>
          </div>
        </header>

        <!-- Topic Suggestion Header Chips -->
        <div class="quick-suggestion-bar">
          <button 
            v-for="(chip, cIdx) in quickChips" 
            :key="cIdx" 
            @click="sendQuickPrompt(chip.query)" 
            class="suggestion-chip"
          >
            <component :is="chip.icon" :size="12" />
            <span>{{ chip.label }}</span>
          </button>
        </div>

        <!-- Messages Flow Container -->
        <div class="chat-messages-flow" ref="messagesContainer">
          
          <div 
            v-for="(msg, mIdx) in messages" 
            :key="mIdx" 
            :class="['message-row', msg.sender === 'user' ? 'user-row' : 'ai-row']"
          >
            <!-- AI Avatar for bot messages -->
            <div v-if="msg.sender === 'ai'" class="msg-avatar-icon">
              <Bot :size="15" />
            </div>

            <div class="message-bubble">
              <!-- Message Text (Markdown-like HTML) -->
              <div class="bubble-text" v-html="formatMessageText(msg.text)"></div>

              <!-- Embedded Quote Card (If AI matched a quote) -->
              <div v-if="msg.quote" class="embedded-quote-card">
                <div class="quote-card-top flex justify-between items-center">
                  <span class="eq-ref">#{{ msg.quote.ref }}</span>
                  <span :class="['eq-badge', getStatusBadgeClass(msg.quote.status)]">
                    {{ msg.quote.status_label }}
                  </span>
                </div>
                <div class="eq-service">{{ msg.quote.service_title }}</div>
                <div class="eq-metrics grid grid-cols-2 gap-2 mt-2">
                  <div class="eq-metric-item">
                    <span class="eq-lbl">Montant :</span>
                    <strong class="text-gold">{{ formatQuoteAmount(msg.quote.amount) }}</strong>
                  </div>
                  <div class="eq-metric-item">
                    <span class="eq-lbl">Délai :</span>
                    <span>{{ msg.quote.deadline || '15-30j' }}</span>
                  </div>
                </div>
                <div class="eq-actions flex gap-2 mt-2.5">
                  <router-link :to="`/suivi-devis?ref=${msg.quote.id}`" class="eq-btn-primary flex items-center justify-center gap-1">
                    <Search :size="13" /> Suivre en direct
                  </router-link>
                  <a :href="`https://wa.me/22549679002?text=${encodeURIComponent('Bonjour Yass Digital Lab, je souhaite un suivi pour mon devis #' + msg.quote.ref)}`" target="_blank" class="eq-btn-wa">
                    <MessageSquare :size="13" />
                  </a>
                </div>
              </div>

              <!-- Embedded Products Recommendations -->
              <div v-if="msg.products && msg.products.length > 0" class="embedded-products-list">
                <div 
                  v-for="prod in msg.products" 
                  :key="prod.id"
                  class="product-mini-card"
                >
                  <div class="prod-info-col">
                    <span class="prod-type-pill">{{ prod.type || 'SaaS' }}</span>
                    <h5 class="prod-title">{{ prod.title }}</h5>
                    <span class="prod-price">{{ currency.format(prod.price) }}</span>
                  </div>
                  <div class="prod-actions-col">
                    <router-link :to="`/products/${prod.id}`" class="prod-btn-view" title="Voir les détails">
                      <ExternalLink :size="13" />
                    </router-link>
                    <button @click="addToCart(prod)" class="prod-btn-cart" title="Ajouter au panier">
                      <ShoppingCart :size="13" />
                    </button>
                  </div>
                </div>
              </div>

              <!-- Embedded Services Recommendations -->
              <div v-if="msg.services && msg.services.length > 0" class="embedded-services-list">
                <div 
                  v-for="serv in msg.services" 
                  :key="serv.id"
                  class="service-mini-card"
                >
                  <div class="serv-info-col">
                    <h5 class="serv-title">{{ serv.title }}</h5>
                    <span class="serv-starting">À partir de <strong class="text-gold">{{ currency.format(serv.starting_price) }}</strong></span>
                  </div>
                  <router-link to="/services" class="serv-btn-quote">
                    <Plus :size="13" /> Devis
                  </router-link>
                </div>
              </div>

              <!-- Contextual Quick Action Buttons -->
              <div v-if="msg.quick_actions && msg.quick_actions.length > 0" class="msg-action-pills">
                <template v-for="(act, aIdx) in msg.quick_actions" :key="aIdx">
                  <a 
                    v-if="act.url.startsWith('http')" 
                    :href="act.url" 
                    target="_blank" 
                    class="action-pill-link"
                  >
                    {{ act.label }}
                  </a>
                  <router-link 
                    v-else 
                    :to="act.url" 
                    class="action-pill-link"
                    @click="handleActionClick"
                  >
                    {{ act.label }}
                  </router-link>
                </template>
              </div>

              <!-- Timestamp -->
              <span class="bubble-timestamp">{{ msg.time }}</span>
            </div>
          </div>

          <!-- Typing Indicator -->
          <div v-if="isTyping" class="message-row ai-row">
            <div class="msg-avatar-icon"><Bot :size="15" /></div>
            <div class="message-bubble typing-bubble flex items-center gap-2">
              <span class="typing-dot"></span>
              <span class="typing-dot"></span>
              <span class="typing-dot"></span>
              <span class="typing-text">L'Assistant rédige une réponse...</span>
            </div>
          </div>

        </div>

        <!-- Input Control Area -->
        <footer class="chat-input-footer">
          <form @submit.prevent="sendMessage" class="input-form-wrapper">
            
            <!-- Voice Dictation Button (Web Speech API) -->
            <button 
              v-if="isSpeechSupported"
              type="button" 
              class="btn-voice-mic" 
              :class="{ 'recording': isListening }"
              :title="isListening ? 'Arrêter la dictée' : 'Dicter à la voix'"
              @click="toggleVoiceRecognition"
            >
              <Mic :size="16" />
            </button>

            <!-- Text Input -->
            <input 
              v-model="inputMsg" 
              type="text" 
              placeholder="Posez une question ou entrez #DEV-XXXXXX..." 
              class="chat-text-field"
              :disabled="isTyping"
              ref="textInputRef"
            />

            <!-- Send Button -->
            <button 
              type="submit" 
              class="chat-send-action" 
              :disabled="!inputMsg.trim() || isTyping"
              title="Envoyer"
            >
              <Send :size="16" />
            </button>
          </form>

          <!-- WhatsApp Escalation Bar -->
          <div class="flex justify-between items-center px-3 py-1.5" style="border-top: 1px solid rgba(255,255,255,0.06); background: rgba(0,0,0,0.25); font-size: 0.74rem;">
            <span style="color: #94A3B8;">Besoin d'un accompagnement direct ?</span>
            <a 
              :href="'https://api.whatsapp.com/send?phone=22549679002&text=' + encodeURIComponent('Bonjour Yass Digital Lab, je souhaite un devis personnalisé pour mon projet.')" 
              target="_blank" 
              class="flex items-center gap-1" 
              style="color: #25D366; text-decoration: none; font-weight: 800;"
            >
              <MessageSquare :size="12" /> WhatsApp Direct →
            </a>
          </div>

          <div class="chat-footer-branding flex justify-between items-center">
            <span>IA Sécurisée Yass Digital Lab</span>
            <span class="flex items-center gap-1"><ShieldCheck :size="12" class="text-emerald" /> Chiffrement SSL 256-bit</span>
          </div>
        </footer>

      </div>
    </Transition>

    <!-- Floating Trigger Button -->
    <button 
      @click="toggleChat" 
      class="chat-floating-trigger" 
      :class="{ 'has-alert': hasUnread && !isOpen }"
      aria-label="Ouvrir le chat avec l'Assistant IA"
    >
      <div v-if="hasUnread && !isOpen" class="trigger-badge-ping">1</div>
      <div class="trigger-icon-inner">
        <MessageSquare v-if="!isOpen" :size="25" />
        <X v-else :size="22" />
      </div>
      <span class="trigger-glow"></span>
    </button>

  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import {
  MessageSquare,
  X,
  Send,
  Bot,
  Sparkles,
  RotateCcw,
  ShoppingCart,
  ExternalLink,
  Search,
  Maximize2,
  Minimize2,
  Mic,
  ShieldCheck,
  Code2,
  Layers,
  FileCode2,
  Plus
} from 'lucide-vue-next'
import { useCartStore } from '../stores/cart'
import { useCurrencyStore } from '../stores/currency'
import { useToastStore } from '../stores/toast'
import api from '../api'

const cart = useCartStore()
const currency = useCurrencyStore()
const toast = useToastStore()

const isOpen = ref(false)
const isExpanded = ref(false)
const hasUnread = ref(true)
const inputMsg = ref('')
const isTyping = ref(false)
const messagesContainer = ref(null)
const textInputRef = ref(null)

// Voice recognition state
const isListening = ref(false)
const isSpeechSupported = ref(typeof window !== 'undefined' && ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window))
let recognition = null

const quickChips = ref([
  { label: 'Templates SaaS', query: 'Quels templates SaaS Vue 3 & Laravel proposez-vous ?', icon: Layers },
  { label: 'Packs IA & Prompts', query: 'Quels sont vos meilleurs packs de prompts ChatGPT & Claude ?', icon: Bot },
  { label: 'Suivre mon Devis', query: 'Comment suivre l\'avancement de mon devis ?', icon: Search },
  { label: 'Prestations Sur-Mesure', query: 'Quels services de développement web sur-mesure réalisez-vous ?', icon: Code2 },
])

const initialMessage = {
  sender: 'ai',
  text: "Bonjour ! Je suis l'**Assistant IA de Yass Digital Lab** ðŸšFCFA.\n\nJe peux vous orienter parmi nos **templates SaaS & packs IA**, vous conseiller sur une architecture ou **consulter l'état de votre devis** en temps réel (tapez simplement votre référence ex: `#DEV-000001`).\n\nComment puis-je vous aider aujourd'hui ?",
  time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
  products: [],
  services: [],
  quote: null,
  quick_actions: [
    { label: '📦 Voir le Catalogue', url: '/products' },
    { label: 'ðŸšFCFA Demander un Devis', url: '/services' },
    { label: 'ðŸ” Suivre un Devis', url: '/suivi-devis' },
  ]
}

const messages = ref([initialMessage])

const toggleChat = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    hasUnread.value = false
    scrollToBottom()
    nextTick(() => {
      if (textInputRef.value) textInputRef.value.focus()
    })
  }
}

const clearMessages = () => {
  messages.value = [{
    sender: 'ai',
    text: "Discussion réinitialisée ✨. Posez votre question ou entrez votre numéro de dossier pour commencer !",
    time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
    products: [],
    services: [],
    quote: null,
    quick_actions: []
  }]
  toast.showToast('Conversation réinitialisée', 'info')
}

const sendQuickPrompt = (query) => {
  inputMsg.value = query
  sendMessage()
}

const handleActionClick = () => {
  // Can optionally close or keep chat open
}

const addToCart = (product) => {
  cart.addToCart(product)
  toast.showToast(`"${product.title}" ajouté au panier 🛒`, 'success')
}

const formatQuoteAmount = (val) => {
  if (!val) return 'Sur Devis'
  const str = String(val).replace(/[^\d]/g, '')
  if (str) return currency.format(parseInt(str))
  return val
}

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'accepted':
    case 'completed': return 'badge-success'
    case 'in_progress': return 'badge-indigo'
    case 'contacted': return 'badge-blue'
    default: return 'badge-gold'
  }
}

const formatMessageText = (text) => {
  if (!text) return ''
  // Basic markdown bold conversion **text** -> <strong>text</strong>
  let formatted = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
  // Newlines to <br>
  formatted = formatted.replace(/\n/g, '<br>')
  return formatted
}

const sendMessage = async () => {
  if (!inputMsg.value.trim()) return
  const userText = inputMsg.value.trim()
  const now = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })

  messages.value.push({ sender: 'user', text: userText, time: now })
  inputMsg.value = ''
  await scrollToBottom()

  isTyping.value = true

  try {
    const res = await api.post('/ai/recommend', { message: userText })
    isTyping.value = false

    const responseData = res.data || {}
    messages.value.push({
      sender: 'ai',
      text: responseData.reply || "Merci pour votre message ! Je reste à votre entière disposition.",
      time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
      products: responseData.recommended_products || [],
      services: responseData.recommended_services || [],
      quote: responseData.quote || null,
      quick_actions: responseData.quick_actions || []
    })
  } catch (err) {
    isTyping.value = false
    messages.value.push({
      sender: 'ai',
      text: "Désolé, une micro-coupure réseau s'est produite. Vous pouvez consulter nos <a href='/products' style='color:#F5C027;font-weight:700;'>Produits</a> ou remplir une demande de <a href='/services' style='color:#F5C027;font-weight:700;'>Devis</a>.",
      time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
      products: [],
      services: [],
      quote: null,
      quick_actions: []
    })
  }

  await scrollToBottom()
}

const toggleVoiceRecognition = () => {
  if (!isSpeechSupported.value) return

  if (isListening.value) {
    if (recognition) recognition.stop()
    isListening.value = false
    return
  }

  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
  recognition = new SpeechRecognition()
  recognition.lang = 'fr-FR'
  recognition.continuous = false
  recognition.interimResults = false

  recognition.onstart = () => {
    isListening.value = true
    toast.showToast('Dictée vocale active... Parlez maintenant 🎙ï¸', 'info')
  }

  recognition.onresult = (event) => {
    const transcript = event.results[0][0].transcript
    inputMsg.value = transcript
    isListening.value = false
    sendMessage()
  }

  recognition.onerror = () => {
    isListening.value = false
    toast.showToast('Microphone non détecté ou accès refusé.', 'error')
  }

  recognition.onend = () => {
    isListening.value = false
  }

  recognition.start()
}

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

onMounted(() => {
  // Listen to custom global events (e.g. open chat from other components)
  window.addEventListener('open-ai-chat', (e) => {
    isOpen.value = true
    if (e.detail && e.detail.query) {
      sendQuickPrompt(e.detail.query)
    }
  })
})
</script>

<style scoped>
.chat-widget-root {
  position: fixed;
  bottom: 26px;
  right: 26px;
  z-index: 9995;
}

/* â”FCFAâ”FCFA Floating Trigger Button â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.chat-floating-trigger {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366F1 0%, #4F46E5 60%, #F5C027 120%);
  border: 1.5px solid rgba(255, 255, 255, 0.25);
  color: #FFFFFF;
  cursor: pointer;
  box-shadow: 0 10px 30px rgba(99, 102, 241, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.chat-floating-trigger:hover {
  transform: scale(1.08) translateY(-2px);
  box-shadow: 0 16px 40px rgba(99, 102, 241, 0.65);
}
.trigger-badge-ping {
  position: absolute;
  top: -4px;
  right: -4px;
  background: #EF4444;
  color: #FFFFFF;
  font-size: 11px;
  font-weight: 900;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2.5px solid #0B0F19;
  box-shadow: 0 0 12px #EF4444;
}

/* â”FCFAâ”FCFA Main Chat Window â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.chat-window-box {
  position: absolute;
  bottom: 76px;
  right: 0;
  width: 390px;
  height: 540px;
  border-radius: 24px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 25px 70px rgba(0, 0, 0, 0.6), 0 0 35px rgba(99, 102, 241, 0.2);
  border: 1px solid rgba(245, 192, 39, 0.25);
  background: rgba(15, 23, 42, 0.92);
  backdrop-filter: blur(30px);
  -webkit-backdrop-filter: blur(30px);
  transition: width 0.3s ease, height 0.3s ease;
}

.chat-window-box.expanded-mode {
  width: 520px;
  height: 660px;
}

/* Header */
.chat-header-bar {
  background: linear-gradient(90deg, #0B0F19 0%, #151D32 100%);
  padding: 16px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.ai-avatar-halo {
  width: 36px;
  height: 36px;
  border-radius: 12px;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.3), rgba(245, 192, 39, 0.2));
  border: 1px solid rgba(245, 192, 39, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
.ai-online-ping {
  position: absolute;
  bottom: -2px;
  right: -2px;
  width: 9px;
  height: 9px;
  border-radius: 50%;
  background: #10B981;
  border: 1.5px solid #0B0F19;
  box-shadow: 0 0 8px #10B981;
}
.ai-name {
  font-size: 14.5px;
  font-weight: 800;
  color: #FFFFFF;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 6px;
}
.ai-badge {
  font-size: 10px;
  font-weight: 800;
  background: rgba(245, 192, 39, 0.15);
  color: #F5C027;
  padding: 1px 6px;
  border-radius: 4px;
  border: 1px solid rgba(245, 192, 39, 0.3);
}
.ai-status-text {
  font-size: 11px;
  color: #94A3B8;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 4px;
}

.hdr-btn {
  background: transparent;
  border: none;
  color: #94A3B8;
  padding: 6px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.hdr-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #FFFFFF;
}
.close-btn:hover {
  background: rgba(239, 68, 68, 0.2);
  color: #FCA5A5;
}

/* Quick Suggestion Chips */
.quick-suggestion-bar {
  display: flex;
  gap: 6px;
  padding: 8px 12px;
  background: rgba(0, 0, 0, 0.35);
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  overflow-x: auto;
  white-space: nowrap;
}
.suggestion-chip {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #C7D2FE;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s;
}
.suggestion-chip:hover {
  background: rgba(99, 102, 241, 0.25);
  color: #FFFFFF;
  border-color: rgba(99, 102, 241, 0.45);
}

/* Messages Flow */
.chat-messages-flow {
  flex: 1;
  padding: 18px 16px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 14px;
  background: rgba(11, 15, 25, 0.6);
}

.message-row {
  display: flex;
  gap: 8px;
  max-width: 88%;
}
.user-row {
  align-self: flex-end;
  flex-direction: row-reverse;
}
.ai-row {
  align-self: flex-start;
}
.msg-avatar-icon {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  background: linear-gradient(135deg, #6366F1, #4F46E5);
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;
}

.message-bubble {
  padding: 12px 16px;
  border-radius: 16px;
  font-size: 13.5px;
  line-height: 1.55;
  color: #E2E8F0;
  position: relative;
}
.user-row .message-bubble {
  background: linear-gradient(135deg, #6366F1, #4F46E5);
  color: #FFFFFF;
  border-bottom-right-radius: 4px;
  box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
}
.ai-row .message-bubble {
  background: rgba(30, 41, 59, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.09);
  border-bottom-left-radius: 4px;
}
.bubble-timestamp {
  display: block;
  font-size: 10px;
  color: #64748B;
  margin-top: 6px;
  text-align: right;
}
.user-row .bubble-timestamp {
  color: rgba(255, 255, 255, 0.7);
}

/* Embedded Quote Widget */
.embedded-quote-card {
  background: rgba(15, 23, 42, 0.95);
  border: 1.5px solid rgba(245, 192, 39, 0.4);
  border-radius: 12px;
  padding: 12px;
  margin-top: 10px;
}
.eq-ref { font-weight: 900; color: #F5C027; font-size: 13px; }
.eq-badge {
  font-size: 10.5px;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 999px;
}
.badge-gold { background: rgba(245, 192, 39, 0.15); color: #FDE047; }
.badge-indigo { background: rgba(99, 102, 241, 0.15); color: #A5B4FC; }
.badge-success { background: rgba(16, 185, 129, 0.15); color: #6EE7B7; }
.badge-blue { background: rgba(59, 130, 246, 0.15); color: #93C5FD; }

.eq-service { font-size: 12.5px; font-weight: 700; color: #FFFFFF; margin-top: 4px; }
.eq-lbl { color: #64748B; font-size: 11px; margin-right: 4px; }
.eq-btn-primary {
  flex: 1;
  background: linear-gradient(135deg, #6366F1, #4F46E5);
  color: #FFFFFF;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 11.5px;
  font-weight: 800;
  text-decoration: none;
}
.eq-btn-wa {
  background: rgba(16, 185, 129, 0.15);
  color: #34D399;
  border: 1px solid rgba(16, 185, 129, 0.3);
  padding: 6px 10px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Embedded Products */
.embedded-products-list, .embedded-services-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 10px;
}
.product-mini-card, .service-mini-card {
  background: rgba(15, 23, 42, 0.9);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 10px;
  padding: 8px 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.prod-type-pill {
  font-size: 9.5px;
  font-weight: 800;
  color: #818CF8;
  text-transform: uppercase;
}
.prod-title, .serv-title {
  font-size: 12px;
  font-weight: 700;
  color: #FFFFFF;
  margin: 1px 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 170px;
}
.prod-price {
  font-size: 12px;
  font-weight: 800;
  color: #F5C027;
}
.prod-actions-col { display: flex; gap: 4px; }
.prod-btn-view, .prod-btn-cart, .serv-btn-quote {
  background: rgba(99, 102, 241, 0.15);
  color: #A5B4FC;
  border: 1px solid rgba(99, 102, 241, 0.3);
  padding: 4px 8px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  text-decoration: none;
}
.prod-btn-cart {
  background: linear-gradient(135deg, #6366F1, #4F46E5);
  color: #FFFFFF;
}

/* Quick Action Pills */
.msg-action-pills {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  margin-top: 10px;
}
.action-pill-link {
  background: rgba(245, 192, 39, 0.12);
  border: 1px solid rgba(245, 192, 39, 0.35);
  color: #FDE047;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 800;
  text-decoration: none;
  transition: all 0.2s;
}
.action-pill-link:hover {
  background: rgba(245, 192, 39, 0.25);
  color: #FFFFFF;
}

/* Typing Indicator */
.typing-bubble {
  background: rgba(30, 41, 59, 0.6);
  padding: 8px 14px;
}
.typing-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #F5C027;
  animation: dot-bounce 1.2s infinite;
}
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes dot-bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-4px); }
}
.typing-text { font-size: 11.5px; font-style: italic; color: #94A3B8; }

/* â”FCFAâ”FCFA Footer Input Area â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.chat-input-footer {
  padding: 12px 16px;
  background: #0B0F19;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}
.input-form-wrapper {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.04);
  border: 1.5px solid rgba(255, 255, 255, 0.1);
  border-radius: 999px;
  padding: 4px 6px 4px 12px;
  transition: border-color 0.2s;
}
.input-form-wrapper:focus-within {
  border-color: #F5C027;
  box-shadow: 0 0 0 3px rgba(245, 192, 39, 0.15);
}

.btn-voice-mic {
  background: none;
  border: none;
  color: #94A3B8;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
}
.btn-voice-mic.recording {
  color: #EF4444;
  animation: pulse-mic 1s infinite;
}
@keyframes pulse-mic {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.2); }
}

.chat-text-field {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  color: #FFFFFF;
  font-size: 13.5px;
  padding: 6px 0;
}
.chat-text-field::placeholder { color: #64748B; font-size: 12.5px; }

.chat-send-action {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366F1, #4F46E5);
  border: none;
  color: #FFFFFF;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.chat-send-action:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.chat-send-action:hover:not(:disabled) {
  transform: scale(1.06);
  filter: brightness(1.1);
}

.chat-footer-branding {
  font-size: 10px;
  color: #475569;
  margin-top: 8px;
  padding: 0 4px;
}

/* â”FCFAâ”FCFA Transitions â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.chat-spring-enter-active, .chat-spring-leave-active {
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.chat-spring-enter-from, .chat-spring-leave-to {
  opacity: 0;
  transform: translateY(30px) scale(0.92);
}

.text-gold { color: #F5C027 !important; }
.text-emerald { color: #10B981 !important; }

/* Responsive adjustments */
@media (max-width: 480px) {
  .chat-window-box {
    width: calc(100vw - 32px);
    right: -10px;
    height: 500px;
  }
}
</style>

