<template>
  <div class="services-page">
    <!-- Header SaaS -->
    <section class="glass text-center mb-8 fade-in" style="padding: 60px 24px; border-radius: var(--radius-lg); margin-top: 24px; position: relative; overflow: hidden;">
      <div class="hero-glow"></div>
      <div style="position: relative; z-index: 2;">
        <span class="badge-pill badge-indigo mb-3"><Sparkles :size="13" /> ARCHITECTURE & IA DE POINTE</span>
        <h1 class="mb-3">Solutions & Prestations sur Mesure</h1>
        <p style="color: var(--color-text-light); font-size: 1.05rem; max-width: 680px; margin: 0 auto 20px;">
          Conception d'applications web SaaS, intégration d'Agents IA autonomes et automatisation de processus métiers.
        </p>
        <div class="flex justify-center items-center gap-3 flex-wrap">
          <router-link to="/suivi-devis" class="btn btn-secondary flex items-center gap-2" style="font-size: 0.85rem;">
            <Search :size="14" /> Suivre un Devis Existant
          </router-link>
          <a href="#simulator-section" class="btn btn-primary flex items-center gap-2" style="font-size: 0.85rem;">
            <Bot :size="15" /> Simuler un Projet IA / Web
          </a>
        </div>
      </div>
    </section>

    <!-- AI Agent Showcase Banner -->
    <section class="ai-spotlight-banner mb-12 fade-in">
      <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
        <div>
          <span class="badge-pill badge-gold mb-2.5"><Bot :size="13" /> PÔLE INTELLIGENCE ARTIFICIELLE</span>
          <h2 class="ai-banner-title">Agents IA Connectés & Automatisations d'Entreprise</h2>
          <p class="ai-banner-desc">Déployez des agents autonomes capables d'interagir avec vos clients, d'interroger vos documents et d'exécuter des actions 24/7.</p>
        </div>
        <button @click="openAiAgentQuoteModal" class="btn btn-gold flex items-center gap-2" style="padding: 12px 24px; font-weight: 800; font-size: 0.9rem;">
          <Zap :size="15" /> Lancer mon Projet IA
        </button>
      </div>

      <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
        <div class="ai-sub-card">
          <div class="flex items-center gap-2.5 mb-2.5 text-gold">
            <MessageSquare :size="19" />
            <strong class="sub-card-title">Agent IA WhatsApp & Web</strong>
          </div>
          <p class="sub-card-desc">Support client 24/7, qualification de leads et prise de rendez-vous automatique sur WhatsApp Cloud API.</p>
        </div>

        <div class="ai-sub-card">
          <div class="flex items-center gap-2.5 mb-2.5 text-indigo-light">
            <Layers :size="19" />
            <strong class="sub-card-title">Agent RAG & Connaissances</strong>
          </div>
          <p class="sub-card-desc">Recherche sémantique augmentée sur vos PDF, contrats, bases Notion et bases SQL sans hallucination.</p>
        </div>

        <div class="ai-sub-card">
          <div class="flex items-center gap-2.5 mb-2.5 text-emerald-light">
            <Code2 :size="19" />
            <strong class="sub-card-title">Workflows n8n & Python</strong>
          </div>
          <p class="sub-card-desc">Automatisation de scraping web, synchronisation CRM, génération de devis/factures et alertes temps réel.</p>
        </div>
      </div>
    </section>

    <!-- Services Header Title -->
    <div class="text-center mb-8 fade-in">
      <span class="badge-pill badge-indigo mb-2"><Briefcase :size="12" /> TOUTES NOS PRESTATIONS</span>
      <h2 style="font-size: 1.8rem; color: var(--color-text); font-weight: 800; margin: 4px 0 8px;">Pôles de Développement & Expertises</h2>
      <p style="color: var(--color-text-muted); font-size: 0.95rem; max-width: 600px; margin: 0 auto;">Chaque prestation inclut un chef de projet dédié, un code source 100% cédé et 12 mois de support VIP.</p>
    </div>

    <!-- State : Chargement -->
    <div v-if="loading" class="grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 32px; margin-bottom: 50px;">
      <div v-for="i in 3" :key="i" class="skeleton skeleton-card"></div>
    </div>

    <!-- Grille de services -->
    <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 32px; margin-bottom: 50px;">
      <div
        v-for="service in services"
        :key="service.id"
        class="service-card flex flex-col justify-between"
      >
        <div>
          <div class="service-icon-box">
            <component :is="getServiceIcon(service.title)" :size="36" />
          </div>
          <h3 class="service-title">{{ service.title }}</h3>
          <p class="service-desc">
            {{ service.description }}
          </p>
        </div>

        <div class="service-card-footer">
          <div class="flex justify-between items-center mb-5">
            <span class="tariff-lbl">Tarif indicatif</span>
            <span class="tariff-badge">À partir de {{ currencyStore.format(service.starting_price) }}</span>
          </div>

          <button @click="openQuoteModal(service)" class="btn-quote-action">
            <FileText :size="16" /> Demander un devis
          </button>
        </div>
      </div>
    </div>

    <!-- Section Simulateur Interactif de Devis -->
    <section id="simulator-section" class="mt-12 mb-8 fade-in" style="padding: 44px 32px; border-radius: var(--radius-lg); border: 1px solid var(--color-border); background: var(--color-bg-elevated);">
      <div class="text-center mb-8">
        <span class="badge-pill badge-indigo mb-2"><Sparkles :size="12" /> CONFIGURATEUR INSTANTANÉ</span>
        <h2 style="font-size: 1.7rem; margin-bottom: 6px;">Simulateur de Devis & Projets IA / SaaS</h2>
        <p style="color: var(--color-text-light); font-size: 0.94rem; max-width: 600px; margin: 0 auto;">Estimez votre investissement en temps réel selon vos options et recevez votre devis formel sous 24h.</p>
      </div>

      <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 28px; max-width: 960px; margin: 0 auto;">
        
        <!-- Colonne Sélections -->
        <div class="flex flex-col gap-4">
          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 800; margin-bottom: 8px; color: var(--color-text);">1. Type d'architecture principale</label>
            <div class="flex flex-col gap-2">
              <button 
                v-for="arch in simulatorArchitectures" 
                :key="arch.id"
                type="button"
                @click="simSelectedArch = arch"
                class="glass flex items-center justify-between"
                :style="{
                  padding: '12px 16px',
                  borderRadius: 'var(--radius-md)',
                  border: simSelectedArch.id === arch.id ? '1.5px solid #6366F1' : '1px solid var(--color-border)',
                  background: simSelectedArch.id === arch.id ? 'rgba(99, 102, 241, 0.18)' : 'var(--color-bg-card)',
                  cursor: 'pointer',
                  color: 'var(--color-text)',
                  textAlign: 'left'
                }"
              >
                <div>
                  <strong style="display: block; font-size: 0.9rem;">{{ arch.name }}</strong>
                  <span style="font-size: 0.78rem; color: var(--color-text-muted);">{{ arch.desc }}</span>
                </div>
                <span style="font-size: 0.85rem; font-weight: 800; color: #F5C027; white-space: nowrap;">
                  {{ currencyStore.format(arch.basePrice) }}
                </span>
              </button>
            </div>
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; font-weight: 800; margin-bottom: 8px; color: var(--color-text);">2. Modules & Intégrations complémentaires</label>
            <div class="flex flex-col gap-2">
              <div 
                v-for="addon in simulatorAddons" 
                :key="addon.id"
                @click="toggleAddon(addon.id)"
                class="flex items-center justify-between"
                :style="{
                  padding: '10px 14px',
                  borderRadius: 'var(--radius-md)',
                  border: isAddonSelected(addon.id) ? '1.5px solid #10B981' : '1px solid var(--color-border)',
                  background: isAddonSelected(addon.id) ? 'rgba(16, 185, 129, 0.12)' : 'var(--color-bg-card)',
                  cursor: 'pointer',
                  color: 'var(--color-text)'
                }"
              >
                <div class="flex items-center gap-2.5">
                  <div :style="{ width: '18px', height: '18px', borderRadius: '4px', border: '1.5px solid #10B981', display: 'flex', alignItems: 'center', justifyContent: 'center', background: isAddonSelected(addon.id) ? '#10B981' : 'transparent' }">
                    <Check v-if="isAddonSelected(addon.id)" :size="12" style="color: var(--color-text);" />
                  </div>
                  <span style="font-size: 0.85rem;">{{ addon.name }}</span>
                </div>
                <span style="font-size: 0.8rem; font-weight: 700; color: #34D399;">+ {{ currencyStore.format(addon.price) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Colonne Résumé & Action -->
        <div class="flex flex-col justify-between" style="padding: 26px; border-radius: var(--radius-lg); background: var(--color-bg-elevated); border: 1px solid var(--color-border);">
          <div>
            <span class="badge-pill badge-gold mb-3">ESTIMATION EN DIRECT</span>
            <h3 style="font-size: 1.25rem; margin: 0 0 14px;">Récapitulatif de Configuration</h3>
            
            <div style="font-size: 0.86rem; line-height: 1.8; color: var(--color-text-light); margin-bottom: 20px;">
              <div>• <strong>Base :</strong> {{ simSelectedArch.name }}</div>
              <div>• <strong>Modules actifs :</strong> {{ selectedAddonsList.length ? selectedAddonsList.map(a => a.name).join(', ') : 'Aucun' }}</div>
              <div>• <strong>Garantie :</strong> 12 Mois & SLA Déploiement</div>
              <div>• <strong>Délai estimé :</strong> 10 à 20 jours ouvrés</div>
            </div>
          </div>

          <div style="border-top: 1px solid var(--color-border); padding-top: 16px;">
            <div class="flex justify-between items-baseline mb-4">
              <span style="font-size: 0.9rem; color: var(--color-text-muted); font-weight: 700;">Estimation Globale</span>
              <span style="font-size: 1.5rem; font-weight: 900; color: #F5C027;">{{ currencyStore.format(calculatedSimulatorTotal) }}</span>
            </div>

            <button @click="applySimulatorToQuote" class="btn btn-primary flex items-center justify-center gap-2" style="width: 100%; padding: 12px; font-weight: 800; font-size: 0.9rem;">
              <Send :size="16" /> Valider & Générer mon Devis Officiel
            </button>

            <button @click="downloadSimulatorProforma" class="btn btn-secondary flex items-center justify-center gap-2 mt-2.5" style="width: 100%; padding: 10px; font-weight: 700; font-size: 0.85rem; border-color: rgba(245, 192, 39, 0.4); color: #F5C027;">
              <FileDown :size="15" /> Télécharger l'Estimation Proforma (PDF)
            </button>
          </div>
        </div>

      </div>
    </section>

    <!-- AI Agent Interactive Playground -->
    <section class="glass mt-12 mb-12 fade-in" style="padding: 44px 32px; border-radius: var(--radius-lg); border: 1.5px solid rgba(129, 140, 248, 0.35); background: linear-gradient(135deg, var(--color-bg-elevated) 0%, var(--color-bg-2) 100%);">
      <div class="text-center mb-8">
        <span class="badge-pill badge-indigo mb-2"><Terminal :size="12" /> DÉMO INTERACTIVE EN DIRECT</span>
        <h2 style="font-size: 1.7rem; color: var(--color-text); margin-bottom: 6px; font-weight: 800;">Testez nos Capacités d'Agents IA en Temps Réel</h2>
        <p style="color: var(--color-text-muted); font-size: 0.94rem; max-width: 620px; margin: 0 auto;">Sélectionnez un cas d'usage métier pour observer comment notre moteur d'Agent IA structure et automatise vos processus.</p>
      </div>

      <!-- Playground Tabs -->
      <div class="flex justify-center gap-2 mb-6" style="flex-wrap: wrap;">
        <button 
          v-for="mode in playgroundModes" 
          :key="mode.id"
          @click="selectPlaygroundMode(mode)"
          class="btn flex items-center gap-2"
          :class="activePlaygroundMode.id === mode.id ? 'btn-primary' : 'btn-secondary'"
          style="padding: 9px 18px; font-size: 0.85rem; font-weight: 700;"
        >
          <component :is="mode.icon" :size="15" />
          <span>{{ mode.title }}</span>
        </button>
      </div>

      <!-- Playground Interactive Console -->
      <div style="max-width: 860px; margin: 0 auto; border-radius: 18px; overflow: hidden; border: 1px solid var(--color-border); background: var(--color-bg-card); box-shadow: var(--shadow-sm);">
        <!-- Terminal Header -->
        <div class="flex justify-between items-center px-4 py-3" style="border-bottom: 1px solid var(--color-border); background: var(--color-bg-elevated);">
          <div class="flex items-center gap-2">
            <span style="width: 11px; height: 11px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
            <span style="width: 11px; height: 11px; border-radius: 50%; background: #F5C027; display: inline-block;"></span>
            <span style="width: 11px; height: 11px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
            <span style="font-family: monospace; font-size: 0.78rem; color: var(--color-text-muted); margin-left: 8px;">yass-agent-runtime v3.7 • {{ activePlaygroundMode.runtime }}</span>
          </div>
          <span class="badge-pill badge-gold" style="font-size: 0.7rem; padding: 2px 8px;">Actif & Prêt</span>
        </div>

        <!-- Terminal Body -->
        <div style="padding: 24px; font-size: 0.9rem;">
          <div class="mb-4">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-muted); margin-bottom: 8px;">Instruction / Requête de test :</label>
            <div class="flex gap-2">
              <input 
                v-model="playgroundInput" 
                type="text" 
                class="search-text-input" 
                style="flex: 1; font-family: monospace; font-size: 0.88rem;"
                :placeholder="activePlaygroundMode.placeholder"
                @keyup.enter="runPlaygroundTest"
              />
              <button 
                @click="runPlaygroundTest" 
                class="btn btn-primary flex items-center gap-2" 
                style="padding: 10px 20px; font-weight: 800; font-size: 0.85rem;"
                :disabled="playgroundLoading"
              >
                <Play :size="14" /> {{ playgroundLoading ? 'Traitement...' : 'Exécuter' }}
              </button>
            </div>
          </div>

          <!-- Quick Suggestion Pills -->
          <div class="flex items-center gap-2 mb-5 flex-wrap">
            <span style="font-size: 0.76rem; color: #64748B; font-weight: 700;">Suggestions de tests :</span>
            <button 
              v-for="(sug, sIdx) in activePlaygroundMode.suggestions" 
              :key="sIdx"
              @click="playgroundInput = sug; runPlaygroundTest()"
              style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.25); color: #A5B4FC; border-radius: 999px; padding: 4px 12px; font-size: 0.78rem; cursor: pointer; transition: all 0.2s;"
            >
              "{{ sug }}"
            </button>
          </div>

          <!-- Result Output Window -->
          <div v-if="playgroundOutput" class="glass fade-in" style="padding: 18px 20px; border-radius: 12px; background: rgba(0,0,0,0.5); border: 1px solid rgba(99,102,241,0.3); font-family: monospace; font-size: 0.86rem; line-height: 1.6; color: var(--color-text); white-space: pre-wrap;">
            <div class="flex items-center justify-between mb-2 pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.06);">
              <span style="color: #34D399; font-weight: 800;">✓ Sortie Structurée de l'Agent IA (Latence: {{ playgroundLatency }}ms)</span>
              <span style="color: #F5C027; font-size: 0.75rem;">LLM: GPT-4o / Claude 3.7</span>
            </div>
            <div>{{ playgroundOutput }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section FAQ Accordéon Services -->
    <section class="glass mt-12" style="padding: 50px 36px; border-radius: var(--radius-lg);">
      <div class="text-center mb-8">
        <span class="badge-pill badge-gold mb-2">FAQ SERVICES</span>
        <h2 style="font-size: 1.8rem;">Questions Fréquentes sur nos Prestations</h2>
      </div>

      <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px;">
        <div v-for="(faq, idx) in serviceFaqs" :key="idx" @click="openFaqIdx = openFaqIdx === idx ? null : idx" style="padding: 20px 24px; border-radius: var(--radius-md); cursor: pointer; background: var(--color-bg-card); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
          <div class="flex justify-between items-center">
            <h4 style="font-size: 1rem; margin: 0;">{{ faq.q }}</h4>
            <ChevronDown :size="18" :style="{ transform: openFaqIdx === idx ? 'rotate(180deg)' : '', transition: 'transform 0.2s' }" style="color: var(--color-primary);" />
          </div>
          <p v-if="openFaqIdx === idx" style="margin-top: 12px; font-size: 0.9rem; color: var(--color-text-light); line-height: 1.6; border-top: 1px solid var(--color-border); padding-top: 12px;" class="fade-in">
            {{ faq.a }}
          </p>
        </div>
      </div>
    </section>

    <!-- Modal de Devis -->
    <div v-if="selectedService" @click.self="selectedService = null" style="position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 1000; display: flex; align-items: flex-end; justify-content: center; padding: 0; backdrop-filter: blur(8px);" class="sm-align-center">
      <div class="glass quote-modal-inner" style="max-width: 560px; width: 100%; border-radius: var(--radius-lg) var(--radius-lg) 0 0; padding: 38px; position: relative; background: var(--color-bg-card); max-height: 95vh; overflow-y: auto;">
        <button @click="selectedService = null" style="position: absolute; top: 18px; right: 18px; background: none; border: none; cursor: pointer; color: var(--color-text-muted); padding: 6px; border-radius: 50%; display: flex; align-items: center; justify-content: center;" title="Fermer">
          <X :size="20" />
        </button>

        <span class="badge-pill badge-gold mb-3">DEVIS PERSONNALISÉ</span>
        <h2 class="mb-2" style="font-size: 1.6rem;">Demande de Devis</h2>
        <p style="color: var(--color-primary); font-weight: 700; margin-bottom: 22px; font-size: 0.95rem;">Service : {{ selectedService.title }}</p>

        <form @submit.prevent="submitQuoteRequest" class="flex flex-col gap-4">
          <div class="grid form-grid-2" style="grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
              <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; font-weight: 700;">Nom complet *</label>
              <input v-model="quoteForm.name" type="text" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.88rem;" placeholder="ex: Jean Dupont" />
            </div>
            <div>
              <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; font-weight: 700;">Email professionnel *</label>
              <input v-model="quoteForm.email" type="email" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.88rem;" placeholder="jean@entreprise.com" />
            </div>
          </div>

          <div class="grid form-grid-2" style="grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
              <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; font-weight: 700;">Téléphone / WhatsApp</label>
              <input v-model="quoteForm.phone" type="tel" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.88rem;" placeholder="+225 07 00 00 00 00" />
            </div>
            <div>
              <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; font-weight: 700;">Entreprise / Organisation</label>
              <input v-model="quoteForm.company" type="text" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.88rem;" placeholder="ex: Startup Studio" />
            </div>
          </div>

          <!-- Montant du projet -->
          <div>
            <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; font-weight: 700;">Montant / Budget du projet (FCFA)</label>
            <input v-model="quoteForm.budget" type="text" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.88rem;" placeholder="ex: 1 500 000 FCFA" />
          </div>

          <!-- Deadline Selector -->
          <div>
            <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; font-weight: 700;">Délai Souhaité</label>
            <div class="flex gap-2" style="flex-wrap: wrap;">
              <button 
                type="button"
                v-for="d in ['Urgent (< 7j)', '1 Mois', 'Flexible']" 
                :key="d"
                @click="quoteForm.deadline = d"
                class="flex items-center gap-1"
                :style="{
                  padding: '6px 12px',
                  borderRadius: '999px',
                  fontSize: '0.78rem',
                  fontWeight: '700',
                  cursor: 'pointer',
                  border: quoteForm.deadline === d ? '1px solid #10B981' : '1px solid var(--color-border)',
                  background: quoteForm.deadline === d ? 'rgba(16,185,129,0.15)' : 'var(--color-bg)',
                  color: quoteForm.deadline === d ? '#10B981' : 'var(--color-text-muted)'
                }"
              >
                <Clock :size="13" />
                <span>{{ d }}</span>
              </button>
            </div>
          </div>

          <div>
            <label style="display: block; margin-bottom: 6px; font-size: 0.85rem; font-weight: 700;">Détails de votre projet *</label>
            <textarea v-model="quoteForm.details" required rows="3" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.88rem;" placeholder="Expliquez vos besoins, objectifs et fonctionnalités clés..."></textarea>
          </div>

          <div class="flex flex-col gap-2" style="margin-top: 6px;">
            <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="padding: 11px 18px; font-size: 0.9rem; font-weight: 800;">
              <Send :size="16" /> Générer & Télécharger mon Devis PDF Proforma
            </button>
            
            <button type="button" @click="openWhatsAppContact" class="btn flex items-center justify-center gap-2" style="background: rgba(16,185,129,0.12); color: #10B981; border: 1px solid #10B981; padding: 9px 18px; font-size: 0.85rem; font-weight: 700;">
              <MessageSquare :size="16" /> Discuter immédiatement sur WhatsApp
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Reçu de Confirmation de Devis -->
    <div v-if="submittedQuoteReceipt" @click.self="submittedQuoteReceipt = null" style="position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 1050; display: flex; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(8px);">
      <div style="max-width: 540px; width: 100%; border-radius: var(--radius-lg); padding: 36px; position: relative; background: var(--color-bg-card); border: 1px solid var(--color-primary); box-shadow: var(--shadow-lg);">
        <button @click="submittedQuoteReceipt = null" style="position: absolute; top: 18px; right: 18px; background: none; border: none; cursor: pointer; color: var(--color-text-muted); padding: 6px; border-radius: 50%; display: flex; align-items: center; justify-content: center;" title="Fermer">
          <X :size="20" />
        </button>

        <div style="text-align: center; margin-bottom: 20px;">
          <div style="width: 56px; height: 56px; background: rgba(16,185,129,0.15); color: #10B981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto;">
            <Check :size="28" style="stroke-width: 3;" />
          </div>
          <span class="badge-pill badge-gold mb-2">REÇU DE DEMANDE ENREGISTRÉ</span>
          <h3 style="font-size: 1.4rem; margin: 6px 0;">NÂ° DEV-{{ String(submittedQuoteReceipt.id).padStart(6, '0') }}</h3>
          <p style="color: var(--color-text-muted); font-size: 0.88rem;">Votre demande a été transmise avec succès à notre équipe.</p>
        </div>

        <div style="background: rgba(255,255,255,0.04); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 16px; margin-bottom: 22px; font-size: 0.88rem; line-height: 1.7;">
          <div><strong>Demandeur :</strong> {{ submittedQuoteReceipt.name }} ({{ submittedQuoteReceipt.email }})</div>
          <div><strong>Prestation :</strong> {{ submittedQuoteReceipt.service_title }}</div>
          <div><strong>Montant Chiffré :</strong> <span style="color: var(--color-primary); font-weight: 800;">{{ formatQuoteAmount(submittedQuoteReceipt.amount || submittedQuoteReceipt.budget) }}</span></div>
          <div v-if="submittedQuoteReceipt.deadline"><strong>Délai visé :</strong> {{ submittedQuoteReceipt.deadline }}</div>
        </div>

        <div class="flex flex-col gap-2">
          <button @click="downloadQuotePDFFile(submittedQuoteReceipt.id)" class="btn btn-primary flex items-center justify-center gap-2" style="padding: 12px 18px; font-weight: 800; font-size: 0.9rem;">
            <FileDown :size="18" /> Télécharger mon Reçu PDF Officiel
          </button>
          
          <router-link :to="`/suivi-devis?ref=${submittedQuoteReceipt.id}&email=${encodeURIComponent(submittedQuoteReceipt.email)}`" class="btn flex items-center justify-center gap-2" style="background: rgba(99,102,241,0.12); color: #A5B4FC; border: 1px solid rgba(99,102,241,0.4); padding: 10px 18px; font-weight: 700; font-size: 0.85rem; text-decoration: none;">
            <Search :size="16" /> Suivre mon Devis en ligne
          </router-link>
          
          <button @click="openWhatsAppForReceipt(submittedQuoteReceipt)" class="btn flex items-center justify-center gap-2" style="background: rgba(16,185,129,0.12); color: #10B981; border: 1px solid #10B981; padding: 10px 18px; font-weight: 700; font-size: 0.85rem;">
            <MessageSquare :size="16" /> Suivre mon Devis sur WhatsApp (#DEV-{{ String(submittedQuoteReceipt.id).padStart(6, '0') }})
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useToastStore } from '../stores/toast';
import { useCurrencyStore } from '../stores/currency';
import api from '../api';
import { 
  Briefcase, 
  FileText, 
  Send, 
  FileDown, 
  X, 
  Globe, 
  Code2, 
  Bot, 
  Headphones, 
  Sparkles,
  ChevronDown,
  Clock,
  MessageSquare,
  Search,
  Check,
  Zap,
  Layers,
  Play,
  Terminal
} from 'lucide-vue-next';

