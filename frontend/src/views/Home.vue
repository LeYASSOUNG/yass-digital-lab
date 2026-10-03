<template>
  <div class="home">

    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
         HERO — Mega Immersive Dark Premium
    â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
    <section class="hero fade-in">
      <div class="hero-orb hero-orb-purple"></div>
      <div class="hero-orb hero-orb-cyan"></div>
      <div class="hero-orb hero-orb-gold"></div>
      
      <div class="hero-inner">
        <div class="hero-badge mb-5">
          <span class="hero-badge-dot"></span>
          <span>Écosystème IA & Digital — Actif 24/7</span>
        </div>
        
        <h1 class="hero-title mb-5">
          Des Agents IA & Templates SaaS qui font<br/>
          <span class="gradient-text-hero">gagner des semaines de travail</span>
        </h1>
        
        <p class="hero-sub mb-8">
          Architectures prêtes pour la production. Agents WhatsApp autonomes. Packs de prompts d'élite. Formations exclusives.
        </p>
        
        <div class="hero-cta mb-10">
          <router-link to="/products" class="btn btn-primary" style="font-size: 1rem; padding: 14px 32px; border-radius: 999px; box-shadow: 0 12px 32px rgba(124,58,237,0.45);">
            <ShoppingBag :size="19" /> Explorer le Catalogue <ArrowRight :size="16" />
          </router-link>
          <router-link to="/services" class="btn btn-secondary" style="font-size: 1rem; padding: 14px 30px; border-radius: 999px;">
            <Bot :size="19" style="color: #FBBF24;" /> Simulateur & Devis IA
          </router-link>
        </div>
        
        <div class="hero-terminal">
          <div class="terminal-header">
            <div class="flex items-center gap-2">
              <span class="dot-red"></span>
              <span class="dot-yellow"></span>
              <span class="dot-green"></span>
              <span class="terminal-title">yass-agent-orchestrator — v2.6.4</span>
            </div>
            <span class="terminal-status">â— LIVE</span>
          </div>
          <div class="terminal-body">
            <div><span style="color: #FBBF24;">$</span><span style="color: var(--color-primary);"> agy init --stack "FastAPI + PgVector + Vue3 + n8n"</span></div>
            <div style="color: #64748B;">[INFO] Connecting to GPT-4o &amp; Claude 3.7... <span style="color: #10B981; font-weight: 700;">OK (38ms)</span></div>
            <div style="color: #64748B;">[INFO] WhatsApp multi-agent bridge... <span style="color: #10B981; font-weight: 700;">READY</span></div>
            <div style="color: #64748B;">[INFO] RAG pipeline with PgVector... <span style="color: #10B981; font-weight: 700;">READY</span></div>
            <div style="color: #FBBF24; font-weight: 700;">[SUCCESS] Architecture ready for production. ðŸšFCFA</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Statistics Grid -->
    <section class="stats-grid mb-16">
      <div class="stat-card stat-purple">
        <div class="stat-icon"><Package :size="26" /></div>
        <div class="stat-number">{{ countProducts }}+</div>
        <div class="stat-label">Produits & Templates</div>
      </div>
      <div class="stat-card stat-green">
        <div class="stat-icon"><Zap :size="26" /></div>
        <div class="stat-number">100%</div>
        <div class="stat-label">Accès Instantané 24/7</div>
      </div>
      <div class="stat-card stat-violet">
        <div class="stat-icon"><Users :size="26" /></div>
        <div class="stat-number">{{ countClients }}+</div>
        <div class="stat-label">Clients Satisfaits</div>
      </div>
      <div class="stat-card stat-gold">
        <div class="stat-icon"><Star :size="26" /></div>
        <div class="stat-number">5.0</div>
        <div class="stat-label">Note Satisfaction</div>
      </div>
    </section>

    <!-- How it Works -->
    <section class="section-block mb-16">
      <div class="text-center mb-10">
        <span class="badge-pill badge-purple mb-3">PROCESSUS SIMPLE</span>
        <h2 style="color: var(--color-text); margin-top: 8px;">Comment ça marche ?</h2>
        <p style="color: var(--color-text-muted); max-width: 520px; margin: 10px auto 0;">Trois étapes pour transformer vos projets numériques.</p>
      </div>
      <div class="process-grid">
        <div class="process-step">
          <div class="process-number" style="background: linear-gradient(135deg, #7C3AED, #6D28D9);">1</div>
          <h4>Explorez le catalogue</h4>
          <p>Choisissez parmi nos packs de prompts, templates SaaS et guides spécialement optimisés pour la production.</p>
        </div>
        <div class="process-connector"><ArrowRight :size="22" style="color: #334155;" /></div>
        <div class="process-step">
          <div class="process-number" style="background: linear-gradient(135deg, #06B6D4, #0891B2);">2</div>
          <h4>Commandez en sécurité</h4>
          <p>Paiement instantané par Carte Bancaire ou Mobile Money (Wave, Orange, MTN, Moov).</p>
        </div>
        <div class="process-connector"><ArrowRight :size="22" style="color: #334155;" /></div>
        <div class="process-step">
          <div class="process-number" style="background: linear-gradient(135deg, #10B981, #059669);">3</div>
          <h4>Accédez à vos ressources</h4>
          <p>Téléchargement immédiat et clés de licence sécurisées dans votre espace client.</p>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="mb-16">
      <div class="flex justify-between items-center mb-8" style="flex-wrap: wrap; gap: 16px;">
        <div>
          <span class="badge-pill badge-gold mb-2">SÉLECTION EXCLUSIVE</span>
          <h2 class="flex items-center gap-3 mt-3">
            <Flame :size="26" style="color: #F97316;" /> Produits à la Une
          </h2>
        </div>
        <router-link to="/products" class="btn btn-secondary flex items-center gap-2">
          Voir le catalogue <ArrowRight :size="16" />
        </router-link>
      </div>
      
      <div v-if="loadingProducts" class="grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 28px;">
        <div v-for="i in 3" :key="i" class="skeleton skeleton-card"></div>
      </div>
      
      <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 28px;">
        <div v-for="product in featuredProducts" :key="product.id" class="product-card" @click="$router.push('/products/' + product.id)">
          <div class="product-banner">
            <component :is="getProductIcon(product.type)" :size="80" style="color: rgba(255,255,255,0.06);" />
            <div class="product-banner-icon">
              <component :is="getProductIcon(product.type)" :size="34" />
            </div>
            <span class="badge-pill badge-gold" style="position: absolute; top: 14px; left: 14px;">{{ product.type || 'Digital' }}</span>
          </div>
          <div class="product-body">
            <h3 class="product-title">{{ product.title }}</h3>
            <p class="product-desc">{{ product.description }}</p>
          </div>
          <div class="product-footer">
            <div>
              <span class="product-price-label">Prix unique</span>
              <span class="product-price">{{ currencyStore.format(product.price) }}</span>
            </div>
            <router-link :to="`/products/${product.id}`" class="btn btn-primary btn-sm" @click.stop>
              <ShoppingBag :size="14" /> Commander
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Band -->
    <section class="services-band mb-16">
      <div class="services-band-glow"></div>
      <div style="position: relative; z-index: 2;">
        <div class="text-center mb-10">
          <span class="badge-pill badge-indigo mb-3">PRESTATIONS SUR MESURE</span>
          <h2 style="color: var(--color-text); margin-top: 8px;">Services Développement & IA</h2>
          <p style="color: var(--color-text-muted); max-width: 540px; margin: 10px auto 0;">Applications web, automatisations d'entreprise, agents IA sur mesure.</p>
        </div>
        <div class="services-band-grid">
          <div v-for="service in services" :key="service.id" class="service-mini-card">
            <div class="service-mini-icon"><component :is="getServiceIcon(service.title)" :size="28" /></div>
            <h4>{{ service.title }}</h4>
            <p>{{ service.description ? service.description.substring(0, 85) + '...' : '' }}</p>
          </div>
        </div>
        <div class="text-center mt-8">
          <router-link to="/services" class="btn btn-primary" style="font-size: 1rem; padding: 14px 32px; display: inline-flex; align-items: center; gap: 8px;">
            Consulter tous nos services <ArrowRight :size="18" />
          </router-link>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="mb-16">
      <div class="flex justify-between items-center mb-8" style="flex-wrap: wrap; gap: 16px;">
        <div>
          <span class="badge-pill badge-gold mb-2">TÉMOIGNAGES</span>
          <h2 class="flex items-center gap-3 mt-3"><Star :size="26" style="color: #FBBF24;" /> Ce que disent nos clients</h2>
        </div>
        <div class="flex gap-2">
          <button @click="prevReview" class="btn btn-secondary btn-icon" title="Précédent"><ChevronLeft :size="20" /></button>
          <button @click="nextReview" class="btn btn-secondary btn-icon" title="Suivant"><ChevronRight :size="20" /></button>
        </div>
      </div>
      
      <div class="testimonial-card">
        <div class="testimonial-quote">"</div>
        <div class="flex gap-1 mb-4" style="color: #FBBF24;">
          <Star v-for="n in reviews[currentReviewIdx].rating" :key="n" :size="18" style="fill: #FBBF24;" />
        </div>
        <p class="testimonial-text">"{{ reviews[currentReviewIdx].comment }}"</p>
        <div class="testimonial-footer">
          <div class="testimonial-avatar">{{ reviews[currentReviewIdx].name[0] }}</div>
          <div>
            <p class="testimonial-name">{{ reviews[currentReviewIdx].name }}</p>
            <p class="testimonial-role"><CheckCircle2 :size="13" style="color: #10B981;" /> Client Vérifié Â· {{ reviews[currentReviewIdx].role }}</p>
          </div>
          <div class="testimonial-dots">
            <span v-for="(r, idx) in reviews" :key="r.id" @click="currentReviewIdx = idx"
              :class="currentReviewIdx === idx ? 'dot-active' : 'dot-inactive'"></span>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section class="faq-section mb-16">
      <div class="text-center mb-10">
        <span class="badge-pill badge-indigo mb-3">AIDE & SUPPORT</span>
        <h2 class="flex items-center justify-center gap-3 mt-3">
          <HelpCircle :size="26" style="color: var(--color-primary);" /> Questions Fréquentes
        </h2>
      </div>
      <div class="faq-list">
        <div v-for="(faq, idx) in faqs" :key="idx" class="faq-item" :class="{ 'faq-open': openFaqIdx === idx }" @click="toggleFaq(idx)">
          <div class="faq-question">
            <span>{{ faq.question }}</span>
            <ChevronDown :size="18" :style="{ transform: openFaqIdx === idx ? 'rotate(180deg)' : 'rotate(0)', transition: 'transform 0.3s' }" style="color: #7C3AED; flex-shrink: 0;" />
          </div>
          <div v-if="openFaqIdx === idx" class="faq-answer fade-in">{{ faq.answer }}</div>
        </div>
      </div>
    </section>

    <!-- Trust Band -->
    <section class="trust-band">
      <div class="trust-item"><ShieldCheck :size="32" style="color: var(--color-primary);" /><div><h4>Garantie Qualité</h4><span>Code propre & testé</span></div></div>
      <div class="trust-item"><Zap :size="32" style="color: #34D399;" /><div><h4>Livraison Instantanée</h4><span>Accès immédiat post-achat</span></div></div>
      <div class="trust-item"><Headphones :size="32" style="color: #22D3EE;" /><div><h4>Support Réactif</h4><span>Accompagnement 7j/7</span></div></div>
      <div class="trust-item"><RefreshCw :size="32" style="color: #FCD34D;" /><div><h4>Mises à Jour à Vie</h4><span>Incluses sur tous les packs</span></div></div>
    </section>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { t } from '../i18n';
