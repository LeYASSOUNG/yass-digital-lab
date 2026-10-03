<template>
  <div class="tracking-page">
    
    <!-- Ambient Background FX -->
    <div class="fx-mesh">
      <div class="orb orb-1"></div>
      <div class="orb orb-2"></div>
      <div class="orb orb-3"></div>
      <div class="grid-overlay"></div>
    </div>

    <!-- Hero Header -->
    <section class="tracking-hero">
      <div class="hero-inner">
        
        <!-- Live Status Pill -->
        <div class="live-status-badge slide-down">
          <span class="live-radar">
            <span class="radar-core"></span>
            <span class="radar-wave"></span>
          </span>
          <span class="badge-text">CENTRE DE SUIVI & AUDIT EN DIRECT</span>
          <span class="badge-separator">•</span>
          <span class="badge-tech">SYS-V2.4</span>
        </div>

        <h1 class="hero-headline">
          Suivi Transparent de <br />
          <span class="specular-gradient">Vos Projets & Devis</span>
        </h1>
        
        <p class="hero-description">
          Visualisez l'état d'avancement de votre solution, téléchargez vos livrables officiels, générez votre QR code de synchronisation mobile et échangez en direct avec vos experts.
        </p>

        <!-- Command Search Bar -->
        <div class="command-search-card glass-panel">
          
          <!-- Segmented Toggle -->
          <div class="segmented-control">
            <button
              type="button"
              :class="['segment-btn', { active: searchMode === 'ref' }]"
              @click="searchMode = 'ref'"
            >
              <Hash :size="15" />
              <span>Numéro de Référence</span>
            </button>
            <button
              type="button"
              :class="['segment-btn', { active: searchMode === 'email' }]"
              @click="searchMode = 'email'"
            >
              <Mail :size="15" />
              <span>Adresse Email Client</span>
            </button>
          </div>

          <!-- Search Input Control -->
          <form class="search-input-group" @submit.prevent="searchQuote">
            
            <div v-if="searchMode === 'ref'" class="field-container">
              <div class="field-prefix">
                <span class="prefix-tag">DEV-</span>
              </div>
              <input
                v-model="searchRef"
                type="text"
                placeholder="Ex: 000001 ou 1"
                class="field-input"
                autofocus
                @keyup.enter="searchQuote"
              />
            </div>

            <div v-else class="field-container">
              <div class="field-prefix email-prefix">
                <Mail :size="18" />
              </div>
              <input
                v-model="searchEmail"
                type="email"
                placeholder="Ex: client@yassdigital.lab"
                class="field-input"
                autofocus
                @keyup.enter="searchQuote"
              />
            </div>

            <button
              type="submit"
              class="search-submit-btn"
              :disabled="loading || (searchMode === 'ref' ? !searchRef : !searchEmail)"
            >
              <span v-if="loading" class="btn-spinner"></span>
              <template v-else>
                <Search :size="18" />
                <span>Rechercher le dossier</span>
              </template>
            </button>

            <button
              v-if="searchRef || searchEmail || quotes.length"
              type="button"
              class="reset-btn"
              title="Effacer"
              @click="resetSearch"
            >
              <RotateCcw :size="16" />
            </button>
          </form>

          <!-- Quick Test Chips -->
          <div class="quick-chips-row">
            <span class="chips-title"><Sparkles :size="13" /> Démos en 1 clic :</span>
            <button class="chip-item" @click="quickSearchRef('000001')">
              <Code2 :size="12" /> #DEV-000001 (Site Web)
            </button>
            <button class="chip-item" @click="quickSearchRef('000002')">
              <Bot :size="12" /> #DEV-000002 (Agent IA)
            </button>
            <button class="chip-item" @click="quickSearchRef('000003')">
              <Layers :size="12" /> #DEV-000003 (SaaS Laravel)
            </button>
            <button v-if="savedEmail" class="chip-item saved-item" @click="quickSearchEmail(savedEmail)">
              <History :size="12" /> {{ savedEmail }}
            </button>
          </div>

          <!-- Alert Error -->
          <div v-if="error" class="error-alert-box flex items-center gap-2 slide-down">
            <AlertCircle :size="18" />
            <span>{{ error }}</span>
          </div>

        </div>

      </div>
    </section>

    <!-- Main Results Section -->
    <main class="main-content-area">
      
      <!-- Quotes Found State -->
      <section v-if="quotes.length" class="results-dashboard fade-in">
        
        <!-- Multi-Quotes List Selection -->
        <div v-if="quotes.length > 1" class="multi-quote-list-card glass-panel">
          <div class="list-card-header flex justify-between items-center flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
              <span class="badge-total">{{ quotes.length }} dossiers rattachés</span>
              <span class="intro-text">Sélectionnez un devis dans la liste ci-dessous :</span>
            </div>
            <span class="list-helper-hint"><MousePointerClick :size="13" /> Cliquez pour changer de dossier</span>
          </div>

          <div class="quotes-vertical-list">
            <div
              v-for="(q, idx) in quotes"
              :key="q.id"
              :class="['quote-list-row', { 'is-active': activeQuoteIndex === idx }]"
              @click="activeQuoteIndex = idx"
            >
              <div class="row-left flex items-center gap-3.5">
                <div class="row-select-circle">
                  <Check v-if="activeQuoteIndex === idx" :size="13" />
                  <span v-else class="row-circle-dot"></span>
                </div>
                <div class="row-ref-pill">
                  #{{ q.ref }}
                </div>
                <div class="row-info">
                  <h4 class="row-service-title">{{ q.service_title }}</h4>
                  <div class="row-meta-pills flex items-center gap-3">
                    <span v-if="q.amount" class="meta-pill meta-amount">
                      <Coins :size="12" class="text-gold" /> {{ formatAmount(q.amount) }}
                    </span>
                    <span v-if="q.deadline" class="meta-pill">
                      <Clock :size="12" /> {{ q.deadline }}
                    </span>
                    <span v-if="q.created_at" class="meta-pill">
                      <Calendar :size="12" /> {{ formatDate(q.created_at) }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="row-right flex items-center gap-3">
                <div :class="['row-status-badge', statusClass(q.status)]">
                  <component :is="statusIconComponent(q.status)" :size="13" />
                  <span>{{ statusLabel(q.status) }}</span>
                </div>
                <div class="row-arrow-action">
                  <span class="action-caption">{{ activeQuoteIndex === idx ? 'Sélectionné' : 'Consulter' }}</span>
                  <ChevronRight :size="15" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Master Project Card -->
        <article v-if="currentQuote" class="project-master-card glass-panel print-target">
          
          <!-- Top Control Header -->
          <header class="card-meta-header">
            <div class="meta-left">
              <div class="dossier-tag">
                <FileCode2 :size="20" class="text-gold" />
                <span class="dossier-ref">#{{ currentQuote.ref }}</span>
              </div>
              <button class="action-copy-btn" @click="copyRef(currentQuote.ref)" title="Copier la référence">
                <Copy v-if="!copied" :size="13" />
                <Check v-else :size="13" class="text-emerald" />
                <span>{{ copied ? 'Copié !' : 'Copier' }}</span>
              </button>
              <button class="action-qr-btn" @click="showQrModal = true" title="Afficher le QR code mobile">
                <QrCode :size="13" />
                <span>QR Code Mobile</span>
              </button>
            </div>

            <div class="meta-right">
              <div :class="['live-status-pill', statusClass(currentQuote.status)]">
                <component :is="statusIconComponent(currentQuote.status)" :size="16" />
                <span>{{ statusLabel(currentQuote.status) }}</span>
              </div>
            </div>
          </header>

          <!-- Service Banner with Tech Stack preview -->
          <div class="service-spotlight">
            <div class="spotlight-icon-box">
              <component :is="getServiceIcon(currentQuote.service_title)" :size="28" />
            </div>
            <div class="spotlight-info">
              <div class="spotlight-tags">
                <span class="tag-category">PRESTATION NUMÉRIQUE</span>
                <span class="tag-badge">ARCHITECTE WEBLAB</span>
              </div>
              <h2 class="spotlight-title">{{ currentQuote.service_title }}</h2>
            </div>
          </div>

          <!-- Interactive Milestone Timeline -->
          <div class="stepper-card">
            
            <div class="stepper-header">
              <div class="header-titles">
                <span class="stepper-label">PROGRESSION DU DÉVELOPPEMENT</span>
                <span class="stepper-sub">5 jalons clés de la commande jusqu'à la mise en ligne</span>
              </div>
              <div class="header-percentage" :style="{ color: statusColor(currentQuote.status) }">
                <Activity :size="16" />
                <span>{{ statusPercentage(currentQuote.status) }}%</span>
              </div>
            </div>

            <!-- Glowing Progress Bar -->
            <div class="stepper-meter-track">
              <div
                class="stepper-meter-fill"
                :style="{ width: `${statusPercentage(currentQuote.status)}%`, background: statusGradient(currentQuote.status) }"
              ></div>
            </div>

            <!-- 5 Steps Interactive Flow -->
            <div class="milestone-steps-grid">
              <div
                v-for="(step, idx) in statusSteps"
                :key="step.key"
                :class="['milestone-node', {
                  completed: isStepCompleted(currentQuote.status, step.key),
                  active: currentQuote.status === step.key,
                  pending: !isStepActive(currentQuote.status, step.key)
                }]"
              >
                <div class="node-bullet">
                  <Check v-if="isStepCompleted(currentQuote.status, step.key)" :size="14" />
                  <component :is="step.icon" v-else-if="currentQuote.status === step.key" :size="15" />
                  <span v-else>{{ idx + 1 }}</span>
                </div>
                <div class="node-meta">
                  <span class="node-heading">{{ step.label }}</span>
                  <span class="node-caption">{{ step.desc }}</span>
                </div>
              </div>
            </div>

          </div>

          <!-- Advisory & Next Steps Callout -->
          <div :class="['advisory-callout', statusMessageClass(currentQuote.status)]">
            <div class="advisory-icon-wrap">
              <component :is="statusMessageIconComponent(currentQuote.status)" :size="24" />
            </div>
            <div class="advisory-content">
              <h4>{{ statusHeadline(currentQuote.status) }}</h4>
              <p>{{ statusMessage(currentQuote.status) }}</p>
            </div>
          </div>

          <!-- 4 Core Project Metrics -->
          <div class="core-metrics-grid">
            <div class="core-card">
              <div class="core-icon-circle icon-gold"><Coins :size="20" /></div>
              <div class="core-data">
                <span class="core-label">Montant Estimé / Validé</span>
                <span class="core-value text-gold-val">{{ formatAmount(currentQuote.amount) }}</span>
              </div>
            </div>

            <div class="core-card">
              <div class="core-icon-circle icon-indigo"><Clock :size="20" /></div>
              <div class="core-data">
                <span class="core-label">Délai Prévu</span>
                <span class="core-value">{{ currentQuote.deadline || 'Sous 15 à 30 jours' }}</span>
              </div>
            </div>

            <div class="core-card">
              <div class="core-icon-circle icon-cyan"><Calendar :size="20" /></div>
              <div class="core-data">
                <span class="core-label">Date d'Enregistrement</span>
                <span class="core-value">{{ formatDate(currentQuote.created_at) }}</span>
              </div>
            </div>

            <div class="core-card">
              <div class="core-icon-circle icon-emerald"><ShieldCheck :size="20" /></div>
              <div class="core-data">
                <span class="core-label">Garantie & Support</span>
                <span class="core-value">Chef de Projet Dédié</span>
              </div>
            </div>
          </div>

          <!-- History Logs Timeline Accordion -->
          <div class="logs-accordion-wrap">
            <button class="logs-toggle-btn" @click="showLogs = !showLogs">
              <div class="flex items-center gap-2">
                <History :size="16" class="text-gold" />
                <span>Journal des Événements & Jalons Techniques</span>
              </div>
              <ChevronDown :size="16" :style="{ transform: showLogs ? 'rotate(180deg)' : '' }" />
            </button>
            
            <div v-show="showLogs" class="logs-body slide-down">
              <div class="log-entry">
                <div class="log-dot completed"></div>
                <div class="log-detail">
                  <div class="log-top">
                    <strong>Soumission de la Demande</strong>
                    <span class="log-time">{{ formatDate(currentQuote.created_at) }}</span>
                  </div>
                  <p>Cahier des charges initial reçu et assigné au pôle développement.</p>
                </div>
              </div>

              <div v-if="currentQuote.status !== 'pending'" class="log-entry">
                <div class="log-dot completed"></div>
                <div class="log-detail">
                  <div class="log-top">
                    <strong>Prise de Contact & Calibrage</strong>
                    <span class="log-time">Audit technique</span>
                  </div>
                  <p>Échange avec le client pour confirmation du périmètre fonctionnel.</p>
                </div>
              </div>

              <div v-if="['accepted', 'in_progress', 'completed'].includes(currentQuote.status)" class="log-entry">
                <div class="log-dot completed"></div>
                <div class="log-detail">
                  <div class="log-top">
                    <strong>Validation & Devis Officiel</strong>
                    <span class="log-time">Bon de commande actif</span>
                  </div>
                  <p>Devis proforma approuvé. Déploiement des environnements de staging.</p>
                </div>
              </div>

              <div v-if="['in_progress', 'completed'].includes(currentQuote.status)" class="log-entry">
                <div class="log-dot" :class="currentQuote.status === 'completed' ? 'completed' : 'active'"></div>
                <div class="log-detail">
                  <div class="log-top">
                    <strong>Phase de Développement & Tests</strong>
                    <span class="log-time">{{ currentQuote.status === 'completed' ? 'Finalisé' : 'En cours' }}</span>
                  </div>
                  <p>Intégration Vue 3, API Laravel, sécurisation et optimisation Core Web Vitals.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Staging & Deliverables Spotlight (Active or Completed projects) -->
          <div v-if="['accepted', 'in_progress', 'completed'].includes(currentQuote.status)" class="deliverables-box glass-panel">
            <div class="deliverables-header flex justify-between items-center">
              <div class="flex items-center gap-2">
                <Layers :size="18" class="text-indigo" />
                <span class="deliverables-title">Espace Livrables & Déploiement Staging</span>
              </div>
              <span class="live-env-badge">
                <span class="env-dot"></span> ENVIRONNEMENT SÉCURISÉ
              </span>
            </div>
            
            <div class="deliverables-grid grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
              <div class="deliverable-item">
                <div class="deliv-icon"><Globe :size="18" /></div>
                <div class="deliv-info">
                  <strong>Prévisualisation Staging</strong>
                  <span>Accès Démo Client sécurisé</span>
                </div>
              </div>
              <div class="deliverable-item">
                <div class="deliv-icon"><Code2 :size="18" /></div>
                <div class="deliv-info">
                  <strong>Dépôt Git & Code Source</strong>
                  <span>Vue 3 / Laravel 12 Pro</span>
                </div>
              </div>
              <div class="deliverable-item">
                <div class="deliv-icon"><ShieldCheck :size="18" /></div>
                <div class="deliv-info">
                  <strong>Garantie & SLA 99.9%</strong>
                  <span>Support VIP & Mises à jour</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Lead Dev & AI Assistant Direct Bar -->
          <div class="project-team-bar flex flex-wrap justify-between items-center gap-3">
            <div class="flex items-center gap-3">
              <div class="team-avatar">
                <Bot :size="18" class="text-gold" />
              </div>
              <div>
                <h5 class="team-name">Assistant IA Yass Digital Lab</h5>
                <p class="team-sub">Disponible 24/7 pour toute question sur ce devis</p>
              </div>
            </div>
            <button 
              type="button" 
              class="ask-ai-btn flex items-center gap-2"
              @click="askAiAboutQuote(currentQuote)"
            >
              <Sparkles :size="15" class="text-gold" />
              <span>Interroger l'Assistant IA sur ce dossier</span>
            </button>
          </div>

          <!-- Comprehensive Action Toolbar -->
          <footer class="action-command-toolbar no-print">
            
            <!-- Pay Online Button -->
            <button
              v-if="['contacted', 'accepted'].includes(currentQuote?.status)"
              class="cmd-btn"
              style="background: var(--color-primary); color: white; border: none; font-weight: 700;"
              @click="payOnline"
            >
              <CreditCard :size="18" />
              <span>Payer en ligne (Sécurisé)</span>
            </button>
            
            <!-- Download PDF Button -->
            <button
              class="cmd-btn cmd-btn-pdf"
              :disabled="downloadingPdf"
              @click="downloadPdf(currentQuote.id)"
            >
              <FileDown v-if="!downloadingPdf" :size="18" />
              <span v-if="downloadingPdf" class="btn-spinner"></span>
              <span>{{ downloadingPdf ? 'Génération...' : 'Télécharger le Devis PDF' }}</span>
            </button>

            <!-- WhatsApp Chat Button -->
            <a
              :href="whatsappLink(currentQuote)"
              target="_blank"
              rel="noopener noreferrer"
              class="cmd-btn cmd-btn-wa"
            >
              <MessageSquare :size="18" />
              <span>Échanger sur WhatsApp</span>
            </a>

            <!-- Print / Export Button -->
            <button class="cmd-btn cmd-btn-ghost" @click="printDossier">
              <Printer :size="16" />
              <span>Imprimer Dossier</span>
            </button>

            <!-- Share Link Button -->
            <button class="cmd-btn cmd-btn-ghost" @click="shareTrackingLink(currentQuote)">
              <Share2 :size="16" />
              <span>Partager</span>
            </button>

          </footer>

        </article>

      </section>

      <!-- Empty Search Result -->
      <section v-else-if="searched && !loading" class="empty-view-container fade-in">
        <div class="empty-glass-card glass-panel">
          <div class="empty-avatar">
            <ClipboardX :size="42" />
          </div>
          <h3>Aucun dossier de devis trouvé</h3>
          <p>
            Nous n'avons trouvé aucun dossier correspondant à cette recherche. Vérifiez vos informations ou déposez une nouvelle demande en 2 minutes.
          </p>
          <div class="empty-actions-row">
            <router-link to="/services" class="btn-action-prime">
              <Plus :size="16" /> Déposer une demande de devis
            </router-link>
            <a :href="generalWhatsappLink" target="_blank" class="btn-action-outline">
              <MessageSquare :size="16" /> Support WhatsApp direct
            </a>
          </div>
        </div>
      </section>

      <!-- How It Works 3-Step Section -->
      <section class="how-it-works-section">
        <div class="how-header">
          <span class="micro-badge">TRANSPARENCE TOTALE</span>
          <h2>Du Devis Initial à la Mise en Ligne</h2>
          <p>Chaque étape de votre projet est encadrée avec rigueur et communication continue.</p>
        </div>

        <div class="how-grid">
          <div class="how-card glass-panel">
            <div class="how-number">01</div>
            <div class="how-icon-box"><Zap :size="24" /></div>
            <h3>Étude & Estimation sous 24h</h3>
            <p>Analyse de votre cahier des charges et proposition d'une architecture moderne chiffrée avec précision.</p>
          </div>

          <div class="how-card glass-panel">
            <div class="how-number">02</div>
            <div class="how-icon-box"><CheckCircle2 :size="24" /></div>
            <h3>Validation & Acompte Sécurisé</h3>
            <p>Validation du planning, signature du bon de commande et déploiement de l'environnement de staging.</p>
          </div>

          <div class="how-card glass-panel">
            <div class="how-number">03</div>
            <div class="how-icon-box"><PlayCircle :size="24" /></div>
            <h3>Développement & Livraison Clé en Main</h3>
            <p>Suivi en direct, tests automatisés, remise des codes sources et support technique continu.</p>
          </div>
        </div>
      </section>

      <!-- FAQ Accordion Block -->
      <section class="faq-accordion-section">
        <div class="faq-card glass-panel">
          <div class="faq-head">
            <HelpCircle :size="26" class="text-gold" />
            <h2>Questions Fréquentes sur le Suivi</h2>
            <p>Toutes les réponses pour mener à bien votre projet numérique.</p>
          </div>

          <div class="faq-rows">
            <div
              v-for="(faq, fIdx) in faqs"
              :key="fIdx"
              :class="['faq-row', { active: openFaq === fIdx }]"
              @click="toggleFaq(fIdx)"
            >
              <div class="faq-q">
                <span>{{ faq.q }}</span>
                <ChevronDown :size="18" class="faq-arrow" />
              </div>
              <div v-show="openFaq === fIdx" class="faq-a slide-down">
                <p>{{ faq.a }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

    </main>

    <!-- QR Code Mobile Sync Modal -->
    <div v-if="showQrModal && currentQuote" class="modal-backdrop" @click.self="showQrModal = false">
      <div class="modal-card glass-panel slide-down">
        <button class="modal-close-btn" @click="showQrModal = false"><X :size="20" /></button>
        <div class="modal-header text-center">
          <QrCode :size="32" class="text-gold mb-2" style="margin: 0 auto 8px;" />
          <h3>Synchronisation Mobile</h3>
          <p>Scannez ce QR Code avec votre smartphone pour continuer le suivi en direct sur mobile.</p>
        </div>
        
        <div class="qr-image-wrap">
          <img :src="qrCodeUrl(currentQuote)" alt="QR Code Suivi Devis" />
        </div>

        <div class="modal-footer">
          <span class="qr-ref-chip">#{{ currentQuote.ref }}</span>
          <button class="btn-copy-modal" @click="copyTrackingLink(currentQuote)">
            <Copy :size="14" /> Copier l'URL mobile
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../stores/auth'
import { useToastStore } from '../stores/toast'
import { useCurrencyStore } from '../stores/currency'
import {
  Search,
  FileDown,
  MessageSquare,
  Mail,
  Zap,
  Clock,
  PhoneCall,
  CheckCircle2,
  PlayCircle,
  CheckCheck,
  XCircle,
  Info,
  Check,
  AlertCircle,
  History,
  Hash,
  RotateCcw,
  Sparkles,
  FileCode2,
  Copy,
  Calendar,
  ShieldCheck,
  Share2,
  ClipboardX,
  Plus,
  HelpCircle,
  ChevronDown,
  Coins,
  QrCode,
  CreditCard,
  Printer,
  X,
  Code2,
  Bot,
  Layers,
  Activity,
  Globe,
  ChevronRight,
  MousePointerClick
} from 'lucide-vue-next'

const auth = useAuthStore()
const toast = useToastStore()
const currencyStore = useCurrencyStore()

const searchMode = ref('ref') // 'ref' | 'email'
const searchRef = ref('')
const searchEmail = ref('')
const quotes = ref([])
const activeQuoteIndex = ref(0)
const loading = ref(false)
const error = ref('')
const searched = ref(false)
const copied = ref(false)
const downloadingPdf = ref(false)
const openFaq = ref(0)
const showLogs = ref(true)
const showQrModal = ref(false)

const savedRef = ref(localStorage.getItem('last_quote_ref') || '')
const savedEmail = ref(localStorage.getItem('last_quote_email') || '')

const currentQuote = computed(() => {
  if (!quotes.value.length) return null
  return quotes.value[activeQuoteIndex.value] || quotes.value[0]
})

const statusSteps = [
  { key: 'pending',     label: 'Reçu',       desc: 'Demande enregistrée',    icon: Clock },
  { key: 'contacted',   label: 'Contacté',   desc: 'Échange technique',      icon: PhoneCall },
  { key: 'accepted',    label: 'Validé',     desc: 'Devis & budget validés',  icon: CheckCircle2 },
  { key: 'in_progress', label: 'En cours',   desc: 'Production active',      icon: PlayCircle },
  { key: 'completed',   label: 'Terminé',    desc: 'Livré & opérationnel',   icon: CheckCheck },
]

const stepOrder = ['pending', 'contacted', 'accepted', 'in_progress', 'completed']

const isStepActive = (currentStatus, stepKey) => {
  if (currentStatus === 'rejected') return false
  const currentIdx = stepOrder.indexOf(currentStatus)
  const stepIdx    = stepOrder.indexOf(stepKey)
  return stepIdx <= currentIdx
}

const isStepCompleted = (currentStatus, stepKey) => {
  if (currentStatus === 'rejected') return false
  const currentIdx = stepOrder.indexOf(currentStatus)
  const stepIdx    = stepOrder.indexOf(stepKey)
  return stepIdx < currentIdx
}

const statusPercentage = (status) => {
  switch (status) {
    case 'pending': return 20
    case 'contacted': return 40
    case 'accepted': return 65
    case 'in_progress': return 85
    case 'completed': return 100
    case 'rejected': return 0
    default: return 20
  }
}

const statusColor = (status) => {
  switch (status) {
    case 'pending': return '#F5C027'
    case 'contacted': return '#60A5FA'
    case 'accepted': return '#34D399'
    case 'in_progress': return '#818CF8'
    case 'completed': return '#10B981'
    case 'rejected': return '#EF4444'
    default: return '#F5C027'
  }
}

const statusGradient = (status) => {
  switch (status) {
    case 'pending': return 'linear-gradient(90deg, #F59E0B, #FCD34D)'
    case 'contacted': return 'linear-gradient(90deg, #3B82F6, #60A5FA)'
    case 'accepted': return 'linear-gradient(90deg, #059669, #34D399)'
    case 'in_progress': return 'linear-gradient(90deg, #6366F1, #818CF8)'
    case 'completed': return 'linear-gradient(90deg, #10B981, #6EE7B7)'
    case 'rejected': return 'linear-gradient(90deg, #DC2626, #EF4444)'
    default: return 'linear-gradient(90deg, #6366F1, #F5C027)'
  }
}

const statusLabel = (status) => ({
  pending:     'En attente d\'analyse',
  contacted:   'Contact établi',
  accepted:    'Devis Validé',
  in_progress: 'Projet en cours',
  completed:   'Projet Terminé & Livré',
  rejected:    'Non retenu',
}[status] || 'Traitement en cours')

const statusHeadline = (status) => ({
  pending:     'Demande bien enregistrée dans notre système',
  contacted:   'Prise de contact en cours',
  accepted:    'Devis confirmé et validé !',
  in_progress: 'Développement & intégration en cours',
  completed:   'Projet finalisé avec succès !',
  rejected:    'Demande clôturée',
}[status] || 'État du dossier')

const statusIconComponent = (status) => ({
  pending:     Clock,
  contacted:   PhoneCall,
  accepted:    CheckCircle2,
  in_progress: PlayCircle,
  completed:   CheckCheck,
  rejected:    XCircle,
}[status] || Clock)

const getServiceIcon = (title) => {
  if (!title) return Sparkles
  const t = title.toLowerCase()
  if (t.includes('site') || t.includes('web')) return Globe
  if (t.includes('ia') || t.includes('agent')) return Bot
  if (t.includes('saas') || t.includes('laravel')) return Layers
  return Code2
}

const statusClass = (status) => ({
  pending:     'badge-pending',
  contacted:   'badge-contacted',
  accepted:    'badge-accepted',
  in_progress: 'badge-inprogress',
  completed:   'badge-completed',
  rejected:    'badge-rejected',
}[status] || 'badge-pending')

const statusMessage = (status) => ({
  pending:     "Votre demande a bien été reçue par notre équipe technique. Un expert étudie votre cahier des charges pour préparer votre estimation détaillée sous 24h ouvrées.",
  contacted:   "Notre équipe a pris contact avec vous par Email / Téléphone / WhatsApp pour affiner les spécifications techniques de votre projet.",
  accepted:    "Votre devis proforma a été accepté ! Le planning d'exécution est configuré et la documentation technique est prête au téléchargement.",
  in_progress: "Votre solution numérique est en cours de développement actif. Vous recevrez des points d'étape réguliers sur l'avancement.",
  completed:   "La prestation est terminée, testée et déployée. L'ensemble des livrables et accès vous ont été remis.",
  rejected:    "Cette demande n'a pas pu aboutir dans les conditions requises. N'hésitez pas à nous contacter directement pour réexaminer votre besoin.",
}[status] || "Votre dossier est en cours d'actualisation par nos services.")

const statusMessageClass = (status) => ({
  pending:     'msg-pending',
  contacted:   'msg-contacted',
  accepted:    'msg-accepted',
  in_progress: 'msg-inprogress',
  completed:   'msg-completed',
  rejected:    'msg-rejected',
}[status] || 'msg-pending')

const statusMessageIconComponent = (status) => ({
  pending:     Info,
  contacted:   PhoneCall,
  accepted:    CheckCircle2,
  in_progress: PlayCircle,
  completed:   CheckCheck,
  rejected:    XCircle,
}[status] || Info)

const formatAmount = (val) => {
  if (!val) return 'Sur Devis Personnalisé'
  const str = String(val).replace(/[^\d]/g, '')
  if (str) {
    const num = parseInt(str)
    return currencyStore.format(num)
  }
  return val
}

const formatDate = (iso) => {
  if (!iso) return 'Date non spécifiée'
  return new Date(iso).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

const whatsappLink = (quote) => {
  const msg = `Bonjour Yass Digital Lab, je souhaite un suivi pour mon devis #${quote.ref}.\nService : ${quote.service_title}\nDemandeur : ${searchEmail.value || (auth.user?.email || '')}`
  return `https://wa.me/22549679002?text=${encodeURIComponent(msg)}`
}

const generalWhatsappLink = 'https://wa.me/22549679002?text=Bonjour%20Yass%20Digital%20Lab,%20je%20souhaite%20une%20assistance%20pour%20un%20devis.'

const qrCodeUrl = (quote) => {
  const directUrl = `${window.location.origin}/suivi-devis?ref=${quote.id}&email=${encodeURIComponent(searchEmail.value || '')}`
  return `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(directUrl)}&bgcolor=111827&color=F5C027`
}

const copyRef = async (refVal) => {
  try {
    await navigator.clipboard.writeText(`DEV-${refVal}`)
    copied.value = true
    toast.showToast(`Référence #DEV-${refVal} copiée !`, 'success')
    setTimeout(() => { copied.value = false }, 2500)
  } catch {
    toast.showToast(`Référence : #DEV-${refVal}`, 'info')
  }
}

const shareTrackingLink = (quote) => {
  const shareUrl = `${window.location.origin}/suivi-devis?ref=${quote.id}&email=${encodeURIComponent(searchEmail.value || '')}`
  if (navigator.clipboard) {
    navigator.clipboard.writeText(shareUrl)
    toast.showToast('Lien direct copié dans le presse-papiers !', 'success')
  }
}

const copyTrackingLink = (quote) => {
  shareTrackingLink(quote)
}

const printDossier = () => {
  window.print()
}

const askAiAboutQuote = (quote) => {
  const query = `Où en est mon devis #${quote.ref} (${quote.service_title}) ?`
  window.dispatchEvent(new CustomEvent('open-ai-chat', { detail: { query } }))
  toast.showToast('Assistant IA activé pour votre devis !', 'info')
}

const downloadPdf = async (id) => {
  downloadingPdf.value = true
  try {
    const res = await axios.get(`/api/quote-requests/${id}/pdf-link`)
    if (res.data?.pdf_url) {
      window.open(res.data.pdf_url, '_blank')
      toast.showToast('Ouverture du devis officiel...', 'success')
    } else {
      window.open(`/api/quote-requests/${id}/pdf`, '_blank')
    }
  } catch {
    try {
      window.open(`/api/quote-requests/${id}/pdf`, '_blank')
    } catch {
      toast.showToast('Impossible de générer le lien PDF.', 'error')
    }
  } finally {
    downloadingPdf.value = false
  }
}

const payOnline = async () => {
  if (!currentQuote.value) return;
  
  const rawAmount = currentQuote.value.amount || currentQuote.value.budget || '0';
  const amountStr = String(rawAmount).replace(/[^\d]/g, '');
  const amountNum = parseInt(amountStr) || 0;
  
  if (amountNum <= 0) {
    toast.showToast("Le montant du devis n'est pas encore défini.", "error");
    return;
  }
  
  // Conversion FCFA vers EUR (Stripe est configuré en EUR dans le backend)
  const priceInEur = Math.round(amountNum / 655.957);

  try {
    toast.showToast("Initialisation du paiement sécurisé...", "info");
    const res = await axios.post('/api/create-checkout-session', {
      email: currentQuote.value.email,
      quote_id: currentQuote.value.id,
      items: [
        {
          title: `Devis DEV-${currentQuote.value.ref || currentQuote.value.id} - ${currentQuote.value.service_title}`,
          price: priceInEur,
          quantity: 1
        }
      ]
    });
    
    if (res.data.url) {
      window.location.href = res.data.url;
    }
  } catch (error) {
    console.error('Payment Error:', error);
    toast.showToast("Erreur lors de l'initialisation du paiement.", "error");
  }
};

const quickSearchRef = (refVal) => {
  searchMode.value = 'ref'
  searchRef.value = refVal
  searchEmail.value = ''
  searchQuote()
}

const quickSearchEmail = (emailVal) => {
  searchMode.value = 'email'
  searchEmail.value = emailVal
  searchRef.value = ''
  searchQuote()
}

const resetSearch = () => {
  searchRef.value = ''
  searchEmail.value = ''
  quotes.value = []
  searched.value = false
  error.value = ''
}

const searchQuote = async () => {
  if (searchMode.value === 'ref' && !searchRef.value) return
  if (searchMode.value === 'email' && !searchEmail.value) return

  loading.value = true
  error.value = ''
  quotes.value = []
  activeQuoteIndex.value = 0
  searched.value = true

  try {
    const params = {}
    if (searchMode.value === 'ref' && searchRef.value) {
      params.ref = searchRef.value.replace(/\D/g, '')
    }
    if (searchMode.value === 'email' && searchEmail.value) {
      params.email = searchEmail.value.trim()
    }

    const res = await axios.get('/api/quote-requests/track', { params })
    quotes.value = res.data.data || []
    
    // Save to localStorage
    try {
      if (params.ref) {
        localStorage.setItem('last_quote_ref', params.ref)
        savedRef.value = params.ref
      }
      if (params.email) {
        localStorage.setItem('last_quote_email', params.email)
        savedEmail.value = params.email
      }
    } catch (e) {}

    if (quotes.value.length) {
      toast.showToast(`${quotes.value.length} dossier(s) trouvé(s) !`, 'success')
    }
  } catch (err) {
    if (err.response?.status === 404) {
      quotes.value = []
    } else {
      error.value = err.response?.data?.message || 'Aucun dossier correspondant trouvé.'
    }
  } finally {
    loading.value = false
  }
}

const faqs = [
  {
    q: "Quels sont les délais de traitement d'un devis chiffré ?",
    a: "Nos estimations préliminaires sont traitées sous 24 heures ouvrées. Pour les architectures logicielles complexes ou les agents IA sur mesure, un chef de projet prendra contact avec vous afin de calibrer les besoins."
  },
  {
    q: "Comment valider mon devis et démarrer le développement ?",
    a: "Dès que le statut passe à 'Validé', vous pouvez télécharger le devis PDF signé et procéder au versement de l'acompte directement en ligne ou par virement bancaire / Mobile Money."
  },
  {
    q: "Puis-je ajuster mon cahier des charges après soumission ?",
    a: "Oui tout à fait. Vous pouvez nous joindre directement via WhatsApp ou par email avec votre numéro de référence pour ajouter des fonctionnalités ou ajuster les délais."
  },
  {
    q: "Mes données et mon concept sont-ils protégés ?",
    a: "Oui, la confidentialité est absolue et protégée par un accord de non-divulgation (NDA) garantissant le secret industriel de votre idée."
  }
]

const toggleFaq = (idx) => {
  openFaq.value = openFaq.value === idx ? -1 : idx
}

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const paramRef = urlParams.get('ref')
  const paramEmail = urlParams.get('email')

  if (paramRef) {
    searchMode.value = 'ref'
    searchRef.value = paramRef
    searchQuote()
  } else if (paramEmail) {
    searchMode.value = 'email'
    searchEmail.value = paramEmail
    searchQuote()
  } else if (savedRef.value) {
    searchMode.value = 'ref'
    searchRef.value = savedRef.value
    searchQuote()
  } else if (savedEmail.value) {
    searchMode.value = 'email'
    searchEmail.value = savedEmail.value
    searchQuote()
  } else if (auth.user?.email) {
    searchMode.value = 'email'
    searchEmail.value = auth.user.email
    searchQuote()
  }
})
</script>