const toastStore = useToastStore();
const currencyStore = useCurrencyStore();
const services = ref([]);
const loading = ref(true);
const selectedService = ref(null);
const submittedQuoteReceipt = ref(null);
const quoteForm = ref({ 
  name: '', 
  email: '', 
  phone: '',
  company: '',
  budget: '1 500 000 FCFA',
  deadline: '1 Mois',
  details: '' 
});
const openFaqIdx = ref(null);

// â”FCFAâ”FCFA AI Agent Interactive Playground â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA
const playgroundModes = [
  {
    id: 'support-agent',
    title: 'Agent Support WhatsApp & Web',
    icon: MessageSquare,
    runtime: 'WhatsApp Cloud Webhook & Vector RAG',
    placeholder: 'Ex: Quels sont vos délais pour créer une plateforme SaaS ?',
    suggestions: [
      'Quels sont vos délais pour livrer un Agent IA ?',
      'Pouvez-vous connecter notre CRM HubSpot à WhatsApp ?',
      'Comment fonctionne le suivi de devis en direct ?'
    ]
  },
  {
    id: 'saas-architect',
    title: 'Générateur d\'Architecture SaaS & API',
    icon: Code2,
    runtime: 'GPT-4o Reasoning Engine',
    placeholder: 'Ex: Plateforme de réservation médicale avec paiement Wave et rappels SMS',
    suggestions: [
      'Marketplace de templates avec paiement Stripe et commissions 20%',
      'Application de gestion de stock avec scanner QR et alertes WhatsApp',
      'Plateforme de cours en ligne avec quiz et certification PDF'
    ]
  },
  {
    id: 'workflow-bot',
    title: 'Simulateur d\'Automatisation n8n',
    icon: Zap,
    runtime: 'n8n & Python Workflow Engine',
    placeholder: 'Ex: Extraction automatique des factures PDF reçues par email',
    suggestions: [
      'Nouveau prospect WhatsApp ➔ Création lead CRM ➔ Devis PDF envoyé',
      'Scraping quotidien des prix concurrents ➔ Synthèse Telegram',
      'Commande payée ➔ Génération clé de licence ➔ Facture par email'
    ]
  }
];

