<template>
  <div class="products-page">
    <!-- SaaS Header Banner -->
    <section class="products-hero-card text-center fade-in">
      <div class="hero-glow"></div>
      <div style="position: relative; z-index: 2;">
        <span class="badge-pill badge-indigo mb-3"><Package :size="13" /> CATALOGUE PRODUITS & TEMPLATES</span>
        <h1 class="hero-title mb-3">{{ t('catalogTitle') }}</h1>
        <p class="hero-sub mb-4">
          {{ t('catalogSub') }}
        </p>
      </div>
    </section>

    <!-- Recherche, Filtres, Tri & Toggle Vue -->
    <div class="filter-command-bar flex gap-3 mb-8" style="flex-wrap: wrap; align-items: center; justify-content: space-between;">
      
      <!-- Barre de recherche -->
      <div style="flex: 1; min-width: 270px; position: relative;">
        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #64748B;">
          <Search :size="18" />
        </span>
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('searchPlaceholder')"
          class="search-text-input"
        />
      </div>

      <!-- Selecteur de tri -->
      <div style="min-width: 190px;">
        <select v-model="sortBy" class="sort-select-input">
          <option value="default">{{ t('sortDefault') }}</option>
          <option value="price-asc">{{ t('sortPriceAsc') }}</option>
          <option value="price-desc">{{ t('sortPriceDesc') }}</option>
          <option value="title-asc">{{ t('sortNameAsc') }}</option>
        </select>
      </div>

      <!-- Curseur Tranche de Prix -->
      <div class="price-range-pill flex items-center gap-3">
        <span style="font-size: 0.85rem; font-weight: 800; color: #F5C027; white-space: nowrap;">Max : {{ maxPriceRange }} FCFA</span>
        <input type="range" min="0" max="600000" step="5000" v-model.number="maxPriceRange" style="width: 100%; cursor: pointer; accent-color: #6366F1;" />
      </div>

      <!-- Toggle Vue (Grille / Liste) -->
      <div class="view-toggle-wrap flex gap-1">
        <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'active-view' : 'inactive-view'" class="view-btn" title="Vue Grille">
          <LayoutGrid :size="16" />
        </button>
        <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'active-view' : 'inactive-view'" class="view-btn" title="Vue Liste">
          <List :size="16" />
        </button>
      </div>
    </div>

    <!-- Pills de Catégories & Favoris -->
    <div class="categories-bar flex gap-2 mb-8" style="flex-wrap: wrap; align-items: center;">
      <button
        v-for="cat in categories"
        :key="cat"
        @click="activeCategory = cat; showFavoritesOnly = false"
        class="cat-filter-pill"
        :class="{ active: activeCategory === cat && !showFavoritesOnly }"
      >
        {{ cat }}
      </button>

      <!-- Bouton Filtre Favoris -->
      <button
        @click="showFavoritesOnly = !showFavoritesOnly"
        class="cat-filter-pill flex items-center gap-1.5"
        :class="{ active: showFavoritesOnly }"
        :style="showFavoritesOnly ? 'background: rgba(239,68,68,0.2) !important; color: #EF4444 !important; border-color: rgba(239,68,68,0.4) !important;' : ''"
        title="Voir mes favoris"
      >
        <Heart :size="13" :style="{ fill: showFavoritesOnly ? '#EF4444' : 'none', color: showFavoritesOnly ? '#EF4444' : '#F43F5E' }" />
        <span>Favoris ({{ wishlist.items.length }})</span>
      </button>

      <span style="color: var(--color-text-muted); font-size: 0.85rem; margin-left: auto; align-self: center; font-weight: 700;">
        {{ filteredProducts.length }} produit(s) affiché(s)
      </span>
    </div>

    <!-- Skeleton Chargement -->
    <div v-if="loading" :class="viewMode === 'grid' ? 'grid' : 'flex flex-col gap-4'" :style="viewMode === 'grid' ? 'grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 28px;' : ''">
      <div v-for="i in 6" :key="i" class="product-item-card" style="padding: 0;">
        <div class="skeleton skeleton-image" style="height: 190px; width: 100%; border-radius: 0;"></div>
        <div style="padding: 24px; display: flex; flex-direction: column; gap: 12px;">
          <div class="skeleton skeleton-badge" style="width: 70px;"></div>
          <div class="skeleton skeleton-text-lg" style="width: 75%;"></div>
          <div class="skeleton skeleton-text" style="width: 100%;"></div>
          <div class="skeleton skeleton-text" style="width: 55%;"></div>
        </div>
      </div>
    </div>

    <!-- Aucun résultat -->
    <div v-else-if="filteredProducts.length === 0" class="text-center no-result-card">
      <div style="display: inline-flex; width: 64px; height: 64px; border-radius: 50%; background: rgba(99,102,241,0.15); align-items: center; justify-content: center; color: #818CF8; margin-bottom: 16px;">
        <SearchX :size="32" />
      </div>
      <h3 style="color: var(--color-text); font-size: 1.25rem; font-weight: 800;">Aucun produit trouvé</h3>
      <p style="color: var(--color-text-muted); margin-top: 6px; font-size: 0.9rem;">Essayez un autre mot-clé ou modifiez la catégorie sélectionnée.</p>
    </div>

    <!-- Grille ou Liste de produits -->
    <div v-else :class="viewMode === 'grid' ? 'grid' : 'flex flex-col gap-4'" :style="viewMode === 'grid' ? 'grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 28px;' : ''">
      <div
        v-for="product in filteredProducts"
        :key="product.id"
        class="product-item-card"
        :class="viewMode === 'grid' ? 'flex flex-col justify-between' : 'flex flex-row items-center justify-between p-4 gap-6'"
      >
        <!-- Card Image -->
        <div class="card-thumb-wrap" :style="viewMode === 'grid' ? 'height: 190px; width: 100%;' : 'height: 100px; width: 140px; flex-shrink: 0; border-radius: 12px;'">
          <div class="thumb-icon-center">
            <component :is="getProductIcon(product.type)" :size="viewMode === 'grid' ? 56 : 36" />
          </div>
          
          <!-- Bouton Favoris CÅ“ur -->
          <button @click.stop="toggleFav(product)" class="fav-heart-btn" title="Ajouter aux favoris">
            <Heart :size="16" :style="{ color: wishlist.isFavorite(product.id) ? '#EF4444' : 'rgba(255,255,255,0.75)', fill: wishlist.isFavorite(product.id) ? '#EF4444' : 'transparent' }" />
          </button>

          <div v-if="product.type && viewMode === 'grid'" class="card-type-tag">
            {{ product.type }}
          </div>
        </div>

        <!-- Card Content -->
        <div :style="viewMode === 'grid' ? 'padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;' : 'flex: 1; display: flex; justify-content: space-between; align-items: center; gap: 20px;'">
          <div>
            <div class="flex items-center gap-2 mb-1.5">
              <h3 class="prod-card-title">{{ product.title }}</h3>
              <span v-if="viewMode === 'list' && product.type" class="card-type-tag" style="position: static;">{{ product.type }}</span>
            </div>
            <p class="prod-card-desc">
              {{ product.description }}
            </p>
          </div>

          <div :style="viewMode === 'grid' ? 'width: 100%;' : 'min-width: 220px; text-align: right;'">
            <div class="flex justify-between items-center mb-4" :style="viewMode === 'grid' ? 'border-top: 1px solid rgba(255,255,255,0.08); padding-top: 16px;' : 'justify-content: flex-end; gap: 12px; margin-bottom: 8px;'">
              <div>
                <span style="font-size: 0.75rem; color: #64748B; font-weight: 700; text-transform: uppercase;">Prix officiel</span>
                <span class="prod-card-price">{{ currencyStore.format(product.price) }}</span>
              </div>
              <div class="flex items-center gap-1" style="color: #F5C027; font-size: 0.85rem; font-weight: 800;">
                <Star :size="14" style="fill: #F5C027;" />
                <span>5.0</span>
              </div>
            </div>
            <div class="flex gap-2">
              <button @click="quickAddToCart(product)" class="btn-card-cart">
                <ShoppingCart :size="15" /> Panier
              </button>
              <router-link :to="`/products/${product.id}`" class="btn-card-details">
                Détails <ArrowRight :size="15" />
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
import { useCurrencyStore } from '../stores/currency';
import { t } from '../i18n';
import api from '../api';
import { 
  Search, 
  SearchX, 
  ShoppingCart, 
  Heart, 
  Star, 
  ArrowRight, 
  Package, 
  Layout, 
  BookOpen, 
  Wrench, 
  FileText, 
  Sparkles,
  LayoutGrid,
  List
} from 'lucide-vue-next';