import api from '../api';
import { useCurrencyStore } from '../stores/currency';
import {
  Package, Zap, Users, Star, Flame, ArrowRight,
  ShoppingBag, ChevronLeft, ChevronRight, ChevronDown,
  HelpCircle, ShieldCheck, Headphones, CheckCircle2,
  Layout, BookOpen, Wrench, FileText, Sparkles,
  Globe, Code2, Bot, Workflow, RefreshCw
} from 'lucide-vue-next';

const currencyStore = useCurrencyStore();
const featuredProducts = ref([]);
const services = ref([]);
const loadingProducts = ref(true);
const countProducts = ref(0);
const countClients = ref(0);

const animateCounters = () => {
  let p = 0, c = 0;
  const timerP = setInterval(() => { if (p >= 50) clearInterval(timerP); else { p += 2; countProducts.value = p; } }, 30);
  const timerC = setInterval(() => { if (c >= 200) clearInterval(timerC); else { c += 8; countClients.value = c; } }, 25);
};

const currentReviewIdx = ref(0);
const openFaqIdx = ref(null);

const faqs = ref([
  { question: 'Comment recevoir mes fichiers après achat ?', answer: 'Dès la validation de votre commande, vos fichiers et liens de téléchargement sont accessibles immédiatement dans votre espace client sécurisé sous l\'onglet Mes Téléchargements.' },
  { question: 'Les templates et packs incluent-ils les futures mises à jour ?', answer: 'Oui ! Tous nos produits et templates incluent un accès illimité et gratuit aux nouvelles versions et mises à jour à vie.' },
  { question: 'Proposez-vous des prestations sur-mesure pour entreprises ?', answer: 'Absolument. Vous pouvez nous contacter via la page Services ou par formulaire pour obtenir un devis personnalisé adapté à votre projet.' },
  { question: 'Quels sont les moyens de paiement acceptés ?', answer: 'Nous acceptons les cartes bancaires sécurisées via Stripe, ainsi que les paiements Mobile Money (Wave, Orange Money, MTN, Moov).' }
]);

