<template>
  <div class="product-detail" style="margin-top: 24px;">
    <!-- Fil d'ariane -->
    <div class="mb-6 flex items-center gap-2" style="font-size: 0.88rem; color: var(--color-text-muted);">
      <router-link to="/products" style="color: var(--color-text-muted); text-decoration: none;" class="flex items-center gap-1">
        <ArrowLeft :size="14" /> Produits
      </router-link>
      <span>/</span>
      <span style="color: var(--color-primary); font-weight: 600;">{{ product?.title || 'Détails' }}</span>
    </div>

    <!-- State : Chargement -->
    <div v-if="loading" class="text-center glass" style="padding: 80px; border-radius: var(--radius-lg);">
      <div class="spinner"></div>
      <p style="color: var(--color-primary); margin-top: 16px; font-weight: 600;">Chargement des détails du produit...</p>
    </div>

    <!-- State : Produit trouvé -->
    <div v-else-if="product" class="flex flex-col gap-10">
      
      <!-- Fiche Produit Principale -->
      <div class="glass flex" style="padding: 42px; border-radius: var(--radius-lg); gap: 40px; flex-wrap: wrap;">
        <!-- Visual Column -->
        <div style="flex: 1; min-width: 300px; height: 380px; background: linear-gradient(135deg, var(--color-bg-elevated) 0%, var(--color-bg-2) 50%, var(--color-bg-card) 100%); border-radius: var(--radius-md); position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid rgba(255,255,255,0.08);">
          <div style="display: flex; align-items: center; justify-content: center; width: 140px; height: 140px; border-radius: 32px; background: rgba(124,58,237,0.18); border: 2px solid rgba(124,58,237,0.3); color: var(--color-primary); box-shadow: 0 0 40px rgba(124,58,237,0.3);">
            <component :is="getProductIcon(product.type)" :size="64" />
          </div>
          <div class="badge-pill badge-gold" style="position: absolute; top: 16px; left: 16px; font-size: 0.85rem;">
            {{ product.type || 'Numérique' }}
          </div>
          <!-- Live Preview button if Template -->
          <a v-if="product.type === 'Template'" href="https://demo.yassdigital.lab" target="_blank" style="position: absolute; bottom: 16px; right: 16px; background: rgba(0,0,0,0.75); color: var(--color-text); font-size: 0.85rem; font-weight: 700; padding: 8px 18px; border-radius: var(--radius-pill); text-decoration: none; backdrop-filter: blur(8px); border: 1px solid var(--color-primary); display: inline-flex; align-items: center; gap: 6px;">
            <ExternalLink :size="14" /> Démo en direct
          </a>
        </div>

        <!-- Info Column -->
        <div style="flex: 1.2; min-width: 300px; display: flex; flex-direction: column; justify-content: center;">
          <h1 class="mb-3" style="font-size: 2.2rem; line-height: 1.25;">{{ product.title }}</h1>
          
          <div class="flex items-center gap-2 mb-4">
            <div class="flex gap-0.5" style="color: var(--color-accent);">
              <Star v-for="n in 5" :key="n" :size="16" style="fill: var(--color-accent);" />
            </div>
            <span style="font-size: 0.88rem; color: var(--color-text-muted); font-weight: 600;">(5.0 • {{ reviews.length }} avis vérifiés)</span>
          </div>

          <p class="mb-6" style="color: var(--color-text-light); font-size: 1.05rem; line-height: 1.7;">
            {{ product.description }}
          </p>

          <!-- Attributs clés -->
          <div class="grid mb-6" style="grid-template-columns: 1fr 1fr; gap: 14px; background: rgba(99,102,241,0.06); padding: 20px; border-radius: var(--radius-md); border: 1px solid rgba(99,102,241,0.15);">
            <div class="flex items-center gap-2" style="font-size: 0.9rem; font-weight: 600;">
              <Zap :size="18" style="color: #10B981;" /> <span>Accès instantané (.zip / link)</span>
            </div>
            <div class="flex items-center gap-2" style="font-size: 0.9rem; font-weight: 600;">
              <ShieldCheck :size="18" style="color: var(--color-primary);" /> <span>Mises à jour à vie incluses</span>
            </div>
            <div class="flex items-center gap-2" style="font-size: 0.9rem; font-weight: 600;">
              <CheckCircle2 :size="18" style="color: #8B5CF6;" /> <span>Fichiers source complets</span>
            </div>
            <div class="flex items-center gap-2" style="font-size: 0.9rem; font-weight: 600;">
              <Lock :size="18" style="color: var(--color-accent);" /> <span>Paiement 100% sécurisé</span>
            </div>
          </div>

          <!-- Prix & Action -->
          <div class="flex items-center gap-4 mb-6" style="flex-wrap: wrap;">
            <h2 style="color: var(--color-primary); font-size: 2.2rem; font-weight: 800;">{{ currencyStore.formatDual(product.price) }}</h2>
            <span class="badge-pill badge-emerald" style="font-size: 0.85rem;">Disponibilité Immédiate</span>
          </div>

          <div class="flex gap-4" style="flex-wrap: wrap;">
            <button @click="addToCart" class="btn btn-primary" style="flex: 1; padding: 0.95rem 1.6rem; font-size: 1rem; min-width: 180px;">
              <ShoppingCart :size="18" /> Ajouter au panier
            </button>
            <button @click="buyNow" class="btn btn-gold" style="flex: 1; padding: 0.95rem 1.6rem; font-size: 1rem; min-width: 180px;">
              <Zap :size="18" /> Acheter maintenant
            </button>
          </div>

          <!-- Social Share Row -->
          <div class="flex items-center gap-2 mt-4 pt-4" style="border-top: 1px solid var(--color-border); font-size: 0.82rem; color: var(--color-text-muted); flex-wrap: wrap;">
            <span style="font-weight: 700;">Partager :</span>
            <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent('Découvrez ' + (product?.title || '') + ' sur Yass Digital Lab : ' + currentUrl)" target="_blank" class="btn btn-secondary flex items-center gap-1" style="padding: 5px 12px; font-size: 0.76rem; color: #25D366; border-color: rgba(37,211,102,0.3);">
              <MessageSquare :size="13" /> WhatsApp
            </a>
            <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent('Découvrez ' + (product?.title || '') + ' sur Yass Digital Lab') + '&url=' + encodeURIComponent(currentUrl)" target="_blank" class="btn btn-secondary flex items-center gap-1" style="padding: 5px 12px; font-size: 0.76rem; color: #38BDF8; border-color: rgba(56,189,248,0.3);">
              <Share2 :size="13" /> Twitter/X
            </a>
            <button @click="copyProductLink" class="btn btn-secondary flex items-center gap-1" style="padding: 5px 12px; font-size: 0.76rem;">
              <Copy :size="13" /> Copier le lien
            </button>
          </div>
        </div>
      </div>

      <!-- Section Onglets (Description Détaillée / Spécifications / FAQ) -->
      <section class="glass p-8" style="border-radius: var(--radius-lg);">
        <div class="tabs mb-6">
          <button 
            @click="activeTab = 'description'" 
            :class="['tab-btn', { active: activeTab === 'description' }]"
          >
            Description détaillée
          </button>
          <button 
            @click="activeTab = 'specs'" 
            :class="['tab-btn', { active: activeTab === 'specs' }]"
          >
            Spécifications & Inclus
          </button>
          <button 
            @click="activeTab = 'faq'" 
            :class="['tab-btn', { active: activeTab === 'faq' }]"
          >
            FAQ & Licence
          </button>
        </div>

        <div v-if="activeTab === 'description'" class="fade-in">
          <h3 class="mb-4">À propos de cette ressource</h3>
          <p style="color: var(--color-text-light); line-height: 1.8; font-size: 1rem;">
            {{ product.description }}
          </p>
          <div class="mt-6 flex flex-col gap-3">
            <h4 style="color: var(--color-primary);">Points forts :</h4>
            <ul style="list-style-type: disc; padding-left: 20px; color: var(--color-text-light); line-height: 1.7;">
              <li>Optimisé pour la vitesse, la performance et le SEO.</li>
              <li>Structure modulaire propre et facile à personnaliser.</li>
              <li>Documentation complète et support réactif fourni.</li>
            </ul>
          </div>
        </div>

        <div v-else-if="activeTab === 'specs'" class="fade-in">
          <h3 class="mb-4">Ce qui est inclus dans le téléchargement</h3>
          <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div class="glass p-4" style="border-radius: var(--radius-md);">
              <h4 style="font-size: 0.95rem; color: var(--color-primary);" class="mb-1">Format Fichiers</h4>
              <p style="font-size: 0.9rem; color: var(--color-text-muted);">ZIP, JSON, Markdown, Code source</p>
            </div>
            <div class="glass p-4" style="border-radius: var(--radius-md);">
              <h4 style="font-size: 0.95rem; color: var(--color-primary);" class="mb-1">Version & Tech</h4>
              <p style="font-size: 0.9rem; color: var(--color-text-muted);">Vue 3, Tailwind/CSS, REST API</p>
            </div>
            <div class="glass p-4" style="border-radius: var(--radius-md);">
              <h4 style="font-size: 0.95rem; color: var(--color-primary);" class="mb-1">Licence commerciale</h4>
              <p style="font-size: 0.9rem; color: var(--color-text-muted);">Projets illimités personnels & pro</p>
            </div>
          </div>
        </div>

        <div v-else-if="activeTab === 'faq'" class="fade-in">
          <h3 class="mb-4">Questions fréquentes sur ce produit</h3>
          <p style="color: var(--color-text-light); line-height: 1.7;">
            <strong>Puis-je réutiliser cette ressource pour mes clients ?</strong><br/>
            Oui, notre licence vous permet d'utiliser et d'adapter cette ressource pour vos projets clients et personnels.
          </p>
        </div>
      </section>

      <!-- Section Avis & Commentaires -->
      <section style="padding: 40px; border-radius: var(--radius-lg); background: var(--color-bg-card); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
        <div class="flex justify-between items-center mb-8" style="flex-wrap: wrap; gap: 16px;">
          <div>
            <div class="flex items-center gap-3">
              <h3 class="flex items-center gap-2" style="font-size: 1.45rem; margin: 0;">
                <Star :size="22" style="color: var(--color-accent); fill: var(--color-accent);" /> Avis & Évaluations Clients
              </h3>
              <span class="badge-pill badge-gold" style="font-size: 0.82rem;">
                â­ {{ avgRating }} / 5.0 ({{ reviews.length }} avis)
              </span>
            </div>
            <p style="color: var(--color-text-muted); font-size: 0.9rem; margin-top: 4px;">Découvrez les retours d'expérience vérifiés des utilisateurs de ce produit.</p>
          </div>
          <button @click="showReviewForm = !showReviewForm" class="btn btn-secondary" style="font-size: 0.9rem;">
            {{ showReviewForm ? 'Fermer' : 'Laisser un avis' }}
          </button>
        </div>

        <!-- Formulaire d'Avis -->
        <div v-if="showReviewForm" class="mb-8" style="background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.2); padding: 28px; border-radius: var(--radius-md);">
          <h4 class="mb-4">Votre Évaluation</h4>
          <form @submit.prevent="submitReview" class="flex flex-col gap-4">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <label style="display: block; margin-bottom: 6px; font-size: 0.88rem;">Votre Nom</label>
                <input v-model="newReview.name" type="text" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text);" placeholder="Jean D." />
              </div>
              <div>
                <label style="display: block; margin-bottom: 6px; font-size: 0.88rem;">Note (Étoiles)</label>
                <select v-model.number="newReview.rating" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text);">
                  <option :value="5">★★★★★ (5/5 Excellent)</option>
                  <option :value="4">★★★★☆ (4/5 Très bien)</option>
                  <option :value="3">★★★☆☆ (3/5 Moyen)</option>
                  <option :value="2">★★☆☆☆ (2/5 Passable)</option>
                  <option :value="1">★☆☆☆☆ (1/5 Insatisfaisant)</option>
                </select>
              </div>
            </div>
            <div>
              <label style="display: block; margin-bottom: 6px; font-size: 0.88rem;">Votre Commentaire</label>
              <textarea v-model="newReview.comment" required rows="3" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text);" placeholder="Partagez votre expérience avec ce produit..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="align-self: flex-start;">
              Envoyer mon avis <Send :size="16" />
            </button>
          </form>
        </div>

        <!-- Rating Filter Pills -->
        <div v-if="reviews.length > 0" class="flex items-center gap-2 mb-6 flex-wrap">
          <span style="font-size: 0.82rem; color: var(--color-text-muted); font-weight: 600;">Filtrer par note :</span>
          <button 
            v-for="rOption in [0, 5, 4, 3]" 
            :key="rOption"
            @click="selectedRatingFilter = rOption"
            class="badge-pill"
            :style="selectedRatingFilter === rOption ? 'background: #6366F1; color: var(--color-text); border: 1px solid #818CF8; cursor: pointer; font-weight: 800;' : 'background: rgba(255,255,255,0.06); color: var(--color-text); border: 1px solid var(--color-border); cursor: pointer;'"
          >
            {{ rOption === 0 ? 'Tous les avis (' + reviews.length + ')' : rOption + ' ★ (' + countByRating(rOption) + ')' }}
          </button>
        </div>

        <!-- Liste des avis -->
        <div v-if="filteredReviews.length === 0" style="text-align: center; color: var(--color-text-muted); padding: 30px;">
          Aucun avis trouvé pour cette note.
        </div>
        <div v-else class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
          <div v-for="rev in filteredReviews" :key="rev.id" class="glass p-6 flex flex-col justify-between" style="border-radius: var(--radius-md); background: var(--color-bg-card);">
            <div>
              <div class="flex justify-between items-center mb-2">
                <div class="flex items-center gap-2">
                  <span style="font-weight: 700; font-size: 0.95rem;">{{ rev.name }}</span>
                  <span v-if="rev.verified_buyer" class="badge-pill badge-emerald" style="font-size: 0.68rem; padding: 2px 7px;">
                    <CheckCircle2 :size="11" /> Acheteur Vérifié
                  </span>
                </div>
                <div class="flex gap-0.5" style="color: var(--color-accent);">
                  <Star v-for="n in (rev.rating || 5)" :key="n" :size="14" style="fill: var(--color-accent);" />
                </div>
              </div>
              <p style="color: var(--color-text-light); font-size: 0.9rem; line-height: 1.6; margin-bottom: 12px;">"{{ rev.comment }}"</p>
            </div>

            <!-- Réponse Officielle Administrateur -->
            <div v-if="rev.admin_reply" style="margin-top: 12px; padding: 12px 14px; border-radius: var(--radius-sm); background: rgba(99,102,241,0.08); border-left: 3px solid var(--color-primary);">
              <span style="display: flex; align-items: center; gap: 5px; font-weight: 800; font-size: 0.78rem; color: var(--color-primary); margin-bottom: 4px;">
                💬 Réponse de l'équipe Yass Digital Lab
              </span>
              <p style="font-size: 0.85rem; color: var(--color-text); margin: 0; line-height: 1.5;">
                {{ rev.admin_reply }}
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Produits Similaires -->
      <section v-if="relatedProducts.length > 0" style="padding: 40px; border-radius: var(--radius-lg); background: var(--color-bg-card); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
        <h3 class="mb-6 flex items-center gap-2">
          <Sparkles :size="20" style="color: var(--color-primary);" /> Ces produits pourraient aussi vous intéresser
        </h3>
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px;">
          <div v-for="rel in relatedProducts" :key="rel.id" class="glass card p-4" style="border-radius: var(--radius-md);">
            <h4 class="mb-2" style="font-size: 1rem;">{{ rel.title }}</h4>
            <div class="flex justify-between items-center mt-4">
              <span style="color: var(--color-primary); font-weight: 800;">{{ currencyStore.format(rel.price) }}</span>
              <router-link :to="`/products/${rel.id}`" class="btn btn-secondary btn-sm">Voir</router-link>
            </div>
          </div>
        </div>
      </section>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useHead } from '@vueuse/head';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';
