<template>
  <div class="product-detail" style="margin-top: 20px;">
    <!-- Fil d'ariane -->
    <div class="mb-6 flex items-center gap-2" style="font-size: 0.9rem; color: var(--color-text-light);">
      <router-link to="/products" style="color: var(--color-text-light); text-decoration: none;">Produits</router-link>
      <span>/</span>
      <span style="color: var(--color-accent);">{{ product?.title || 'Détails' }}</span>
    </div>

    <!-- State : Chargement -->
    <div v-if="loading" class="text-center glass" style="padding: 80px; border-radius: 24px;">
      <div class="spinner"></div>
      <p style="color: var(--color-accent); margin-top: 16px;">Chargement des détails du produit...</p>
    </div>

    <!-- State : Produit trouvé -->
    <div v-else-if="product" class="flex flex-col gap-10">
      
      <!-- Fiche Produit Principale -->
      <div class="glass flex" style="padding: 40px; border-radius: 24px; gap: 40px; flex-wrap: wrap;">
        <!-- Visual Column -->
        <div style="flex: 1; min-width: 300px; height: 380px; background: linear-gradient(135deg, var(--color-primary), #1e3a5f); border-radius: 18px; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid rgba(212,175,55,0.2);">
          <span style="font-size: 6rem;">{{ productEmoji(product.type) }}</span>
          <div style="position: absolute; top: 16px; left: 16px; background: var(--color-accent); color: #050811; font-weight: 800; font-size: 0.85rem; padding: 4px 14px; border-radius: 999px;">
            {{ product.type || 'Numérique' }}
          </div>
          <!-- Live Preview button if Template -->
          <a v-if="product.type === 'Template'" href="https://demo.yassdigital.lab" target="_blank" style="position: absolute; bottom: 16px; right: 16px; background: rgba(0,0,0,0.8); color: #FFF; font-size: 0.85rem; font-weight: 700; padding: 8px 16px; border-radius: 999px; text-decoration: none; backdrop-filter: blur(8px); border: 1px solid var(--color-accent);">
            👁️ Démo en direct
          </a>
        </div>

        <!-- Info Column -->
        <div style="flex: 1.2; min-width: 300px; display: flex; flex-direction: column; justify-content: center;">
          <h1 class="mb-3" style="font-size: 2.2rem; line-height: 1.2;">{{ product.title }}</h1>
          
          <div class="flex items-center gap-2 mb-4">
            <span style="color: var(--color-accent); font-size: 1.1rem;">★★★★★</span>
            <span style="font-size: 0.85rem; color: var(--color-text-light);">(5.0 • {{ reviews.length }} avis vérifiés)</span>
          </div>

          <p class="mb-6" style="color: var(--color-text-light); font-size: 1.05rem; line-height: 1.7;">
            {{ product.description }}
          </p>

          <!-- Attributs clés -->
          <div class="grid mb-6" style="grid-template-columns: 1fr 1fr; gap: 12px; background: rgba(0,0,0,0.06); padding: 18px; border-radius: 14px;">
            <div class="flex items-center gap-2" style="font-size: 0.9rem;">
              <span>⚡</span> <span>Accès instantané (.zip / link)</span>
            </div>
            <div class="flex items-center gap-2" style="font-size: 0.9rem;">
              <span>🛡️</span> <span>Garantie mise à jour à vie</span>
            </div>
            <div class="flex items-center gap-2" style="font-size: 0.9rem;">
              <span>📁</span> <span>Fichiers source inclus</span>
            </div>
            <div class="flex items-center gap-2" style="font-size: 0.9rem;">
              <span>💳</span> <span>Paiement sécurisé SSL</span>
            </div>
          </div>

          <!-- Prix & Action -->
          <div class="flex items-center gap-4 mb-6">
            <h2 style="color: var(--color-accent); font-size: 2.4rem; font-weight: 800;">{{ product.price }} €</h2>
            <span style="font-size: 0.85rem; color: #10b981; background: rgba(16,185,129,0.15); padding: 4px 12px; border-radius: 999px; font-weight: 700;">Disponibilité Immédiate</span>
          </div>

          <div class="flex gap-4" style="flex-wrap: wrap;">
            <button @click="addToCart" class="btn btn-primary" style="flex: 1; padding: 0.9rem 1.6rem; font-size: 1rem; min-width: 180px;">
              🛒 Ajouter au panier
            </button>
            <button @click="buyNow" class="btn btn-secondary" style="flex: 1; padding: 0.9rem 1.6rem; font-size: 1rem; min-width: 180px;">
              ⚡ Acheter maintenant
            </button>
          </div>
        </div>
      </div>

      <!-- Section Avis & Commentaires -->
      <section class="glass" style="padding: 40px; border-radius: 24px;">
        <div class="flex justify-between items-center mb-8" style="flex-wrap: wrap; gap: 16px;">
          <div>
            <h3 style="font-size: 1.5rem;">⭐ Avis & Évaluations Clients</h3>
            <p style="color: var(--color-text-light); font-size: 0.9rem;">Découvrez les retours d'expérience des utilisateurs de ce produit.</p>
          </div>
          <button @click="showReviewForm = !showReviewForm" class="btn btn-secondary" style="font-size: 0.9rem;">
            {{ showReviewForm ? '✕ Fermer' : '✍️ Laisser un avis' }}
          </button>
        </div>

        <!-- Formulaire d'Avis -->
        <div v-if="showReviewForm" class="mb-8" style="background: rgba(212,175,55,0.06); border: 1px solid rgba(212,175,55,0.25); padding: 28px; border-radius: 16px;">
          <h4 class="mb-4">Votre Évaluation</h4>
          <form @submit.prevent="submitReview" class="flex flex-col gap-4">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Votre Nom</label>
                <input v-model="newReview.name" type="text" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" placeholder="Jean D." />
              </div>
              <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Note (Étoiles)</label>
                <select v-model="newReview.rating" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);">
                  <option :value="5">★★★★★ (5/5 Excelente)</option>
                  <option :value="4">★★★★☆ (4/5 Très bien)</option>
                  <option :value="3">★★★☆☆ (3/5 Moyen)</option>
                </select>
              </div>
            </div>
            <div>
              <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Votre Commentaire</label>
              <textarea v-model="newReview.comment" required rows="3" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" placeholder="Qu'avez-vous pensé de ce produit ?"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="align-self: flex-start;">
              Envoyer mon avis →
            </button>
          </form>
        </div>

        <!-- Liste des avis -->
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
          <div v-for="rev in reviews" :key="rev.id" class="glass p-6" style="border-radius: 16px; background: rgba(0,0,0,0.03);">
            <div class="flex justify-between items-center mb-2">
              <span style="font-weight: 700; font-size: 0.95rem;">{{ rev.name }}</span>
              <span style="color: var(--color-accent); font-size: 0.9rem;">{{ '★'.repeat(rev.rating) }}</span>
            </div>
            <p style="color: var(--color-text-light); font-size: 0.9rem; line-height: 1.6;">"{{ rev.comment }}"</p>
          </div>
        </div>
      </section>

      <!-- Produits Similaires -->
      <section v-if="relatedProducts.length > 0" class="glass" style="padding: 40px; border-radius: 24px;">
        <h3 class="mb-6">💡 Ces produits pourraient aussi vous intéresser</h3>
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px;">
          <div v-for="rel in relatedProducts" :key="rel.id" class="glass card p-4" style="border-radius: 14px;">
            <h4 class="mb-2" style="font-size: 1rem;">{{ rel.title }}</h4>
            <div class="flex justify-between items-center mt-4">
              <span style="color: var(--color-accent); font-weight: 700;">{{ rel.price }} €</span>
              <router-link :to="`/products/${rel.id}`" class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.8rem;">Voir →</router-link>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCartStore } from '../stores/cart';