const cart = useCartStore();
const wishlist = useWishlistStore();
const toastStore = useToastStore();
const currencyStore = useCurrencyStore();

const viewMode = ref('grid'); // 'grid' ou 'list'

const toggleFav = (product) => {
  const isFav = wishlist.isFavorite(product.id);
  wishlist.toggleWishlist(product);
  toastStore.showToast(isFav ? `Retiré des favoris.` : `"${product.title}" ajouté à vos favoris â¤ï¸ !`, isFav ? 'info' : 'success');
};

const products = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const activeCategory = ref('Tous');
const sortBy = ref('default');
const showFavoritesOnly = ref(false);

const maxPriceRange = ref(600000);

const categories = computed(() => {
  const types = [...new Set(products.value.map(p => p.type).filter(Boolean))];
  return ['Tous', ...types];
});

const filteredProducts = computed(() => {
  let result = [...products.value];

  // Filtre par favoris (Wishlist)
  if (showFavoritesOnly.value) {
    result = result.filter(p => wishlist.isFavorite(p.id));
  }

  // Filtre par prix max
  result = result.filter(p => parseFloat(p.price) <= maxPriceRange.value);

  if (activeCategory.value !== 'Tous' && !showFavoritesOnly.value) {
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

const quickAddToCart = (product) => {
  cart.addItem(product);
  toastStore.showToast(`"${product.title}" ajouté au panier !`, 'success');
};

onMounted(async () => {
  try {
    const res = await api.get('/products');
    products.value = Array.isArray(res.data) ? res.data : (res.data?.data || []);
  } catch (e) {
    console.error('Erreur produits:', e);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.products-page {
  padding: 30px 0 80px;
  color: var(--color-text);
}

.products-hero-card {
  padding: 60px 24px;
  margin-bottom: 40px;
  position: relative;
  overflow: hidden;
}
.hero-title {
  color: var(--color-text) !important;
  font-weight: 900 !important;
  font-size: clamp(2rem, 4vw, 3rem) !important;
  letter-spacing: -0.03em !important;
}
.hero-sub {
  color: var(--color-text-muted) !important;
  font-size: 1.05rem;
  max-width: 620px;
  margin: 0 auto;
}

.search-text-input {
  width: 100%;
  padding: 13px 16px 13px 44px;
  border-radius: 999px;
  border: 1px solid var(--color-border);
  background: var(--color-bg-card);
  color: var(--color-text);
  font-size: 0.92rem;
  transition: all 0.22s ease;
  box-shadow: var(--shadow-sm);
}
.search-text-input:focus {
  outline: none;
  border-color: rgba(124,58,237,0.7);
  box-shadow: 0 0 0 3px rgba(124,58,237,0.2);
}
.search-text-input::placeholder { color: #475569; }

.sort-select-input {
  width: 100%;
  padding: 13px 16px;
  border-radius: 999px;
  border: 1px solid var(--color-border);
  background: var(--color-bg-card);
  color: var(--color-text);
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.22s ease;
  box-shadow: var(--shadow-sm);
}
.sort-select-input:focus {
  outline: none;
  border-color: rgba(124,58,237,0.6);
  box-shadow: 0 0 0 3px rgba(124,58,237,0.18);
}

.price-range-pill {
  padding: 11px 18px;
  border-radius: 999px;
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  box-shadow: var(--shadow-sm);
  min-width: 230px;
}

.view-toggle-wrap {
  padding: 4px;
  border-radius: 999px;
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  box-shadow: var(--shadow-sm);
}
.view-btn {
  padding: 8px 12px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.active-view {
  background: linear-gradient(135deg, #7C3AED, #6D28D9);
  color: var(--color-text);
  box-shadow: 0 4px 12px rgba(124,58,237,0.4);
}
.inactive-view {
  background: transparent;
  color: #64748B;
}
.inactive-view:hover { color: var(--color-text-muted); }

.cat-filter-pill {
  padding: 8px 20px;
  border-radius: 999px;
  font-size: 0.84rem;
  font-weight: 700;
  cursor: pointer;
  border: 1px solid var(--color-border);
  background: var(--color-bg-card);
  color: var(--color-text-muted);
  transition: all 0.22s ease;
  letter-spacing: 0.01em;
}
.cat-filter-pill:hover {
  color: var(--color-text);
  border-color: var(--color-primary);
  background: rgba(124,58,237,0.05);
}
.cat-filter-pill.active {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: #FFFFFF;
}

/* â”FCFAâ”FCFA Product Cards â”FCFAâ”FCFA */
.product-item-card {
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: all 0.2s;
  position: relative;
}
.product-item-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}

/* Banner area — per-type color tints */
.card-thumb-wrap {
  background: var(--color-bg-2);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom: 1px solid var(--color-border);
}
.thumb-icon-center {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 76px; height: 76px;
  border-radius: 22px;
  background: rgba(124,58,237,0.1);
  border: 1px solid rgba(124,58,237,0.2);
  color: var(--color-primary);
  transition: all 0.35s ease;
}
.product-item-card:hover .thumb-icon-center {
  background: rgba(124,58,237,0.15);
  border-color: rgba(124,58,237,0.3);
  transform: scale(1.06);
}

.fav-heart-btn {
  position: absolute;
  top: 12px;
  left: 12px;
  background: var(--color-bg-elevated);
  border: 1px solid var(--color-border);
  border-radius: 50%;
  width: 34px; height: 34px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.22s ease;
  z-index: 2;
  box-shadow: var(--shadow-sm);
}
.fav-heart-btn:hover {
  transform: scale(1.05);
  box-shadow: var(--shadow-md);
  border-color: rgba(239,68,68,0.5);
}
.fav-heart-btn:active {
  transform: scale(0.95);
  box-shadow: var(--shadow-inset);
}

.card-type-tag {
  position: absolute;
  top: 12px;
  right: 12px;
  font-size: 0.72rem;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 999px;
  background: rgba(245,158,11,0.1);
  color: #D97706;
  border: 1px solid rgba(245,158,11,0.2);
  letter-spacing: 0.04em;
  text-transform: uppercase;
  z-index: 2;
}

/* Card content */
.prod-card-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--color-text) !important;
  margin: 0;
  line-height: 1.35;
  letter-spacing: -0.01em;
}

.prod-card-desc {
  color: #64748B !important;
  font-size: 0.87rem;
  line-height: 1.65;
  margin-bottom: 14px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.prod-card-price {
  font-weight: 900;
  font-size: 1.35rem;
  color: var(--color-primary) !important;
  display: block;
  font-family: var(--font-title);
}

/* CTA Buttons */
.btn-card-cart {
  flex: 1;
  padding: 10px 14px;
  border-radius: 10px;
  background: rgba(124,58,237,0.08);
  border: 1px solid rgba(124,58,237,0.2);
  color: var(--color-primary);
  font-weight: 700;
  font-size: 0.85rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.22s ease;
}
.btn-card-cart:hover {
  background: rgba(124,58,237,0.15);
  border-color: rgba(124,58,237,0.3);
  transform: translateY(-1px);
}

.btn-card-details {
  flex: 1;
  padding: 10px 14px;
  border-radius: 10px;
  background: linear-gradient(135deg, #7C3AED, #6D28D9);
  border: none;
  color: var(--color-text) !important;
  font-weight: 800;
  font-size: 0.85rem;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.22s ease;
  box-shadow: 0 6px 18px rgba(124,58,237,0.4);
}
.btn-card-details:hover {
  background: linear-gradient(135deg, #6D28D9, #5B21B6);
  box-shadow: 0 10px 26px rgba(124,58,237,0.55);
  transform: translateY(-2px);
}

.no-result-card {
  padding: 60px 24px;
  border-radius: var(--radius-xl);
  background: var(--color-bg-elevated);
  border: 1px dashed var(--color-border);
}
</style>