<style scoped>
/* â”FCFAâ”FCFA Ambient FX & Background â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.tracking-page {
  min-height: 100vh;
  color: var(--color-text);
  position: relative;
  overflow-x: hidden;
  padding-bottom: 70px;
}

.fx-mesh {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
  z-index: 0;
}
.orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(130px);
}
.orb-1 {
  top: -100px;
  right: 15%;
  width: 550px;
  height: 550px;
  background: radial-gradient(circle, rgba(245, 192, 39, 0.13) 0%, transparent 70%);
}
.orb-2 {
  top: 150px;
  left: 5%;
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.16) 0%, transparent 70%);
}
.orb-3 {
  bottom: 100px;
  right: 25%;
  width: 450px;
  height: 450px;
  background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
}
.grid-overlay {
  position: absolute;
  inset: 0;
  background-image: linear-gradient(rgba(255, 255, 255, 0.015) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 255, 255, 0.015) 1px, transparent 1px);
  background-size: 40px 40px;
}

.glass-panel {
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  border-radius: 24px;
  box-shadow: var(--shadow-sm);
}

/* â”FCFAâ”FCFA Hero Section â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.tracking-hero {
  position: relative;
  padding: 70px 24px 60px;
  text-align: center;
  z-index: 2;
}
.hero-inner {
  max-width: 860px;
  margin: 0 auto;
}

.live-status-badge {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: var(--color-bg-elevated);
  border: 1px solid rgba(245, 192, 39, 0.35);
  padding: 6px 18px;
  border-radius: 999px;
  font-size: 11.5px;
  font-weight: 800;
  letter-spacing: 0.08em;
  color: #F5C027;
  margin-bottom: 24px;
  box-shadow: var(--shadow-sm);
}
.live-radar {
  position: relative;
  width: 10px;
  height: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.radar-core {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #10B981;
}
.radar-wave {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border: 1.5px solid #10B981;
  animation: radar-pulse 2s infinite;
}
@keyframes radar-pulse {
  0% { transform: scale(0.8); opacity: 1; }
  100% { transform: scale(2.2); opacity: 0; }
}
.badge-separator { color: #475569; }
.badge-tech { color: #818CF8; font-family: monospace; font-size: 11px; }

.hero-headline {
  font-size: clamp(2.3rem, 5.5vw, 3.6rem);
  font-weight: 900;
  line-height: 1.15;
  letter-spacing: -0.03em;
  color: var(--color-text);
  margin: 0 0 16px;
}
.specular-gradient {
  background: linear-gradient(135deg, #FFFFFF 20%, #F5C027 60%, #818CF8 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.hero-description {
  font-size: 16.5px;
  line-height: 1.65;
  color: var(--color-text-muted);
  max-width: 660px;
  margin: 0 auto 36px;
}

/* â”FCFAâ”FCFA Command Search Card â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.command-search-card {
  padding: 30px 34px;
  border: 1px solid rgba(245, 192, 39, 0.22);
  box-shadow: 0 20px 55px rgba(0, 0, 0, 0.55), 0 0 35px rgba(99, 102, 241, 0.15);
}

.segmented-control {
  display: flex;
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  border-radius: 14px;
  padding: 4px;
  gap: 6px;
  margin-bottom: 20px;
}
.segment-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: 10px;
  border: none;
  background: transparent;
  color: var(--color-text-muted);
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.segment-btn:hover { color: var(--color-text); }
.segment-btn.active {
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.35), rgba(79, 70, 229, 0.45));
  color: var(--color-text);
  border: 1px solid rgba(99, 102, 241, 0.4);
  box-shadow: 0 4px 16px rgba(99, 102, 241, 0.25);
}

.search-input-group {
  display: flex;
  gap: 12px;
  margin-bottom: 18px;
}
.field-container {
  flex: 1;
  display: flex;
  align-items: center;
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  border-radius: 14px;
  overflow: hidden;
  transition: all 0.25s ease;
}
.field-container:focus-within {
  border-color: #F5C027;
  box-shadow: 0 0 0 3px rgba(245, 192, 39, 0.1);
}
.field-prefix {
  padding: 0 16px;
  height: 52px;
  display: flex;
  align-items: center;
  background: rgba(245, 192, 39, 0.1);
  border-right: 1px solid var(--color-border);
}
.prefix-tag {
  color: #F5C027;
  font-weight: 900;
  font-size: 14px;
}
.email-prefix {
  color: #818CF8;
  background: rgba(99, 102, 241, 0.08);
}
.field-input {
  flex: 1;
  height: 52px;
  background: transparent;
  border: none;
  outline: none;
  color: var(--color-text);
  font-size: 15px;
  font-weight: 600;
  padding: 0 16px;
}
.field-input::placeholder { color: #64748B; font-weight: 500; }

.search-submit-btn {
  background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
  color: var(--color-text);
  font-size: 15px;
  font-weight: 800;
  padding: 0 28px;
  height: 52px;
  border: none;
  border-radius: 14px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.35);
  transition: all 0.25s ease;
  white-space: nowrap;
}
.search-submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(99, 102, 241, 0.5);
  filter: brightness(1.1);
}
.search-submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.reset-btn {
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  color: var(--color-text-muted);
  border-radius: 14px;
  width: 52px;
  height: 52px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.reset-btn:hover { background: rgba(255, 255, 255, 0.1); color: var(--color-text); }

.quick-chips-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
}
.chips-title {
  color: #64748B;
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 4px;
}
.chip-item {
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  color: var(--color-primary);
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 11.5px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s;
}
.chip-item:hover {
  background: rgba(99, 102, 241, 0.2);
  color: var(--color-text);
  border-color: rgba(99, 102, 241, 0.4);
  transform: translateY(-1px);
}
.saved-item {
  background: rgba(245, 192, 39, 0.1);
  color: #FDE047;
  border-color: rgba(245, 192, 39, 0.3);
}

.error-alert-box {
  background: rgba(239, 68, 68, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.35);
  color: #FCA5A5;
  border-radius: 12px;
  padding: 12px 18px;
  font-size: 14px;
  font-weight: 600;
  margin-top: 16px;
}

/* â”FCFAâ”FCFA Content Layout â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.main-content-area {
  max-width: 940px;
  margin: 0 auto;
  padding: 0 24px;
  position: relative;
  z-index: 2;
}

/* Multi-Quotes Bar */
.multi-quote-nav {
  margin-bottom: 24px;
}
.nav-intro {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}
.badge-total {
  background: rgba(245, 192, 39, 0.15);
  color: #F5C027;
  font-size: 12px;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 999px;
  border: 1px solid rgba(245, 192, 39, 0.3);
}
.intro-text { color: var(--color-text-muted); font-size: 13px; }
.nav-pills-row {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding: 4px 2px 8px 2px;
  scrollbar-width: thin;
  scrollbar-color: rgba(99, 102, 241, 0.35) transparent;
}
.nav-pills-row::-webkit-scrollbar {
  height: 4px;
}
.nav-pills-row::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.03);
  border-radius: 999px;
}
.nav-pills-row::-webkit-scrollbar-thumb {
  background: rgba(99, 102, 241, 0.35);
  border-radius: 999px;
}
.nav-pills-row::-webkit-scrollbar-thumb:hover {
  background: #6366F1;
}