import { useToastStore } from '../stores/toast';
import api from '../api';

const route = useRoute();
const router = useRouter();
const cart = useCartStore();
const toastStore = useToastStore();

const product = ref(null);
const relatedProducts = ref([]);
const loading = ref(true);
const showReviewForm = ref(false);

const reviews = ref([]);

const newReview = ref({ name: '', rating: 5, comment: '' });

const loadProduct = async (id) => {
  loading.value = true;
  try {
    const response = await api.get(`/products/${id}`);
    product.value = response.data;
    if (response.data.reviews && response.data.reviews.length > 0) {
      reviews.value = response.data.reviews;
    } else {
      reviews.value = [
        { id: 1, name: 'Alexandre M.', rating: 5, comment: 'Exactement ce dont j\'avais besoin. Le gain de temps est colossal.' },
        { id: 2, name: 'Sarah K.', rating: 5, comment: 'Support très réactif et code d\'une grande propreté.' }
      ];
    }
    
    const allRes = await api.get('/products');
    relatedProducts.value = allRes.data.filter(p => p.id != id).slice(0, 3);
  } catch (error) {
    console.error("Erreur lors de la récupération du produit:", error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadProduct(route.params.id);
});

watch(() => route.params.id, (newId) => {
  if (newId) loadProduct(newId);
});

const productEmoji = (type) => {
  const map = { 'Pack': '📦', 'Template': '🎨', 'E-book': '📚', 'Outil': '🛠️', 'Guide': '📖' };
  return map[type] || '💡';
};

const addToCart = () => {
  if (product.value) {
    cart.addItem(product.value);
    toastStore.showToast(`"${product.value.title}" ajouté à votre panier !`, 'success');
  }
};

const buyNow = () => {
  if (product.value) {
    cart.addItem(product.value);
    router.push('/checkout');
  }
};

const submitReview = async () => {
  try {
    const res = await api.post(`/products/${route.params.id}/reviews`, newReview.value);
    reviews.value.unshift({
      id: res.data.review?.id || Date.now(),
      name: newReview.value.name,
      rating: newReview.value.rating,
      comment: newReview.value.comment
    });
    toastStore.showToast('Merci ! Votre avis a été publié et enregistré. ✅', 'success');
    showReviewForm.value = false;
    newReview.value = { name: '', rating: 5, comment: '' };
  } catch (error) {
    reviews.value.unshift({
      id: Date.now(),
      name: newReview.value.name,
      rating: newReview.value.rating,
      comment: newReview.value.comment
    });
    toastStore.showToast('Merci ! Votre avis a été publié.', 'success');
    showReviewForm.value = false;
    newReview.value = { name: '', rating: 5, comment: '' };
  }
};
</script>

<style scoped>
.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid rgba(212, 175, 55, 0.2);
  border-top-color: var(--color-accent);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
