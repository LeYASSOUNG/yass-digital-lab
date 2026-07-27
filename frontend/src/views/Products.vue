<template>
  <div class="products-page">
    <!-- Header -->
    <section class="glass text-center" style="padding: 70px 20px; border-radius: 24px; margin-top: 20px; margin-bottom: 40px;">
      <div style="font-size: 3rem; margin-bottom: 12px;">🛍️</div>
      <h1 class="mb-2" style="font-size: 2.2rem;">{{ t('catalogTitle') }}</h1>
      <p style="color: var(--color-text-light); font-size: 1.05rem;">{{ t('catalogSub') }}</p>
    </section>

    <!-- Recherche, Filtres & Tri -->
    <div class="flex gap-4 mb-8" style="flex-wrap: wrap; align-items: center; justify-content: space-between;">
      
      <!-- Barre de recherche -->
      <div style="flex: 1; min-width: 260px; position: relative;">
        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);">🔍</span>
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('searchPlaceholder')"
          style="width: 100%; padding: 12px 16px 12px 40px; border-radius: 12px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.95rem;"
        />
      </div>

      <!-- Selecteur de tri -->
      <div style="min-width: 180px;">
        <select v-model="sortBy" style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.9rem; cursor: pointer;">
          <option value="default">{{ t('sortDefault') }}</option>
          <option value="price-asc">{{ t('sortPriceAsc') }}</option>
          <option value="price-desc">{{ t('sortPriceDesc') }}</option>
          <option value="title-asc">{{ t('sortNameAsc') }}</option>
        </select>
      </div>

      <!-- Curseur Tranche de Prix -->
      <div class="flex items-center gap-3 glass" style="padding: 8px 16px; border-radius: 12px; min-width: 220px;">
        <span style="font-size: 0.85rem; font-weight: 700; color: var(--color-accent); white-space: nowrap;">Max : {{ maxPriceRange }} €</span>
        <input type="range" min="5" max="100" step="5" v-model.number="maxPriceRange" style="width: 100%; cursor: pointer; accent-color: var(--color-accent);" />
      </div>
    </div>

    <!-- Pills de Catégories -->
    <div class="flex gap-2 mb-8" style="flex-wrap: wrap; items-center;">
      <button
        v-for="cat in categories"
        :key="cat"
        @click="activeCategory = cat"
        class="btn"
        :style="activeCategory === cat ? 'background: var(--color-accent); color: #050811; border-color: var(--color-accent); font-weight: 800;' : 'background: var(--color-bg-card); border: 1px solid var(--color-border); color: var(--color-text);'"
        style="padding: 7px 18px; border-radius: 999px; font-size: 0.85rem; cursor: pointer;"
      >
        {{ cat }}
      </button>
      <span style="color: var(--color-text-light); font-size: 0.85rem; margin-left: auto; align-self: center;">
        {{ filteredProducts.length }} produit(s) trouvé(s)
      </span>
    </div>

    <!-- Chargement -->
    <div v-if="loading" class="text-center" style="padding: 80px;">
      <div class="spinner"></div>
      <p style="color: var(--color-accent); margin-top: 16px;">Chargement des produits...</p>
    </div>

    <!-- Aucun résultat -->
    <div v-else-if="filteredProducts.length === 0" class="text-center glass" style="padding: 60px; border-radius: 20px;">
      <p style="font-size: 3rem; margin-bottom: 16px;">🔍</p>
      <h3>Aucun produit trouvé</h3>
      <p style="color: var(--color-text-light);">Essayez un autre mot-clé ou catégorie.</p>
    </div>

    <!-- Grille de produits -->
    <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 28px;">
      <div
        v-for="product in filteredProducts"
        :key="product.id"
        class="card glass glass-hover"
        style="border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;"
      >
        <div class="card-image" style="height: 190px; background: linear-gradient(135deg, var(--color-primary), #1e3a5f); position: relative; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--color-border);">
          <span style="font-size: 4.5rem;">{{ productEmoji(product.type) }}</span>
          
          <!-- Bouton Favoris Cœur -->
          <button @click.stop="toggleFav(product)" style="position: absolute; top: 12px; left: 12px; background: rgba(0,0,0,0.6); border: none; border-radius: 50%; width: 34px; height: 34px; cursor: pointer; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
            <span :style="wishlist.isFavorite(product.id) ? 'color: #ef4444;' : 'color: rgba(255,255,255,0.7);'" style="font-size: 1.1rem;">
              {{ wishlist.isFavorite(product.id) ? '❤️' : '🤍' }}
            </span>
          </button>

          <div v-if="product.type" style="position: absolute; top: 12px; right: 12px; background: var(--color-accent); color: #050811; font-size: 0.75rem; font-weight: 800; padding: 4px 12px; border-radius: 999px;">
            {{ product.type }}
          </div>
          <!-- Badge Live Demo pour les templates -->
          <a v-if="product.type === 'Template'" href="https://demo.yassdigital.lab" target="_blank" @click.stop style="position: absolute; bottom: 12px; left: 12px; background: rgba(0,0,0,0.7); color: #FFF; font-size: 0.75rem; font-weight: 600; padding: 3px 10px; border-radius: 999px; backdrop-filter: blur(8px);">
            {{ t('liveDemo') }}
          </a>
        </div>

        <div style="padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <h3 class="mb-2" style="font-size: 1.2rem;">{{ product.title }}</h3>
            <p style="color: var(--color-text-light); font-size: 0.9rem; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ product.description }}</p>
          </div>

          <div>
            <div class="flex justify-between items-center mb-4" style="border-top: 1px solid var(--color-border); padding-top: 16px;">
              <span style="font-weight: 800; font-size: 1.4rem; color: var(--color-accent);">{{ product.price }} €</span>
              <span style="font-size: 0.8rem; color: var(--color-text-light);">★★★★★ (5.0)</span>
            </div>
            <div class="flex gap-2">
              <button @click="quickAddToCart(product)" class="btn btn-secondary" style="flex: 1; padding: 0.5rem 0.8rem; font-size: 0.85rem;">
                🛒 Panier
              </button>
              <router-link :to="`/products/${product.id}`" class="btn btn-primary" style="flex: 1; padding: 0.5rem 0.8rem; font-size: 0.85rem;">
                Détails →
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useCartStore } from '../stores/cart';
import { useWishlistStore } from '../stores/wishlist';
import { useToastStore } from '../stores/toast';
import { t } from '../i18n';
import axios from 'axios';