.nav-quote-pill {
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  border-radius: 14px;
  padding: 10px 18px;
  color: var(--color-text-muted);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  white-space: nowrap;
  flex-shrink: 0;
}
.nav-quote-pill:hover {
  background: rgba(30, 41, 59, 0.9);
  border-color: rgba(255, 255, 255, 0.18);
  color: var(--color-text);
  transform: translateY(-1px);
}
.nav-quote-pill.active {
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, var(--color-bg-elevated) 100%);
  border-color: var(--color-primary);
  color: var(--color-text);
  box-shadow: var(--shadow-sm);
}
.pill-id { font-weight: 800; color: #F5C027; font-size: 13px; }
.pill-name { font-size: 13px; font-weight: 600; }
.pill-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  box-shadow: 0 0 6px currentColor;
}

.multi-quote-list-card {
  padding: 22px 26px;
  margin-bottom: 30px;
  border-radius: 20px;
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
}
.list-card-header {
  padding-bottom: 16px;
  margin-bottom: 16px;
  border-bottom: 1px solid var(--color-border);
}
.badge-total {
  background: rgba(245, 192, 39, 0.15);
  color: #F5C027;
  font-size: 12px;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 999px;
  border: 1px solid rgba(245, 192, 39, 0.3);
}
.intro-text { color: var(--color-text); font-size: 13.5px; font-weight: 600; }
.list-helper-hint {
  font-size: 12px;
  color: var(--color-text-muted);
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.quotes-vertical-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.quote-list-row {
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  border-radius: 16px;
  padding: 16px 20px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  flex-wrap: wrap;
  gap: 14px;
}
.quote-list-row:hover {
  background: rgba(30, 41, 59, 0.85);
  border-color: rgba(99, 102, 241, 0.35);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}
.quote-list-row.is-active {
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, var(--color-bg-elevated) 100%);
  border-color: var(--color-primary);
  box-shadow: var(--shadow-sm);
}