import { useToastStore } from '../stores/toast';
import { useCurrencyStore } from '../stores/currency';
import api from '../api';
import { 
  ArrowLeft, Star, Zap, ShieldCheck, CheckCircle2, Lock, 
  ShoppingCart, ExternalLink, Send, Sparkles, Package, Layout, BookOpen, Wrench, FileText,
  MessageSquare, Share2, Copy
} from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const cart = useCartStore();
const auth = useAuthStore();
const toastStore = useToastStore();
const currencyStore = useCurrencyStore();

const product = ref(null);
const loading = ref(true);
const activeTab = ref('description');
const showReviewForm = ref(false);
const reviews = ref([]);
const selectedRatingFilter = ref(0);

const currentUrl = computed(() => {
  return typeof window !== 'undefined' ? window.location.href : '';
});

const copyProductLink = () => {
  if (typeof navigator !== 'undefined' && navigator.clipboard) {
    navigator.clipboard.writeText(window.location.href);
    toastStore.showToast('Lien du produit copié dans le presse-papier ! 📋', 'success');
  }
};

const countByRating = (stars) => {
  return reviews.value.filter(r => (r.rating || 5) === stars).length;
};

const filteredReviews = computed(() => {
  if (selectedRatingFilter.value === 0) return reviews.value;
  return reviews.value.filter(r => (r.rating || 5) === selectedRatingFilter.value);
});