const toggleFaq = (idx) => { openFaqIdx.value = openFaqIdx.value === idx ? null : idx; };

const reviews = ref([
  { id: 1, name: 'Kouassi A.', role: 'Fondateur Agency', rating: 5, comment: 'Des packs de prompts incroyables ! J\'ai gagné des heures de travail sur mes projets de rédaction et de code.' },
  { id: 2, name: 'Sophie L.', role: 'Lead Developer', rating: 5, comment: 'Le template SaaS Starter Vue 3 + Laravel m\'a permis de lancer notre MVP en seulement 3 jours. Qualité exceptionnelle !' },
  { id: 3, name: 'Marc-Olivier T.', role: 'Consultant Tech', rating: 5, comment: 'Service ultra-réactif et prestations sur mesure irréprochables. Yass Digital Lab est désormais notre partenaire de référence.' }
]);

const nextReview = () => { currentReviewIdx.value = (currentReviewIdx.value + 1) % reviews.value.length; };
const prevReview = () => { currentReviewIdx.value = (currentReviewIdx.value - 1 + reviews.value.length) % reviews.value.length; };

onMounted(async () => {
  animateCounters();
  try {
    const [productsRes, servicesRes] = await Promise.all([api.get('/products'), api.get('/services')]);
    const prodList = Array.isArray(productsRes.data) ? productsRes.data : (productsRes.data?.data || []);
    const servList = Array.isArray(servicesRes.data) ? servicesRes.data : (servicesRes.data?.data || []);
    featuredProducts.value = prodList.slice(0, 3);
    services.value = servList.slice(0, 4);
  } catch (e) { console.error(e); } finally { loadingProducts.value = false; }
});

