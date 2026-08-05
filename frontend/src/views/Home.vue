<template>
  <div class="home">

    <!-- Hero Section -->
    <section class="hero glass text-center fade-in" style="padding: 85px 32px 75px; border-radius: 28px; margin-top: 30px; margin-bottom: 50px; position: relative; overflow: hidden;">
      
      <!-- Mesh Glow Background -->
      <div class="hero-glow"></div>
      
      <div style="position: relative; z-index: 2;">
        
        <!-- Badge -->
        <div class="badge-pill mb-6" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(212,175,55,0.14); border: 1px solid var(--color-accent); padding: 8px 20px; border-radius: 999px; font-size: 0.85rem; font-weight: 700; color: var(--color-accent);">
          <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--color-accent);" class="pulse"></span>
          {{ t('heroBadge') }}
        </div>

        <!-- Main Heading -->
        <h1 class="mb-6" style="font-size: clamp(2.2rem, 5vw, 3.6rem); line-height: 1.18; font-weight: 800; max-width: 860px; margin-inline: auto;">
          {{ t('heroTitle') }}
        </h1>

        <!-- Subheading -->
        <p class="mb-8" style="color: var(--color-text-light); font-size: 1.15rem; max-width: 620px; margin-inline: auto; line-height: 1.7;">
          {{ t('heroSubtitle') }}
        </p>

        <!-- CTA Action Buttons -->
        <div class="flex justify-center gap-4" style="flex-wrap: wrap;">
          <router-link to="/products" class="btn btn-primary flex items-center gap-2" style="font-size: 1.05rem; padding: 0.95rem 2.2rem;">
            <span>{{ t('exploreCatalog') }}</span> <ArrowRight :size="18" />
          </router-link>
          <router-link to="/services" class="btn btn-secondary flex items-center gap-2" style="font-size: 1.05rem; padding: 0.95rem 2.2rem;">
            <Briefcase :size="18" /> <span>{{ t('requestQuote') }}</span>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Key Statistics -->
    <section class="grid mb-16" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">
      <div class="glass glass-hover text-center" style="padding: 26px 20px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid var(--color-accent); background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div style="margin-bottom: 12px; display: inline-flex; width: 50px; height: 50px; border-radius: 14px; background: rgba(212,175,55,0.15); border: 1px solid rgba(212,175,55,0.3); align-items: center; justify-content: center; color: var(--color-accent);">
          <Package :size="26" />
        </div>
        <h2 style="color: var(--color-text); font-size: 2.3rem; font-weight: 800; margin: 4px 0 2px; font-family: var(--font-heading);">50+</h2>
        <p style="color: var(--color-text-light); font-weight: 700; font-size: 0.88rem;">Produits & Templates</p>
      </div>

      <div class="glass glass-hover text-center" style="padding: 26px 20px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #10b981; background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div style="margin-bottom: 12px; display: inline-flex; width: 50px; height: 50px; border-radius: 14px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); align-items: center; justify-content: center; color: #10b981;">
          <Zap :size="26" />
        </div>
        <h2 style="color: var(--color-text); font-size: 2.3rem; font-weight: 800; margin: 4px 0 2px; font-family: var(--font-heading);">100%</h2>
        <p style="color: var(--color-text-light); font-weight: 700; font-size: 0.88rem;">Accès Instantané 24/7</p>
      </div>

      <div class="glass glass-hover text-center" style="padding: 26px 20px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #3b82f6; background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div style="margin-bottom: 12px; display: inline-flex; width: 50px; height: 50px; border-radius: 14px; background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); align-items: center; justify-content: center; color: #3b82f6;">
          <Users :size="26" />
        </div>
        <h2 style="color: var(--color-text); font-size: 2.3rem; font-weight: 800; margin: 4px 0 2px; font-family: var(--font-heading);">200+</h2>
        <p style="color: var(--color-text-light); font-weight: 700; font-size: 0.88rem;">Clients Satisfaits</p>
      </div>

      <div class="glass glass-hover text-center" style="padding: 26px 20px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #a855f7; background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div style="margin-bottom: 12px; display: inline-flex; width: 50px; height: 50px; border-radius: 14px; background: rgba(168,85,247,0.15); border: 1px solid rgba(168,85,247,0.3); align-items: center; justify-content: center; color: #a855f7;">
          <Star :size="26" />
        </div>
        <h2 style="color: var(--color-text); font-size: 2.3rem; font-weight: 800; margin: 4px 0 2px; font-family: var(--font-heading);">5.0 / 5</h2>
        <p style="color: var(--color-text-light); font-weight: 700; font-size: 0.88rem;">Satisfaction Garantie</p>
      </div>
    </section>

    <!-- Produits Phares Section -->
    <section style="margin-bottom: 60px;">
      <div class="flex justify-between items-center mb-8" style="flex-wrap: wrap; gap: 16px;">
        <div>
          <span style="color: var(--color-accent); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em;">Sélection Exclusive</span>
          <h2 class="flex items-center gap-3" style="font-size: 2rem; margin-top: 4px;">
            <Flame :size="26" style="color: #F97316;" /> Produits à la Une
          </h2>
        </div>
        <router-link to="/products" class="btn btn-secondary flex items-center gap-2" style="padding: 0.6rem 1.4rem; font-size: 0.9rem;">
          <span>Voir tout le catalogue</span> <ArrowRight :size="16" />
        </router-link>
      </div>

      <div v-if="loadingProducts" class="text-center" style="padding: 60px;">
        <p style="color: var(--color-accent); font-size: 1.1rem;">Chargement des meilleurs produits...</p>
      </div>

      <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 28px;">
        <div 
          v-for="product in featuredProducts" 
          :key="product.id" 
          class="card glass glass-hover" 
          style="border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;"
        >
          <!-- Banner -->
          <div style="height: 180px; background: linear-gradient(135deg, var(--color-primary), #1e293b); position: relative; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--color-border);">
            <div style="color: var(--color-accent);">
              <component :is="getProductIcon(product.type)" :size="64" />
            </div>
            <div style="position: absolute; top: 14px; right: 14px; background: var(--color-accent); color: #050811; font-size: 0.75rem; font-weight: 800; padding: 4px 12px; border-radius: 999px;">
              {{ product.type || 'Incontournable' }}
            </div>
          </div>

          <!-- Body -->
          <div style="padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <h3 class="mb-2" style="font-size: 1.2rem;">{{ product.title }}</h3>
              <p style="color: var(--color-text-light); font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ product.description }}
              </p>
            </div>

            <div class="flex justify-between items-center" style="border-top: 1px solid var(--color-border); padding-top: 16px; margin-top: auto;">
              <div>
                <span style="font-size: 0.75rem; color: var(--color-text-light); display: block;">Prix unique</span>
                <span style="font-weight: 800; font-size: 1.4rem; color: var(--color-accent);">{{ product.price }} €</span>
              </div>
              <router-link :to="`/products/${product.id}`" class="btn btn-primary flex items-center gap-2" style="padding: 0.5rem 1.2rem; font-size: 0.9rem;">
                <ShoppingBag :size="16" /> Commander
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Grid Highlight -->
    <section class="glass" style="padding: 55px 36px; border-radius: 28px; background: linear-gradient(135deg, rgba(15,23,42,0.92), rgba(30,41,59,0.75)); color: #FFF; margin-bottom: 60px;">
      <div class="text-center mb-10">
        <span style="color: var(--color-accent); font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Prestations sur-mesure</span>
        <h2 class="flex items-center justify-center gap-3" style="font-size: 2.1rem; color: #FFF; margin-top: 6px;">
          <Briefcase :size="26" style="color: var(--color-accent);" /> Services de Développement & IA
        </h2>
        <p style="color: #94A3B8; max-width: 580px; margin: 8px auto 0; font-size: 0.95rem;">Nous concevons vos applications web, automatisations et agents IA de A à Z.</p>
      </div>

      <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
        <div v-for="service in services" :key="service.id" class="service-card glass" style="padding: 26px; border-radius: 20px; background: rgba(255,255,255,0.04); border: 1px solid rgba(212,175,55,0.2); text-align: center; transition: all 0.3s;">
          <div style="display: flex; justify-content: center; align-items: center; height: 60px; margin-bottom: 12px; color: var(--color-accent);">
            <component :is="getServiceIcon(service.title)" :size="40" />
          </div>
          <h4 class="mb-2" style="color: #FFF; font-size: 1.1rem;">{{ service.title }}</h4>
          <p style="color: #94A3B8; font-size: 0.85rem; line-height: 1.6;">{{ service.description ? service.description.substring(0, 90) + '...' : '' }}</p>
        </div>
      </div>

      <div class="text-center mt-8">
        <router-link to="/services" class="btn btn-primary flex items-center justify-center gap-2" style="padding: 0.85rem 2rem; display: inline-flex;">
          <span>Consulter tous nos services</span> <ArrowRight :size="18" />
        </router-link>
      </div>
    </section>

    <!-- Avis Clients (Carrousel Interactif) -->
    <section style="margin-bottom: 50px;">
      <div class="flex justify-between items-center mb-8" style="flex-wrap: wrap; gap: 16px;">
        <div>
          <span style="color: var(--color-accent); font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Témoignages Client</span>
          <h2 class="flex items-center gap-3" style="font-size: 2.1rem; margin-top: 4px;">
            <Star :size="26" style="color: var(--color-accent);" /> Ce que nos clients disent de nous
          </h2>
        </div>
        
        <!-- Controls Carrousel -->
        <div class="flex gap-2">
          <button @click="prevReview" class="btn btn-secondary" style="padding: 8px 14px; border-radius: 999px; display: inline-flex; align-items: center;" title="Précédent">
            <ChevronLeft :size="20" />
          </button>
          <button @click="nextReview" class="btn btn-secondary" style="padding: 8px 14px; border-radius: 999px; display: inline-flex; align-items: center;" title="Suivant">
            <ChevronRight :size="20" />
          </button>
        </div>
      </div>

      <div class="glass glass-hover" style="padding: 40px; border-radius: 24px; position: relative; border-left: 4px solid var(--color-accent);">
        <div class="flex gap-1" style="color: var(--color-accent); margin-bottom: 16px;">
          <Star v-for="n in reviews[currentReviewIdx].rating" :key="n" :size="18" style="fill: var(--color-accent);" />
        </div>
        <p style="font-style: italic; color: var(--color-text); font-size: 1.15rem; line-height: 1.8; margin-bottom: 28px;">
          "{{ reviews[currentReviewIdx].comment }}"
        </p>
        
        <div class="flex justify-between items-center" style="flex-wrap: wrap; gap: 16px;">
          <div class="flex items-center gap-4">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #F0CC55, #D4AF37); color: #050811; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; box-shadow: var(--shadow-accent);">
              {{ reviews[currentReviewIdx].name[0] }}
            </div>
            <div>
              <p style="font-weight: 800; font-size: 1.05rem; margin: 0;">{{ reviews[currentReviewIdx].name }}</p>
              <p style="font-size: 0.85rem; color: var(--color-accent); font-weight: 700; margin: 2px 0 0; display: flex; align-items: center; gap: 4px;">
                <CheckCircle2 :size="13" /> Client Vérifié • {{ reviews[currentReviewIdx].role }}
              </p>
            </div>
          </div>

          <!-- Slide Dots -->
        <div class="flex gap-2">
            <span 
              v-for="(r, idx) in reviews" 
              :key="r.id" 
              @click="currentReviewIdx = idx"
              :style="currentReviewIdx === idx ? 'background: var(--color-accent); width: 24px;' : 'background: rgba(255,255,255,0.2); width: 10px;'"
              style="height: 10px; border-radius: 999px; cursor: pointer; transition: all 0.3s;"
            ></span>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Accordion Interactive Section -->
    <section class="glass" style="padding: 50px 36px; border-radius: 24px; margin-bottom: 60px;">
      <div class="text-center mb-8">
        <span style="color: var(--color-accent); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em;">Aide & Support</span>
        <h2 class="flex items-center justify-center gap-3" style="font-size: 2rem; margin-top: 4px;">
          <HelpCircle :size="26" style="color: var(--color-accent);" /> Questions Fréquemment Posées
        </h2>
      </div>

      <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px;">
        <div 
          v-for="(faq, idx) in faqs" 
          :key="idx"
          @click="toggleFaq(idx)"
          class="glass"
          style="padding: 20px 24px; border-radius: 16px; cursor: pointer; transition: all 0.3s; border: 1px solid var(--color-border);"
        >
          <div class="flex justify-between items-center">
            <h4 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--color-text);">{{ faq.question }}</h4>
            <ChevronDown :size="20" :style="{ transform: openFaqIdx === idx ? 'rotate(180deg)' : 'rotate(0deg)', transition: 'transform 0.3s' }" style="color: var(--color-accent);" />
          </div>
          <p v-if="openFaqIdx === idx" style="margin-top: 14px; font-size: 0.95rem; line-height: 1.7; color: var(--color-text-light); border-top: 1px solid var(--color-border); padding-top: 14px;" class="fade-in">
            {{ faq.answer }}
          </p>
        </div>
      </div>
    </section>

    <!-- Trust Security Banner -->
    <section style="background: linear-gradient(90deg, rgba(212,175,55,0.12), rgba(15,23,42,0.12)); border: 1px solid var(--color-border); border-radius: 20px; padding: 24px; margin-bottom: 60px;">
      <div class="flex justify-between items-center text-center" style="flex-wrap: wrap; gap: 20px;">
        <div class="flex items-center gap-3 justify-center" style="flex: 1; min-width: 200px;">
          <ShieldCheck :size="28" style="color: var(--color-accent);" />
          <div style="text-align: left;">
            <h4 style="font-size: 0.95rem; font-weight: 700; margin: 0;">Garantie Qualité</h4>
            <span style="font-size: 0.82rem; color: var(--color-text-light);">Code propre & testé</span>
          </div>
        </div>
        <div class="flex items-center gap-3 justify-center" style="flex: 1; min-width: 200px;">
          <Zap :size="28" style="color: var(--color-accent);" />
          <div style="text-align: left;">
            <h4 style="font-size: 0.95rem; font-weight: 700; margin: 0;">Livraison Instantanée</h4>
            <span style="font-size: 0.82rem; color: var(--color-text-light);">Accès immédiat post-achat</span>
          </div>
        </div>
        <div class="flex items-center gap-3 justify-center" style="flex: 1; min-width: 200px;">
          <Headphones :size="28" style="color: var(--color-accent);" />
          <div style="text-align: left;">
            <h4 style="font-size: 0.95rem; font-weight: 700; margin: 0;">Support Réactif</h4>
            <span style="font-size: 0.82rem; color: var(--color-text-light);">Accompagnement 7j/7</span>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { t } from '../i18n';