const avgRating = computed(() => {
  if (!reviews.value || reviews.value.length === 0) return '5.0';
  const total = reviews.value.reduce((sum, r) => sum + (r.rating || 5), 0);
  return (total / reviews.value.length).toFixed(1);
});

const newReview = ref({ name: '', rating: 5, comment: '' });
const relatedProducts = ref([]);

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

const addToCart = () => {
  if (!product.value) return;
  cart.addItem(product.value);
  toastStore.showToast(`"${product.value.title}" ajouté au panier !`, 'success');
};

const buyNow = () => {
  addToCart();
  router.push('/checkout');
};

const submitReview = async () => {
  if (!newReview.value.name || !newReview.value.comment) return;
  try {
    const payload = {
      ...newReview.value,
      email: auth.user?.email || ''
    };
    const res = await api.post(`/products/${product.value.id}/reviews`, payload);
    const addedReview = res.data?.review || { id: Date.now(), ...newReview.value, verified_buyer: false };
    reviews.value.unshift(addedReview);
    toastStore.showToast('Votre avis a été publié avec succès ! 🌟', 'success');
  } catch(e) {
    reviews.value.unshift({ id: Date.now(), ...newReview.value });
    toastStore.showToast('Avis enregistré !', 'success');
  }
  newReview.value = { name: auth.user?.name || '', rating: 5, comment: '' };
  showReviewForm.value = false;
};