const getProductIcon = (type) => {
  const map = { 'Pack': Package, 'Template': Layout, 'E-book': BookOpen, 'Outil': Wrench, 'Guide': FileText };
  return map[type] || Sparkles;
};
const getServiceIcon = (title) => {
  if (!title) return Sparkles;
  const tl = title.toLowerCase();
  if (tl.includes('site') || tl.includes('web')) return Globe;
  if (tl.includes('laravel') || tl.includes('full') || tl.includes('code')) return Code2;
  if (tl.includes('ia') || tl.includes('agent')) return Bot;
  if (tl.includes('auto') || tl.includes('workflow')) return Workflow;
  return Sparkles;
};
</script>

<style scoped>
.home { padding: 20px 0 80px; color: var(--color-text); }

/* â”FCFAâ”FCFA HERO â”FCFAâ”FCFA */
.hero {
  position: relative;
  overflow: hidden;
  background: radial-gradient(ellipse at 15% 85%, rgba(245,158,11,0.15) 0%, transparent 50%),
              var(--page-gradient);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl);
  margin-bottom: 56px;
  padding: 100px 40px 90px;
  text-align: center;
  box-shadow: 0 40px 120px rgba(0,0,0,0.55);
}
.hero-orb { position: absolute; border-radius: 50%; pointer-events: none; filter: blur(90px); opacity: 0.4; animation: floatUp 9s ease-in-out infinite; }
.hero-orb-purple { width: 550px; height: 550px; background: radial-gradient(circle, rgba(124,58,237,0.5), transparent 70%); top: -150px; left: 5%; animation-delay: 0s; }
.hero-orb-cyan   { width: 420px; height: 420px; background: radial-gradient(circle, rgba(6,182,212,0.35), transparent 70%); top: 20%; right: 2%; animation-delay: -3s; }
.hero-orb-gold   { width: 320px; height: 320px; background: radial-gradient(circle, rgba(245,158,11,0.28), transparent 70%); bottom: -80px; left: 35%; animation-delay: -6s; }
.hero-inner { position: relative; z-index: 2; max-width: 900px; margin: 0 auto; }