import api from '../api';
import { 
  Package, 
  Zap, 
  Users, 
  Star, 
  Flame, 
  ArrowRight, 
  Briefcase, 
  ShoppingBag, 
  ChevronLeft, 
  ChevronRight, 
  ChevronDown,
  HelpCircle,
  ShieldCheck,
  Headphones,
  CheckCircle2, 
  Layout, 
  BookOpen, 
  Wrench, 
  FileText, 
  Sparkles, 
  Globe, 
  Code2, 
  Bot, 
  Workflow 
} from 'lucide-vue-next';

const featuredProducts = ref([]);
const services = ref([]);
const loadingProducts = ref(true);

const currentReviewIdx = ref(0);
const openFaqIdx = ref(null);

const faqs = ref([
  { question: 'Comment recevoir mes fichiers après achat ?', answer: 'Dès la validation de votre commande, vos fichiers et liens de téléchargement sont accessibles immédiatement dans votre espace client sécurisé sous l\'onglet Mes Téléchargements.' },
  { question: 'Les templates et packs incluent-ils les futures mises à jour ?', answer: 'Oui ! Tous nos produits et templates incluent un accès illimité et gratuit aux nouvelles versions et mises à jour à vie.' },
  { question: 'Proposez-vous des prestations sur-mesure pour entreprises ?', answer: 'Absolument. Vous pouvez nous contacter via la page Services ou par formulaire pour obtenir un devis personnalisé adapté à votre projet.' },
  { question: 'Quels sont les moyens de paiement acceptés ?', answer: 'Nous acceptons les cartes bancaires sécurisées via Stripe, PayPal ainsi que les paiements Mobile Money.' }
]);