const cart = useCartStore();
const wishlist = useWishlistStore();
const toastStore = useToastStore();

const toggleFav = (product) => {
  const isFav = wishlist.isFavorite(product.id);
  wishlist.toggleWishlist(product);
  toastStore.showToast(isFav ? `Retiré des favoris.` : `"${product.title}" ajouté à vos favoris ❤️ !`, isFav ? 'info' : 'success');
};

const products = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const activeCategory = ref('Tous');
const sortBy = ref('default');

const maxPriceRange = ref(100);

const categories = computed(() => {
  const types = [...new Set(products.value.map(p => p.type).filter(Boolean))];
  return ['Tous', ...types];
});

const filteredProducts = computed(() => {
  let result = [...products.value];

  // Filtre par prix max
  result = result.filter(p => parseFloat(p.price) <= maxPriceRange.value);

  if (activeCategory.value !== 'Tous') {
    result = result.filter(p => p.type === activeCategory.value);
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    result = result.filter(p =>
      p.title?.toLowerCase().includes(q) ||
      p.description?.toLowerCase().includes(q)
    );
  }

  if (sortBy.value === 'price-asc') {
    result.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
  } else if (sortBy.value === 'price-desc') {
    result.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
  } else if (sortBy.value === 'title-asc') {
    result.sort((a, b) => a.title.localeCompare(b.title));
  }

  return result;
});

const productEmoji = (type) => {
  const map = { 'Pack': '📦', 'Template': '🎨', 'E-book': '📚', 'Outil': '🛠️', 'Guide': '📖' };
  return map[type] || '💡';
};

const quickAddToCart = (product) => {
  cart.addItem(product);
  toastStore.showToast(`"${product.title}" ajouté au panier !`, 'success');
};

onMounted(async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/products');
    products.value = res.data;
  } catch (e) {
    console.error('Erreur produits:', e);
  } finally {
    loading.value = false;
  }
});
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