.hero-badge {
  display: inline-flex; align-items: center; gap: 10px;
  padding: 8px 22px; border-radius: 999px;
  background: rgba(124,58,237,0.12); border: 1px solid rgba(124,58,237,0.38);
  backdrop-filter: blur(10px);
  font-size: 0.82rem; font-weight: 700; letter-spacing: 0.04em; color: #C4B5FD;
}
.hero-badge-dot { width: 8px; height: 8px; border-radius: 50%; background: #10B981; animation: pulseDot 2s infinite ease-in-out; flex-shrink: 0; }
.hero-title { font-size: clamp(2.4rem, 5.5vw, 4rem) !important; font-weight: 900 !important; color: var(--color-text) !important; line-height: 1.12 !important; letter-spacing: -0.03em !important; }
.hero-sub { color: var(--color-text-muted); font-size: 1.12rem; line-height: 1.75; max-width: 680px; margin-inline: auto; }
.hero-cta { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }

.hero-terminal { max-width: 680px; margin: 0 auto; background: #020510; border: 1px solid rgba(255,255,255,0.09); border-radius: 14px; overflow: hidden; box-shadow: 0 24px 80px rgba(0,0,0,0.75), 0 0 0 1px rgba(124,58,237,0.18); text-align: left; font-family: var(--font-mono); }
.terminal-header { background: #0A0F1E; padding: 10px 18px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); }
.dot-red    { width: 11px; height: 11px; border-radius: 50%; background: #EF4444; display: inline-block; }
.dot-yellow { width: 11px; height: 11px; border-radius: 50%; background: #FBBF24; display: inline-block; }
.dot-green  { width: 11px; height: 11px; border-radius: 50%; background: #10B981; display: inline-block; }
.terminal-title { font-size: 0.72rem; color: #64748B; margin-left: 10px; }
.terminal-status { font-size: 0.7rem; color: #10B981; font-weight: 700; letter-spacing: 0.08em; }
.terminal-body { padding: 18px 22px; font-size: 0.84rem; line-height: 2; color: var(--color-text-muted); }

/* â”FCFAâ”FCFA STATS â”FCFAâ”FCFA */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
.stat-card { padding: 28px 22px; border-radius: var(--radius-lg); border: 1px solid var(--color-border); text-align: center; background: var(--color-bg-card); transition: var(--transition-slow); position: relative; overflow: hidden; }
.stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
.stat-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
.stat-purple::before { background: linear-gradient(90deg, #7C3AED, #6D28D9); }
.stat-purple .stat-icon { color: var(--color-primary); background: rgba(124,58,237,0.12); border: 1px solid rgba(124,58,237,0.25); }
.stat-green::before { background: linear-gradient(90deg, #10B981, #059669); }
.stat-green .stat-icon { color: #34D399; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25); }
.stat-violet::before { background: linear-gradient(90deg, #8B5CF6, #7C3AED); }
.stat-violet .stat-icon { color: #C4B5FD; background: rgba(139,92,246,0.12); border: 1px solid rgba(139,92,246,0.25); }
.stat-gold::before { background: linear-gradient(90deg, #F59E0B, #FBBF24); }
.stat-gold .stat-icon { color: #FCD34D; background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.25); }
.stat-icon { width: 52px; height: 52px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px; }
.stat-number { font-size: 2.4rem; font-weight: 900; color: var(--color-text); font-family: var(--font-title); line-height: 1; margin-bottom: 6px; }
.stat-label  { font-size: 0.88rem; color: var(--color-text-muted); font-weight: 600; }

/* â”FCFAâ”FCFA HOW IT WORKS â”FCFAâ”FCFA */
.section-block { background: var(--color-bg-card); border: 1px solid var(--color-border); border-radius: var(--radius-xl); padding: 60px 48px; }
.process-grid { display: flex; align-items: flex-start; gap: 12px; flex-wrap: wrap; justify-content: center; }
.process-step { flex: 1; min-width: 220px; max-width: 290px; text-align: center; padding: 20px 16px; }
.process-connector { padding-top: 28px; flex-shrink: 0; }
.process-number { width: 64px; height: 64px; border-radius: 50%; color: var(--color-text); font-size: 1.5rem; font-weight: 900; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto 18px; box-shadow: 0 8px 24px rgba(0,0,0,0.4); }
.process-step h4 { color: var(--color-text); font-size: 1.1rem; margin-bottom: 8px; }
.process-step p  { color: var(--color-text-muted); font-size: 0.9rem; line-height: 1.65; }

/* â”FCFAâ”FCFA PRODUCT CARDS â”FCFAâ”FCFA */
.product-banner { height: 185px; background: linear-gradient(135deg, var(--color-bg-elevated) 0%, var(--color-bg-card) 55%, var(--color-bg-glass) 100%); position: relative; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--color-border); overflow: hidden; }
.product-banner-icon { display: flex; align-items: center; justify-content: center; width: 76px; height: 76px; border-radius: 22px; background: rgba(124,58,237,0.18); border: 1.5px solid rgba(124,58,237,0.32); color: var(--color-primary); box-shadow: 0 0 24px rgba(124,58,237,0.25); transition: all 0.35s ease; position: absolute; }
.product-card:hover .product-banner-icon { background: rgba(124,58,237,0.28); border-color: rgba(124,58,237,0.55); box-shadow: 0 0 36px rgba(124,58,237,0.4); transform: scale(1.07); }
.product-body { padding: 22px; flex: 1; }
.product-title { color: var(--color-text); font-size: 1.12rem; font-weight: 700; margin-bottom: 8px; }
.product-desc  { color: var(--color-text-muted); font-size: 0.9rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-footer { padding: 14px 22px; border-top: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; }
.product-price-label { font-size: 0.72rem; color: var(--color-text-muted); display: block; margin-bottom: 2px; }
.product-price { font-weight: 800; font-size: 1.25rem; color: var(--color-primary); }

/* â”FCFAâ”FCFA SERVICES â”FCFAâ”FCFA */
.services-band { background: linear-gradient(135deg, var(--color-bg-elevated) 0%, var(--color-bg-2) 50%, var(--color-bg-card) 100%); border: 1px solid var(--color-border); border-radius: var(--radius-xl); padding: 70px 48px; position: relative; overflow: hidden; }
.services-band-glow { position: absolute; width: 600px; height: 400px; background: radial-gradient(ellipse, rgba(124,58,237,0.14) 0%, transparent 70%); top: 50%; left: 50%; transform: translate(-50%,-50%); pointer-events: none; }
.services-band-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
.service-mini-card { background: var(--color-bg-card); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 28px 22px; text-align: center; transition: var(--transition-slow); box-shadow: var(--shadow-sm); }
.service-mini-card:hover { border-color: rgba(124,58,237,0.4); transform: translateY(-4px); background: var(--color-bg-elevated); box-shadow: var(--shadow-md); }
.service-mini-icon { color: #818CF8; display: flex; justify-content: center; margin-bottom: 14px; }
.service-mini-card h4 { color: var(--color-text); font-size: 1rem; margin-bottom: 8px; }
.service-mini-card p  { color: var(--color-text-muted); font-size: 0.85rem; line-height: 1.6; }

/* â”FCFAâ”FCFA TESTIMONIAL â”FCFAâ”FCFA */
.testimonial-card { background: var(--color-bg-card); border: 1px solid var(--color-border); border-left: 4px solid #7C3AED; border-radius: var(--radius-xl); padding: 48px; position: relative; }
.testimonial-quote { position: absolute; top: -10px; left: 40px; font-size: 5.5rem; color: rgba(124,58,237,0.12); font-family: Georgia, serif; line-height: 1; pointer-events: none; }
.testimonial-text { font-style: italic; color: var(--color-text); font-size: 1.1rem; line-height: 1.82; margin-bottom: 30px; }
.testimonial-footer { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
.testimonial-avatar { width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, #7C3AED, #06B6D4); color: var(--color-text); display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.3rem; flex-shrink: 0; }
.testimonial-name { font-weight: 800; font-size: 1rem; color: var(--color-text); margin: 0 0 4px; }
.testimonial-role { font-size: 0.82rem; color: var(--color-primary); font-weight: 700; display: flex; align-items: center; gap: 5px; }
.testimonial-dots { display: flex; gap: 8px; margin-left: auto; }
.dot-active   { width: 28px; height: 9px; border-radius: 999px; background: #7C3AED; cursor: pointer; transition: all 0.3s; }
.dot-inactive { width: 9px;  height: 9px; border-radius: 50%; background: var(--color-border); cursor: pointer; transition: all 0.3s; }

/* â”FCFAâ”FCFA FAQ â”FCFAâ”FCFA */
.faq-section { background: var(--color-bg-card); border: 1px solid var(--color-border); border-radius: var(--radius-xl); padding: 60px 48px; }
.faq-list { max-width: 820px; margin: 0 auto; display: flex; flex-direction: column; gap: 6px; }
.faq-item { border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 20px 24px; cursor: pointer; transition: var(--transition-base); background: rgba(255,255,255,0.02); }
.faq-item:hover, .faq-open { border-color: rgba(124,58,237,0.4); background: rgba(124,58,237,0.05); }
.faq-question { display: flex; justify-content: space-between; align-items: center; font-size: 0.98rem; font-weight: 600; color: var(--color-text); gap: 12px; }
.faq-answer { margin-top: 14px; font-size: 0.92rem; line-height: 1.75; color: var(--color-text-muted); border-top: 1px solid var(--color-border); padding-top: 14px; }

/* â”FCFAâ”FCFA TRUST BAND â”FCFAâ”FCFA */
.trust-band { background: linear-gradient(135deg, rgba(124,58,237,0.08) 0%, rgba(6,182,212,0.05) 100%); border: 1px solid rgba(124,58,237,0.18); border-radius: var(--radius-xl); padding: 32px 40px; display: flex; justify-content: space-around; align-items: center; gap: 24px; flex-wrap: wrap; }
.trust-item { display: flex; align-items: center; gap: 14px; flex: 1; min-width: 200px; }
.trust-item h4 { font-size: 0.95rem; font-weight: 700; color: var(--color-text); margin: 0; }
.trust-item span { font-size: 0.83rem; color: var(--color-text-muted); }

/* â”FCFAâ”FCFA Responsive â”FCFAâ”FCFA */
@media (max-width: 768px) {
  .hero { padding: 64px 24px 56px; border-radius: var(--radius-lg); }
  .hero-title { font-size: 2.1rem !important; }
  .hero-cta { flex-direction: column; align-items: center; }
  .section-block { padding: 40px 24px; }
  .services-band { padding: 48px 24px; }
  .faq-section { padding: 40px 24px; }
  .testimonial-card { padding: 32px 24px; }
  .process-connector { display: none; }
  .hero-terminal { font-size: 0.78rem; }
}
</style>