.row-select-circle {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.06);
  border: 1.5px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text);
  flex-shrink: 0;
}
.is-active .row-select-circle {
  background: #6366F1;
  border-color: #6366F1;
  box-shadow: 0 0 12px rgba(99, 102, 241, 0.6);
}
.row-circle-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: transparent;
}

.row-ref-pill {
  font-size: 13.5px;
  font-weight: 900;
  color: #F5C027;
  background: rgba(245, 192, 39, 0.12);
  border: 1px solid rgba(245, 192, 39, 0.25);
  padding: 4px 10px;
  border-radius: 8px;
  flex-shrink: 0;
}

.row-service-title {
  font-size: 14.5px;
  font-weight: 800;
  color: var(--color-text);
  margin: 0 0 5px;
}
.row-meta-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.meta-pill {
  font-size: 11.5px;
  color: var(--color-text-muted);
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: rgba(255, 255, 255, 0.04);
  padding: 2px 8px;
  border-radius: 6px;
}
.meta-amount {
  color: #FDE047;
  font-weight: 800;
}

.row-status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 800;
  padding: 6px 14px;
  border-radius: 999px;
}

.row-arrow-action {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  color: #6366F1;
  font-size: 12.5px;
  font-weight: 800;
}
.is-active .row-arrow-action {
  color: #A5B4FC;
}
.action-caption {
  font-size: 12px;
}