onMounted(async () => {
  const id = route.params.id;
  try {
    const res = await api.get(`/products/${id}`);
    product.value = res.data;

    // Mise à jour des balises SEO (Head)
    useHead({
      title: computed(() => `${product.value?.title || 'Produit'} | Yass Digital Lab`),
      meta: [
        { name: 'description', content: computed(() => product.value?.description?.substring(0, 150) || 'Découvrez ce produit premium sur Yass Digital Lab.') },
        { property: 'og:title', content: computed(() => product.value?.title || 'Produit') },
        { property: 'og:description', content: computed(() => product.value?.description?.substring(0, 150) || '') },
        { property: 'og:type', content: 'product' },
        { property: 'og:image', content: computed(() => product.value?.image ? `http://localhost:8000/storage/${product.value.image}` : '') }
      ]
    });

    if (res.data?.reviews && Array.isArray(res.data.reviews)) {
      reviews.value = res.data.reviews.filter(r => r.status === 'approved' || r.is_published !== false);
    }
    
    if (auth.user?.name) {
      newReview.value.name = auth.user.name;
    }
    
    // Produits similaires (Cross-Selling)
    try {
      const crossRes = await api.get(`/products/${id}/cross-sell`);
      relatedProducts.value = crossRes.data;
    } catch(e) {
      // Fallback
      const allRes = await api.get('/products');
      const all = Array.isArray(allRes.data) ? allRes.data : (allRes.data?.data || []);
      relatedProducts.value = all.filter(p => p.id != id).slice(0, 3);
    }
  } catch (e) {
    console.error('Erreur chargement produit:', e);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.product-detail {
  background: var(--page-gradient);
  padding: 30px 0 80px;
  color: var(--color-text);
}
</style>