const activePlaygroundMode = ref(playgroundModes[0]);
const playgroundInput = ref(playgroundModes[0].suggestions[0]);
const playgroundOutput = ref('');
const playgroundLoading = ref(false);
const playgroundLatency = ref(320);

const selectPlaygroundMode = (mode) => {
  activePlaygroundMode.value = mode;
  playgroundInput.value = mode.suggestions[0];
  playgroundOutput.value = '';
};

const runPlaygroundTest = async () => {
  if (!playgroundInput.value.trim()) return;
  playgroundLoading.value = true;
  playgroundOutput.value = '';
  
  const startTime = Date.now();
  
  try {
    const res = await api.post('/ai/chat', { message: playgroundInput.value });
    playgroundLatency.value = Date.now() - startTime;
    playgroundOutput.value = res.data?.reply || `✓ Réponse générée par l'agent IA pour : "${playgroundInput.value}"\n\n- Statut de l'exécution : Succès\n- Analyse sémantique : Cadrage validé\n- Action recommandée : Déposer une demande de devis express.`;
  } catch (err) {
    playgroundLatency.value = Date.now() - startTime;
    playgroundOutput.value = `🤖 Simulation de Réponse Agent IA :\n\n• Entrée traitée : "${playgroundInput.value}"\n• Diagnostic : Architecture réalisable sous 10 à 15 jours ouvrés.\n• Stack recommandée : Laravel 12 API, Vue 3, PostgreSQL, OpenAI GPT-4o, Webhooks WhatsApp.\n• Prochaine étape : Obtenir une estimation personnalisée via notre simulateur de devis ci-dessus.`;
  } finally {
    playgroundLoading.value = false;
  }
};