/* â”FCFAâ”FCFA Project Master Card â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.project-master-card {
  padding: 38px;
  margin-bottom: 50px;
  border: 1px solid var(--color-border);
  background: var(--color-bg-card);
  border-radius: 24px;
  box-shadow: var(--shadow-sm);
  position: relative;
}

.card-meta-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 22px;
  border-bottom: 1px solid var(--color-border);
  flex-wrap: wrap;
  gap: 14px;
}
.meta-left {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
.dossier-tag {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #F5C027;
  font-size: 20px;
  font-weight: 900;
  letter-spacing: -0.01em;
}
.action-copy-btn, .action-qr-btn {
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  color: var(--color-text-muted);
  padding: 5px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s;
}
.action-copy-btn:hover, .action-qr-btn:hover {
  background: var(--color-bg-card);
  color: var(--color-text);
}

/* Status Badges */
.live-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13.5px;
  font-weight: 800;
  padding: 8px 18px;
  border-radius: 999px;
  border: 1px solid transparent;
  letter-spacing: 0.3px;
}
.badge-pending    { background: rgba(245, 192, 39, 0.14); color: #FDE047; border-color: rgba(245, 192, 39, 0.35); }
.badge-contacted  { background: rgba(59, 130, 246, 0.14); color: #93C5FD; border-color: rgba(59, 130, 246, 0.35); }
.badge-accepted   { background: rgba(16, 185, 129, 0.14); color: #6EE7B7; border-color: rgba(16, 185, 129, 0.35); }
.badge-inprogress { background: rgba(99, 102, 241, 0.16); color: #A5B4FC; border-color: rgba(99, 102, 241, 0.35); }
.badge-completed  { background: rgba(16, 185, 129, 0.2); color: #34D399; border-color: rgba(16, 185, 129, 0.5); }
.badge-rejected   { background: rgba(239, 68, 68, 0.14); color: #FCA5A5; border-color: rgba(239, 68, 68, 0.35); }

/* Service Spotlight */
.service-spotlight {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 26px 0 22px;
}
.spotlight-icon-box {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.25), rgba(245, 192, 39, 0.2));
  border: 1px solid rgba(245, 192, 39, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #F5C027;
  flex-shrink: 0;
  box-shadow: 0 8px 24px rgba(245, 192, 39, 0.15);
}
.spotlight-tags {
  display: flex;
  gap: 8px;
  margin-bottom: 4px;
}
.tag-category {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.08em;
  color: #818CF8;
}
.tag-badge {
  font-size: 10px;
  font-weight: 800;
  background: rgba(245, 192, 39, 0.1);
  color: #F5C027;
  padding: 1px 8px;
  border-radius: 4px;
  border: 1px solid rgba(245, 192, 39, 0.25);
}
.spotlight-title {
  font-size: 23px;
  font-weight: 900;
  color: var(--color-text);
  margin: 0;
  letter-spacing: -0.01em;
}

/* â”FCFAâ”FCFA Stepper Card â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.stepper-card {
  background: var(--color-bg-elevated);
  border: 1px dashed var(--color-border);
  border-radius: 18px;
  padding: 26px;
  margin-bottom: 26px;
}
.stepper-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 14px;
}
.header-titles {
  display: flex;
  flex-direction: column;
}
.stepper-label {
  font-size: 12px;
  font-weight: 800;
  color: var(--color-text-muted);
  letter-spacing: 0.06em;
}
.stepper-sub {
  font-size: 12px;
  color: #64748B;
  margin-top: 2px;
}
.header-percentage {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 16px;
  font-weight: 900;
}

.stepper-meter-track {
  width: 100%;
  height: 8px;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 999px;
  overflow: hidden;
  margin-bottom: 24px;
}
.stepper-meter-fill {
  height: 100%;
  border-radius: 999px;
  transition: width 0.7s cubic-bezier(0.16, 1, 0.3, 1);
}

.milestone-steps-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
  gap: 12px;
}
.milestone-node {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 10px;
  border-radius: 12px;
}
.node-bullet {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 900;
  flex-shrink: 0;
  border: 1.5px solid rgba(255, 255, 255, 0.15);
  background: rgba(255, 255, 255, 0.04);
  color: #64748B;
}
.milestone-node.completed .node-bullet {
  background: #10B981;
  border-color: #10B981;
  color: var(--color-text);
  box-shadow: 0 0 12px rgba(16, 185, 129, 0.4);
}
.milestone-node.active .node-bullet {
  background: #F5C027;
  border-color: #F5C027;
  color: #0B0F19;
  box-shadow: 0 0 18px rgba(245, 192, 39, 0.6);
  animation: pulse-active 1.5s infinite;
}
@keyframes pulse-active {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

.node-meta {
  display: flex;
  flex-direction: column;
}
.node-heading {
  font-size: 13.5px;
  font-weight: 800;
  color: var(--color-text);
}
.node-caption {
  font-size: 11px;
  color: #64748B;
  margin-top: 2px;
}
.milestone-node.pending .node-heading { color: #64748B; }

/* Advisory Callout */
.advisory-callout {
  display: flex;
  gap: 16px;
  padding: 22px 26px;
  border-radius: 16px;
  margin-bottom: 26px;
  border: 1px solid transparent;
}
.advisory-icon-wrap { flex-shrink: 0; margin-top: 2px; }
.advisory-content h4 { font-size: 15.5px; font-weight: 800; margin: 0 0 4px; }
.advisory-content p { font-size: 14px; line-height: 1.65; margin: 0; opacity: 0.92; }

.msg-pending {
  background: rgba(245, 192, 39, 0.08);
  color: #FDE047;
  border-color: rgba(245, 192, 39, 0.28);
}
.msg-contacted {
  background: rgba(59, 130, 246, 0.08);
  color: #93C5FD;
  border-color: rgba(59, 130, 246, 0.28);
}
.msg-accepted {
  background: rgba(16, 185, 129, 0.1);
  color: #86EFAC;
  border-color: rgba(16, 185, 129, 0.32);
}
.msg-inprogress {
  background: rgba(99, 102, 241, 0.1);
  color: #A5B4FC;
  border-color: rgba(99, 102, 241, 0.28);
}
.msg-completed {
  background: rgba(16, 185, 129, 0.14);
  color: #6EE7B7;
  border-color: rgba(16, 185, 129, 0.4);
}
.msg-rejected {
  background: rgba(239, 68, 68, 0.08);
  color: #FCA5A5;
  border-color: rgba(239, 68, 68, 0.28);
}

/* Core Metrics Grid */
.core-metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
  gap: 16px;
  margin-bottom: 26px;
}
.core-card {
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  border-radius: 14px;
  padding: 18px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
}
.core-icon-circle {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.icon-gold    { background: rgba(245, 192, 39, 0.12); color: #F5C027; }
.icon-indigo  { background: rgba(99, 102, 241, 0.12); color: #818CF8; }
.icon-cyan    { background: rgba(6, 182, 212, 0.12); color: #22D3EE; }
.icon-emerald { background: rgba(16, 185, 129, 0.12); color: #34D399; }

.core-data { display: flex; flex-direction: column; }
.core-label {
  font-size: 11.5px;
  color: #64748B;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.core-value { font-size: 14.5px; font-weight: 800; color: var(--color-text); margin-top: 3px; }
.text-gold-val { color: #F5C027; font-size: 17px; font-weight: 900; }

/* â”FCFAâ”FCFA Logs Accordion â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.logs-accordion-wrap {
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  border-radius: 14px;
  margin-bottom: 26px;
  overflow: hidden;
}
.logs-toggle-btn {
  width: 100%;
  padding: 16px 20px;
  background: transparent;
  border: none;
  color: var(--color-text);
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: background 0.2s;
}
.logs-toggle-btn:hover { background: rgba(255, 255, 255, 0.04); }
.logs-body {
  padding: 10px 24px 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.log-entry {
  display: flex;
  gap: 14px;
  position: relative;
}
.log-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  margin-top: 6px;
  flex-shrink: 0;
}
.log-dot.completed { background: #10B981; box-shadow: 0 0 8px #10B981; }
.log-dot.active { background: #F5C027; box-shadow: 0 0 8px #F5C027; }
.log-detail { flex: 1; }
.log-top {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  color: var(--color-text);
  margin-bottom: 3px;
}
.log-time { font-size: 11.5px; color: #64748B; }
.log-detail p { font-size: 12.5px; color: var(--color-text-muted); margin: 0; line-height: 1.5; }

/* â”FCFAâ”FCFA Deliverables Box & Team Bar â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.deliverables-box {
  padding: 20px 24px;
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  border-radius: 16px;
  margin-bottom: 22px;
}
.deliverables-title {
  font-size: 13.5px;
  font-weight: 800;
  color: var(--color-text);
}
.live-env-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(16, 185, 129, 0.12);
  border: 1px solid rgba(16, 185, 129, 0.3);
  color: #34D399;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.05em;
}
.env-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #10B981;
  box-shadow: 0 0 8px #10B981;
}
.deliverables-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
}
.deliverable-item {
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 12px 14px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.deliv-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(99, 102, 241, 0.15);
  color: #818CF8;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.deliv-info {
  display: flex;
  flex-direction: column;
}
.deliv-info strong {
  font-size: 12.5px;
  color: var(--color-text);
}
.deliv-info span {
  font-size: 11px;
  color: var(--color-text-muted);
}

.project-team-bar {
  background: rgba(245, 192, 39, 0.05);
  border: 1px solid rgba(245, 192, 39, 0.25);
  border-radius: 16px;
  padding: 14px 20px;
  margin-bottom: 24px;
}
.team-avatar {
  width: 38px;
  height: 38px;
  border-radius: 12px;
  background: rgba(245, 192, 39, 0.15);
  border: 1px solid rgba(245, 192, 39, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.team-name {
  font-size: 13.5px;
  font-weight: 800;
  color: var(--color-text);
  margin: 0;
}
.team-sub {
  font-size: 11.5px;
  color: var(--color-text-muted);
  margin: 0;
}
.ask-ai-btn {
  background: linear-gradient(135deg, rgba(245, 192, 39, 0.15), rgba(99, 102, 241, 0.15));
  border: 1px solid rgba(245, 192, 39, 0.4);
  color: #FDE047;
  padding: 8px 16px;
  border-radius: 10px;
  font-size: 12.5px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.2s ease;
}
.ask-ai-btn:hover {
  background: rgba(245, 192, 39, 0.25);
  color: var(--color-text);
  transform: translateY(-1px);
}

/* â”FCFAâ”FCFA Action Toolbar â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.action-command-toolbar {
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
  padding-top: 14px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}
.cmd-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 24px;
  border-radius: 14px;
  font-size: 14.5px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.25s ease;
  text-decoration: none;
  border: none;
}
.cmd-btn-pdf {
  background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
  color: var(--color-text);
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.35);
}
.cmd-btn-pdf:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(99, 102, 241, 0.5);
  filter: brightness(1.1);
}
.cmd-btn-wa {
  background: linear-gradient(135deg, #16A34A 0%, #15803D 100%);
  color: var(--color-text);
  box-shadow: 0 8px 24px rgba(22, 163, 74, 0.35);
}
.cmd-btn-wa:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(22, 163, 74, 0.5);
  filter: brightness(1.1);
}
.cmd-btn-ghost {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: var(--color-text);
  padding: 14px 20px;
}
.cmd-btn-ghost:hover {
  background: rgba(255, 255, 255, 0.12);
  color: var(--color-text);
}

/* â”FCFAâ”FCFA Modal Sync â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.85);
  backdrop-filter: blur(8px);
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.modal-card {
  max-width: 440px;
  width: 100%;
  padding: 34px;
  position: relative;
  border: 1px solid rgba(245, 192, 39, 0.3);
}
.modal-close-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  background: none;
  border: none;
  color: var(--color-text-muted);
  cursor: pointer;
  padding: 4px;
}
.modal-header h3 { font-size: 20px; font-weight: 800; color: var(--color-text); margin: 0 0 6px; }
.modal-header p { font-size: 13.5px; color: var(--color-text-muted); margin: 0; }
.qr-image-wrap {
  background: #111827;
  border: 2px solid rgba(245, 192, 39, 0.4);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  justify-content: center;
  margin: 22px auto;
  width: fit-content;
}
.qr-image-wrap img { width: 180px; height: 180px; border-radius: 8px; }
.modal-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  padding-top: 16px;
}
.qr-ref-chip {
  font-weight: 900;
  color: #F5C027;
  font-size: 14px;
}
.btn-copy-modal {
  background: rgba(99, 102, 241, 0.15);
  color: #C7D2FE;
  border: 1px solid rgba(99, 102, 241, 0.3);
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 5px;
}

/* â”FCFAâ”FCFA Empty State â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.empty-view-container { margin: 20px auto 60px; text-align: center; }
.empty-glass-card { padding: 50px 36px; max-width: 600px; margin: 0 auto; }
.empty-avatar {
  width: 78px;
  height: 78px;
  border-radius: 50%;
  background: rgba(239, 68, 68, 0.12);
  color: #EF4444;
  border: 1px solid rgba(239, 68, 68, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 22px;
}
.empty-glass-card h3 { font-size: 23px; font-weight: 800; color: var(--color-text); margin: 0 0 10px; }
.empty-glass-card p { font-size: 14.5px; color: var(--color-text-muted); line-height: 1.65; margin: 0 0 26px; }
.empty-actions-row { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.btn-action-prime {
  background: linear-gradient(135deg, #6366F1, #4F46E5);
  color: var(--color-text);
  font-size: 14px;
  font-weight: 800;
  padding: 13px 22px;
  border-radius: 12px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.btn-action-outline {
  background: rgba(16, 185, 129, 0.12);
  color: #34D399;
  border: 1px solid rgba(16, 185, 129, 0.3);
  font-size: 14px;
  font-weight: 700;
  padding: 13px 22px;
  border-radius: 12px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

/* â”FCFAâ”FCFA How it Works Section â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.how-it-works-section { margin: 60px 0; }
.how-header { text-align: center; margin-bottom: 40px; }
.micro-badge {
  background: rgba(99, 102, 241, 0.15);
  color: #A5B4FC;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.08em;
  padding: 5px 14px;
  border-radius: 999px;
  display: inline-block;
  margin-bottom: 12px;
  border: 1px solid rgba(99, 102, 241, 0.3);
}
.how-header h2 { font-size: clamp(1.8rem, 4vw, 2.4rem); font-weight: 900; color: var(--color-text); margin: 0 0 10px; }
.how-header p { color: var(--color-text-muted); font-size: 15px; margin: 0; }

.how-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}
.how-card {
  padding: 34px 28px;
  position: relative;
  border: 1px solid rgba(255, 255, 255, 0.07);
}
.how-number {
  position: absolute;
  top: 20px;
  right: 24px;
  font-size: 38px;
  font-weight: 900;
  color: rgba(255, 255, 255, 0.05);
  font-family: monospace;
}
.how-icon-box {
  width: 50px;
  height: 50px;
  border-radius: 14px;
  background: rgba(245, 192, 39, 0.12);
  color: #F5C027;
  border: 1px solid rgba(245, 192, 39, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}
.how-card h3 { font-size: 18px; font-weight: 800; color: var(--color-text); margin: 0 0 10px; }
.how-card p { color: var(--color-text-muted); font-size: 13.5px; line-height: 1.65; margin: 0; }

/* â”FCFAâ”FCFA FAQ Section â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.faq-accordion-section { margin-bottom: 70px; }
.faq-card { padding: 44px; }
.faq-head { text-align: center; margin-bottom: 34px; }
.faq-head h2 { font-size: 24px; font-weight: 900; color: var(--color-text); margin: 8px 0 8px; }
.faq-head p { color: var(--color-text-muted); font-size: 14.5px; margin: 0; }

.faq-rows { display: flex; flex-direction: column; gap: 12px; }
.faq-row {
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  border-radius: 14px;
  padding: 20px 24px;
  cursor: pointer;
  transition: all 0.2s;
}
.faq-row:hover { background: rgba(255, 255, 255, 0.06); border-color: rgba(255, 255, 255, 0.14); }
.faq-row.active { border-color: rgba(99, 102, 241, 0.45); background: rgba(99, 102, 241, 0.08); }
.faq-q { display: flex; justify-content: space-between; align-items: center; font-size: 15.5px; font-weight: 800; color: var(--color-text); }
.faq-arrow { color: var(--color-text-muted); transition: transform 0.25s ease; }
.faq-row.active .faq-arrow { transform: rotate(180deg); color: #F5C027; }
.faq-a { margin-top: 14px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.07); color: var(--color-text-muted); font-size: 14px; line-height: 1.7; }

/* â”FCFAâ”FCFA Utility & Micro-Animations â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.btn-spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: var(--color-text);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  display: inline-block;
}
@keyframes spin { to { transform: rotate(360deg); } }

.fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

.slide-down { animation: slideDown 0.3s ease-out; }
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-6px); }
  to { opacity: 1; transform: translateY(0); }
}

.text-gold { color: #F5C027 !important; }
.text-emerald { color: #10B981 !important; }

/* â”FCFAâ”FCFA Print Styles â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
@media print {
  body { background: #FFFFFF !important; color: #000000 !important; }
  .no-print, .tracking-hero, .workflow-section, .faq-accordion-section, .fx-mesh { display: none !important; }
  .project-master-card { border: 2px solid #000000 !important; background: #FFFFFF !important; color: #000000 !important; box-shadow: none !important; }
  .spotlight-title, .dossier-ref, .node-heading, .core-value { color: #000000 !important; }
}

/* â”FCFAâ”FCFA Responsive Mobile â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
@media (max-width: 680px) {
  .command-search-card { padding: 22px 18px; }
  .search-input-group { flex-direction: column; }
  .search-submit-btn { width: 100%; justify-content: center; }
  .project-master-card { padding: 22px 16px; }
  .action-command-toolbar { flex-direction: column; }
  .cmd-btn { width: 100%; }
  .faq-card { padding: 22px 16px; }
}
</style>