const toggleFaq = (idx) => {
  openFaqIdx.value = openFaqIdx.value === idx ? null : idx;
};

const reviews = ref([
  { id: 1, name: 'Kouassi A.', role: 'Fondateur Agency', rating: 5, comment: 'Des packs de prompts incroyables ! J\'ai gagné des heures de travail sur mes projets de rédaction et de code.' },
  { id: 2, name: 'Sophie L.', role: 'Lead Developer', rating: 5, comment: 'Le template SaaS Starter Vue 3 + Laravel m\'a permis de lancer notre MVP en seulement 3 jours. Qualité exceptionnelle !' },
  { id: 3, name: 'Marc-Olivier T.', role: 'Consultant Tech', rating: 5, comment: 'Service ultra-réactif et prestations sur mesure irréprochables. Yass Digital Lab est désormais notre partenaire de référence.' }
]);

const nextReview = () => {
  currentReviewIdx.value = (currentReviewIdx.value + 1) % reviews.value.length;
};

const prevReview = () => {
  currentReviewIdx.value = (currentReviewIdx.value - 1 + reviews.value.length) % reviews.value.length;
};

onMounted(async () => {
  try {
    const [productsRes, servicesRes] = await Promise.all([
      api.get('/products'),
      api.get('/services')
    ]);
    const prodList = Array.isArray(productsRes.data) ? productsRes.data : (productsRes.data?.data || []);
    const servList = Array.isArray(servicesRes.data) ? servicesRes.data : (servicesRes.data?.data || []);
    featuredProducts.value = prodList.slice(0, 3);
    services.value = servList.slice(0, 4);
  } catch (e) {
    console.error(e);
  } finally {
    loadingProducts.value = false;
  }
});

const getProductIcon = (type) => {
  const map = { 
    'Pack': Package, 
    'Template': Layout, 
    'E-book': BookOpen, 
    'Outil': Wrench, 
    'Guide': FileText 
  };
  return map[type] || Sparkles;
};

const getServiceIcon = (title) => {
  if (!title) return Sparkles;
  const t = title.toLowerCase();
  if (t.includes('site') || t.includes('web')) return Globe;
  if (t.includes('laravel') || t.includes('full') || t.includes('code')) return Code2;
  if (t.includes('ia') || t.includes('agent')) return Bot;
  if (t.includes('auto') || t.includes('workflow')) return Workflow;
  return Sparkles;
};
</script>

<style scoped>
.hero-glow {
  position: absolute;
  top: -40%;
  left: 50%;
  transform: translateX(-50%);
  width: 700px;
  height: 700px;
  background: radial-gradient(circle, rgba(212,175,55,0.18), transparent 70%);
  pointer-events: none;
}
.pulse {
  animation: pulseGlow 1.8s infinite;
}
@keyframes pulseGlow {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.4); opacity: 0.6; }
}
</style>