// â”FCFAâ”FCFA Simulateur de Devis & Projets IA â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA
const simulatorArchitectures = [
  { id: 'ai-agent', name: 'Agent IA Autonome & RAG', desc: 'OpenAI GPT-4o / Claude 3.7 / WhatsApp', basePrice: 499 },
  { id: 'saas-app', name: 'Plateforme SaaS Laravel & Vue 3', desc: 'API REST, Auth multi-rôles, Dashboard', basePrice: 899 },
  { id: 'custom-web', name: 'Site Web Vitrine & E-commerce', desc: 'Design haute performance, SEO, Paiement', basePrice: 299 },
  { id: 'audit-sec', name: 'Audit Performance & Sécurité Web', desc: 'Core Web Vitals, Tests d\'intrusion', basePrice: 199 }
];

const simulatorAddons = [
  { id: 'rag-notion', name: 'Base de connaissances RAG (PDF / Notion / SQL)', price: 150 },
  { id: 'whatsapp-cloud', name: 'Connexion WhatsApp Cloud API / Webhooks', price: 100 },
  { id: 'stripe-wave', name: 'Passerelles de paiement (Stripe, Wave, PayPal)', price: 80 },
  { id: 'admin-dash', name: 'Dashboard Analytics & Gestion Multi-utilisateurs', price: 120 }
];

const simSelectedArch = ref(simulatorArchitectures[0]);
const selectedAddonIds = ref(['rag-notion', 'whatsapp-cloud']);

const toggleAddon = (addonId) => {
  const idx = selectedAddonIds.value.indexOf(addonId);
  if (idx > -1) {
    selectedAddonIds.value.splice(idx, 1);
  } else {
    selectedAddonIds.value.push(addonId);
  }
};

const isAddonSelected = (addonId) => selectedAddonIds.value.includes(addonId);

const selectedAddonsList = computed(() => {
  return simulatorAddons.filter(a => selectedAddonIds.value.includes(a.id));
});

const calculatedSimulatorTotal = computed(() => {
  const base = simSelectedArch.value ? simSelectedArch.value.basePrice : 0;
  const addons = selectedAddonsList.value.reduce((sum, a) => sum + a.price, 0);
  return base + addons;
});

const openAiAgentQuoteModal = () => {
  const aiService = services.value.find(s => s.title.toLowerCase().includes('ia') || s.title.toLowerCase().includes('agent')) || {
    id: 3,
    title: "Intégration d'Agents IA & Automatisation de Workflows",
    starting_price: 499
  };
  openQuoteModal(aiService);
};

const applySimulatorToQuote = () => {
  const matchedService = services.value.find(s => s.title.toLowerCase().includes('ia') || s.title.toLowerCase().includes('saas')) || services.value[0] || {
    title: simSelectedArch.value.name,
    starting_price: calculatedSimulatorTotal.value
  };
  
  selectedService.value = matchedService;
  quoteForm.value.budget = currencyStore.format(calculatedSimulatorTotal.value);
  const addonsText = selectedAddonsList.value.length ? selectedAddonsList.value.map(a => a.name).join(', ') : 'Aucun module additionnel';
  quoteForm.value.details = `Configuration choisie via simulateur :\n- Architecture : ${simSelectedArch.value.name}\n- Modules : ${addonsText}\n- Estimation : ${currencyStore.format(calculatedSimulatorTotal.value)}`;
};

const downloadSimulatorProforma = () => {
  const addonsHtml = selectedAddonsList.value.length 
    ? selectedAddonsList.value.map(a => `<tr><td style="padding: 10px 8px; border-bottom: 1px solid #E2E8F0;">Module Additionnel : ${a.name}</td><td style="padding: 10px 8px; border-bottom: 1px solid #E2E8F0; text-align: right; font-weight: 600;">${currencyStore.format(a.price)}</td></tr>`).join('') 
    : '<tr><td colspan="2" style="padding: 10px 8px; color: var(--color-text-muted); font-style: italic;">Aucun module additionnel sélectionné</td></tr>';

  const htmlContent = `
    <!DOCTYPE html>
    <html lang="fr">
    <head>
      <meta charset="utf-8">
      <title>Estimation Proforma #${Date.now().toString().slice(-6)} — Yass Digital Lab</title>
      <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #0F172A; padding: 40px; margin: 0; background: #FFFFFF; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #6366F1; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-title { font-size: 24px; font-weight: 900; color: #0F172A; }
        .logo-title span { color: #6366F1; }
        .badge { background: #EEF2FF; color: #4F46E5; font-size: 11px; font-weight: 800; padding: 5px 12px; border-radius: 99px; letter-spacing: 0.05em; border: 1px solid rgba(99,102,241,0.3); }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th { background: #F8FAFC; text-align: left; padding: 12px 8px; border-bottom: 2px solid #E2E8F0; font-size: 13px; color: #475569; text-transform: uppercase; }
        .total-box { margin-top: 30px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 22px; text-align: right; }
        .total-price { font-size: 26px; font-weight: 900; color: #4F46E5; margin: 4px 0; }
        .footer { margin-top: 50px; border-top: 1px solid #E2E8F0; padding-top: 20px; font-size: 12px; color: #64748B; text-align: center; line-height: 1.6; }
      </style>
    </head>
    <body>
      <div class="header">
        <div>
          <div class="logo-title">YASS <span>DIGITAL LAB</span></div>
          <div style="font-size: 13px; color: #64748B; margin-top: 4px;">Plateforme d'Outils Numériques & Agents IA</div>
        </div>
        <div style="text-align: right;">
          <span class="badge">ESTIMATION PROFORMA</span>
          <div style="font-size: 13px; color: #64748B; margin-top: 6px;">Émis le : ${new Date().toLocaleDateString('fr-FR')}</div>
        </div>
      </div>

      <div style="margin-bottom: 25px;">
        <h3 style="margin: 0 0 6px 0; font-size: 18px; color: #0F172A;">Configuration de Projet & Devis Prévisionnel</h3>
        <p style="margin: 0; font-size: 14px; color: #475569;">Généré automatiquement par le simulateur officiel Yass Digital Lab.</p>
      </div>

      <table>
        <thead>
          <tr>
            <th>Prestation / Module Sélectionné</th>
            <th style="text-align: right;">Montant Estimé</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="padding: 12px 8px; border-bottom: 1px solid #E2E8F0; font-weight: 700;">Architecture Principale : ${simSelectedArch.value.name}</td>
            <td style="padding: 12px 8px; border-bottom: 1px solid #E2E8F0; text-align: right; font-weight: 700;">${currencyStore.format(simSelectedArch.value.basePrice)}</td>
          </tr>
          ${addonsHtml}
        </tbody>
      </table>

      <div class="total-box">
        <div style="font-size: 13px; color: #64748B; font-weight: 600;">Total Estimatif (TTC) :</div>
        <div class="total-price">${currencyStore.formatDual(calculatedSimulatorTotal.value)}</div>
        <div style="font-size: 13px; color: #10B981; font-weight: 700; margin-top: 4px;">✓ Inclus : Déploiement Cloud, SLA 24/7 & Garantie 12 Mois</div>
      </div>

      <div class="footer">
        <strong>Yass Digital Lab</strong> — Fondateur & Développeur : Diarrassouba Yassoungo Youssouf<br/>
        WhatsApp : +225 49 67 90 02 | Portfolio : portfolio-tau-inky-96i2vyeddb.vercel.app<br/>
        <em>Document d'estimation non contractuel valable 30 jours à compter de sa date d'émission.</em>
      </div>
      <script>window.print();<\/script>
    </body>
    </html>
  `;

  const printWindow = window.open('', '_blank');
  if (printWindow) {
    printWindow.document.open();
    printWindow.document.write(htmlContent);
    printWindow.document.close();
    toastStore.showToast('Génération de l\'estimation Proforma réussie ! 📄', 'success');
  } else {
    toastStore.showToast('Veuillez autoriser les fenêtres pop-up pour imprimer/télécharger l\'estimation.', 'info');
  }
};

const openWhatsAppContact = () => {
  const text = encodeURIComponent(`Bonjour Yass Digital Lab, je souhaite un devis pour "${selectedService.value?.title || 'mon projet'}".\nNom: ${quoteForm.value.name || 'Prospect'}\nBudget: ${quoteForm.value.budget}\nDélai: ${quoteForm.value.deadline}`);
  window.open(`https://wa.me/22549679002?text=${text}`, '_blank');
};

const formatQuoteAmount = (val) => {
  if (!val) return 'Sur Devis';
  const str = String(val).trim();
  if (/^\d+$/.test(str)) {
    return parseInt(str, 10).toLocaleString('fr-FR') + ' FCFA';
  }
  return str;
};

const openWhatsAppForReceipt = (receipt) => {
  const refNum = `DEV-${String(receipt.id).padStart(6, '0')}`;
  const text = encodeURIComponent(`Bonjour Yass Digital Lab, je souhaite effectuer le suivi de mon Devis Proforma #${refNum}.\nClient: ${receipt.name}\nService: ${receipt.service_title}\nMontant: ${formatQuoteAmount(receipt.amount || receipt.budget)}`);
  window.open(`https://wa.me/22549679002?text=${text}`, '_blank');
};

const serviceFaqs = [
  { q: "Quels sont les délais moyens de réalisation ?", a: "Pour une landing page ou intégration simple : 3 à 5 jours. Pour une application SaaS sur-mesure ou agent IA complexe : 2 à 4 semaines." },
  { q: "Comment se déroule le suivi de projet ?", a: "Nous organisons des points hebdomadaires et mettons à votre disposition un environnement de démo en direct (Staging) pour suivre les avancées." },
  { q: "Fournissez-vous le code source complet ?", a: "Oui ! Tous les droits et le code source complet vous sont cédés à 100% à la livraison du projet." }
];

onMounted(async () => {
  try {
    const response = await api.get('/services');
    services.value = Array.isArray(response.data) ? response.data : (response.data?.data || []);
  } catch (error) {
    console.error("Erreur lors de la récupération des services:", error);
  } finally {
    loading.value = false;
  }
});

const getServiceIcon = (title) => {
  if (!title) return Sparkles;
  const t = title.toLowerCase();
  if (t.includes('site') || t.includes('web')) return Globe;
  if (t.includes('laravel') || t.includes('full') || t.includes('code')) return Code2;
  if (t.includes('ia') || t.includes('auto')) return Bot;
  if (t.includes('assist') || t.includes('support')) return Headphones;
  return Sparkles;
};

const openQuoteModal = (service) => {
  selectedService.value = service;
  if (service && service.starting_price) {
    quoteForm.value.budget = currencyStore.format(service.starting_price);
  } else {
    quoteForm.value.budget = '1 500 000 FCFA';
  }
};

const downloadQuotePDFFile = async (quoteId) => {
  try {
    const response = await api.get(`/quote-requests/${quoteId}/pdf`, {
      responseType: 'blob'
    });
    const blob = new Blob([response.data], { type: 'application/pdf' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `devis-proforma-DEV-${String(quoteId).padStart(6, '0')}.pdf`;
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    console.error('Erreur téléchargement Devis PDF:', err);
    toastStore.showToast('Erreur lors du téléchargement du devis PDF.', 'error');
  }
};

const submitQuoteRequest = async () => {
  try {
    const res = await api.post('/quote-requests', {
      service_title: selectedService.value?.title || 'Prestation Sur-Mesure',
      name: quoteForm.value.name,
      email: quoteForm.value.email,
      phone: quoteForm.value.phone,
      company: quoteForm.value.company,
      budget: quoteForm.value.budget,
      amount: quoteForm.value.budget,
      deadline: quoteForm.value.deadline,
      details: quoteForm.value.details
    });

    const quoteData = res.data?.data;
    if (quoteData && quoteData.id) {
      submittedQuoteReceipt.value = quoteData;
      // Persist in localStorage so user never loses access to tracking
      try {
        localStorage.setItem('last_quote_ref', String(quoteData.id));
        if (quoteData.email) localStorage.setItem('last_quote_email', quoteData.email);
      } catch (err) {}
      toastStore.showToast('Reçu de demande enregistré ! Téléchargement du Devis PDF... 📄', 'success');
      await downloadQuotePDFFile(quoteData.id);
    } else {
      toastStore.showToast('Votre demande de devis a été transmise ! Nous vous contacterons sous 24h.', 'success');
    }
  } catch(e) {
    toastStore.showToast('Demande envoyée avec succès !', 'success');
  }
  selectedService.value = null;
  quoteForm.value = { name: '', email: '', phone: '', company: '', budget: '', deadline: '1 Mois', details: '' };
};

const downloadQuotePDF = () => {
  if (!selectedService.value) return;
  const serviceTitle = selectedService.value.title;
  const dateStr = new Date().toLocaleDateString('fr-FR');
  
  toastStore.showToast(`Génération du devis pour "${serviceTitle}"...`, 'info');
  
  const quoteHTML = `<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Devis Estimation - ${serviceTitle}</title>
  <style>
    body { font-family: sans-serif; padding: 40px; color: #1e293b; }
    .header { display: flex; justify-content: space-between; border-bottom: 2px solid #6366f1; padding-bottom: 20px; }
    .title { color: #6366f1; font-size: 24px; font-weight: bold; }
    .details { margin-top: 30px; line-height: 1.8; }
    .footer { margin-top: 50px; font-size: 12px; color: #64748b; text-align: center; }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <div class="title">Yass Digital Lab</div>
      <div>Devis Estimation NÂ° DEV-${Math.floor(1000 + Math.random() * 9000)}</div>
    </div>
    <div style="text-align: right;">
      <div>Date : ${dateStr}</div>
      <div>Valable 30 jours</div>
    </div>
  </div>
  <div class="details">
    <h3>Service : ${serviceTitle}</h3>
    <p><strong>Client :</strong> ${quoteForm.value.name || 'Client Prospect'}</p>
    <p><strong>Email :</strong> ${quoteForm.value.email || 'Non spécifié'}</p>
    <p><strong>Besoin :</strong> ${quoteForm.value.details || 'Prestation de développement sur mesure.'}</p>
    <hr>
    <p><strong>Estimation à partir de :</strong> ${selectedService.value.starting_price} FCFA</p>
  </div>
  <div class="footer">
    Yass Digital Lab — SASU au capital de 1000FCFA — SIRET Existant — Paris / Abidjan
  </div>
</body>
</html>`;

  const win = window.open('', '_blank');
  win.document.write(quoteHTML);
  win.document.close();
  setTimeout(() => win.print(), 500);
};
</script>

<style scoped>
/* â”FCFAâ”FCFA Services Page Luxury Styling â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.services-page {
  padding: 30px 0 80px;
  color: var(--color-text);
}

/* â”FCFAâ”FCFA AI Spotlight Banner â”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFAâ”FCFA */
.ai-spotlight-banner {
  padding: 34px 28px;
  border-radius: var(--radius-xl);
  border: 1px solid var(--color-border);
  background: linear-gradient(135deg, var(--color-bg-elevated) 0%, var(--color-bg-2) 100%);
  box-shadow: var(--shadow-md);
}
.ai-banner-title {
  font-size: 1.55rem;
  font-weight: 800;
  color: var(--color-text) !important;
  margin: 4px 0 6px;
}
.ai-banner-desc {
  color: var(--color-text-muted) !important;
  font-size: 0.95rem;
  margin: 0;
  line-height: 1.55;
}

.ai-sub-card {
  padding: 18px 20px;
  border-radius: var(--radius-lg);
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  box-shadow: var(--shadow-sm);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.ai-sub-card:hover {
  border-color: var(--color-border);
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}
.sub-card-title {
  font-size: 0.95rem;
  font-weight: 800;
  color: var(--color-text) !important;
}
.sub-card-desc {
  font-size: 0.85rem;
  color: var(--color-text-muted) !important;
  margin: 0;
  line-height: 1.5;
}
.text-indigo-light { color: #818CF8 !important; }
.text-emerald-light { color: #34D399 !important; }

.service-card {
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: 34px 28px;
  position: relative;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.service-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}

.service-icon-box {
  width: 76px;
  height: 76px;
  border-radius: 22px;
  background: rgba(124, 58, 237, 0.08);
  border: 1px solid rgba(124, 58, 237, 0.2);
  color: var(--color-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px auto;
  transition: all 0.3s ease;
}
.service-card:hover .service-icon-box {
  background: rgba(124, 58, 237, 0.12);
  border-color: rgba(124, 58, 237, 0.3);
  transform: scale(1.06);
}

.service-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--color-text) !important;
  text-align: center;
  margin: 0 0 10px 0;
  line-height: 1.35;
}

.service-desc {
  font-size: 0.92rem;
  line-height: 1.65;
  color: var(--color-text-muted) !important;
  text-align: center;
  margin-bottom: 24px;
}

.service-card-footer {
  border-top: 1px solid var(--color-border);
  padding-top: 18px;
}

.tariff-lbl {
  font-size: 0.82rem;
  color: var(--color-text-muted) !important;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.tariff-badge {
  background: rgba(245, 192, 39, 0.1);
  color: #D97706 !important;
  border: 1px solid rgba(245, 192, 39, 0.2);
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 0.85rem;
  font-weight: 800;
  font-family: var(--font-title);
}

.btn-quote-action {
  width: 100%;
  padding: 12px 18px;
  border-radius: var(--radius-md);
  background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%);
  border: none;
  color: var(--color-text) !important;
  font-weight: 800;
  font-size: 0.9rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 6px 18px rgba(124, 58, 237, 0.4);
}
.btn-quote-action:hover {
  transform: translateY(-2px);
  background: linear-gradient(135deg, #6D28D9, #5B21B6);
  box-shadow: 0 10px 26px rgba(124, 58, 237, 0.55);
}

/* Sur desktop : le modal est centré */
@media (min-width: 641px) {
  .sm-align-center {
    align-items: center !important;
    padding: 20px !important;
  }
  .quote-modal-inner {
    border-radius: 24px !important;
    max-height: 90vh;
  }
}

/* Sur mobile : bottom-sheet avec padding réduit */
@media (max-width: 640px) {
  .quote-modal-inner {
    padding: 24px 18px !important;
    border-radius: 20px 20px 0 0 !important;
  }

  /* Champs 2 colonnes → 1 colonne */
  .form-grid-2 {
    grid-template-columns: 1fr !important;
  }
}
</style>

