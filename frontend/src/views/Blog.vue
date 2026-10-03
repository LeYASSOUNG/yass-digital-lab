<template>
  <div class="blog-page">
    <!-- Header SaaS -->
    <section class="text-center mb-8 fade-in" style="padding: 60px 24px; margin-top: 20px; position: relative; overflow: hidden;">
      <div class="hero-glow"></div>
      <div style="position: relative; z-index: 2;">
        <span class="badge-pill badge-indigo mb-3"><BookOpen :size="13" /> ACTUALITÉS & GUIDES TECHNIQUES</span>
        <h1 class="mb-3" style="font-size: 2.2rem; font-weight: 800; color: var(--color-text);">Le Blog & Insights Yass Digital Lab</h1>
        <p style="color: var(--color-text-muted); font-size: 1.05rem; max-width: 620px; margin: 0 auto 24px;">
          Tutoriels sur les Agents IA, architectures SaaS modernes, bonnes pratiques Laravel & Vue.js et astuces d'automatisation.
        </p>

        <!-- Search Bar -->
        <div style="max-width: 480px; margin: 0 auto; position: relative;">
          <Search :size="18" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted);" />
          <input 
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher un article, un prompt ou une technologie..."
            class="search-text-input"
            style="width: 100%; padding: 12px 18px 12px 46px; border-radius: 999px; border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text); font-size: 0.92rem; box-shadow: var(--shadow-sm);"
          />
        </div>
      </div>
    </section>

    <!-- Categories Filter Bar -->
    <div class="flex gap-2 mb-8 justify-between items-center" style="flex-wrap: wrap; padding: 0 10px;">
      <div class="flex gap-2" style="flex-wrap: wrap;">
        <button 
          v-for="cat in categories" 
          :key="cat"
          @click="activeCategory = cat"
          class="cat-filter-pill"
          :class="{ active: activeCategory === cat }"
        >
          {{ cat }}
        </button>
      </div>
      <span style="color: var(--color-text-muted); font-size: 0.85rem; font-weight: 700;">
        {{ filteredPosts.length }} article(s) trouvé(s)
      </span>
    </div>

    <!-- Chargement Skeleton -->
    <div v-if="loading" class="grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 28px;">
      <div v-for="i in 3" :key="i" class="skeleton skeleton-card" style="border-radius: var(--radius-lg); height: 350px;"></div>
    </div>

    <!-- Aucun article -->
    <div v-else-if="filteredPosts.length === 0" class="text-center" style="padding: 80px 24px; border-radius: var(--radius-lg); background: var(--color-bg-elevated); border: 1px dashed var(--color-border);">
      <div style="display: inline-flex; width: 64px; height: 64px; border-radius: 50%; background: rgba(99,102,241,0.15); align-items: center; justify-content: center; color: #818CF8; margin-bottom: 20px;">
        <Inbox :size="32" />
      </div>
      <h2 class="mb-3" style="color: var(--color-text);">Aucun article ne correspond à votre recherche</h2>
      <p style="color: var(--color-text-muted); margin-bottom: 24px;">Essayez d'autres mots-clés ou réinitialisez les filtres.</p>
      <button @click="searchQuery = ''; activeCategory = 'Tous'" class="btn btn-primary">
        <RotateCcw :size="16" /> Réinitialiser les filtres
      </button>
    </div>

    <!-- Grille des articles -->
    <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 28px;">
      <article
        v-for="post in filteredPosts"
        :key="post.id"
        class="card flex flex-col justify-between"
        style="border-radius: var(--radius-lg); overflow: hidden; cursor: pointer; background: var(--color-bg-card); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm); transition: transform 0.2s, box-shadow 0.2s;"
        @click="openPost(post)"
        onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--shadow-md)'"
        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)'"
      >
        <!-- Banner / Image -->
        <div style="height: 190px; background: var(--color-bg-2); display: flex; align-items: center; justify-content: center; position: relative; border-bottom: 1px solid var(--color-border);">
          <img v-if="post.image" :src="getImageUrl(post.image)" :alt="post.title" style="width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0;" />
          <div v-else style="color: var(--color-text-muted); opacity: 0.5;">
            <BookOpen :size="56" />
          </div>
          <div class="badge-pill badge-gold" style="position: absolute; top: 12px; left: 12px; font-size: 0.72rem;">
            {{ post.category || 'Architecture & IA' }}
          </div>
          <span style="position: absolute; bottom: 10px; right: 12px; background: var(--color-bg-elevated); color: var(--color-text); font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
            <Clock :size="11" /> {{ getReadingTime(post.content) }}
          </span>
        </div>

        <!-- Contenu -->
        <div style="padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <p style="font-size: 0.8rem; color: var(--color-text-muted); margin-bottom: 8px; font-weight: 700;" class="flex items-center gap-1.5">
              <Calendar :size="13" style="color: var(--color-primary);" /> 
              {{ new Date(post.created_at || Date.now()).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}
            </p>
            <h3 class="mb-2" style="font-size: 1.2rem; line-height: 1.35; color: var(--color-text) !important; font-weight: 800;">{{ post.title }}</h3>
            <p style="color: var(--color-text-muted) !important; font-size: 0.88rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 16px;">
              {{ stripHtml(post.content) }}
            </p>
          </div>

          <div class="flex justify-between items-center" style="border-top: 1px solid var(--color-border); padding-top: 14px;">
            <span style="color: var(--color-primary); font-size: 0.85rem; font-weight: 800;" class="flex items-center gap-1">
              Lire l'article <ArrowRight :size="14" />
            </span>
          </div>
        </div>
      </article>
    </div>

    <!-- Modal Article Plein Écran -->
    <div v-if="selectedPost" @click.self="selectedPost = null" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; display: flex; align-items: flex-start; justify-content: center; padding: 40px 20px; overflow-y: auto; backdrop-filter: blur(8px);">
      <div class="card" style="max-width: 820px; width: 100%; border-radius: 24px; padding: 42px; position: relative; background: var(--color-bg-card); border: 1px solid var(--color-border); box-shadow: var(--shadow-lg);">
        <button @click="selectedPost = null" style="position: absolute; top: 20px; right: 20px; background: var(--color-bg-elevated); border: 1px solid var(--color-border); cursor: pointer; color: var(--color-text-muted); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" title="Fermer" onmouseover="this.style.color='var(--color-text)'" onmouseout="this.style.color='var(--color-text-muted)'">
          <X :size="18" />
        </button>

        <div class="flex items-center gap-3 mb-4" style="flex-wrap: wrap;">
          <span class="badge-pill badge-gold" style="font-size: 0.75rem;">{{ selectedPost.category || 'Article Tech' }}</span>
          <span style="font-size: 0.82rem; color: var(--color-primary); font-weight: 700;" class="flex items-center gap-1">
            <Calendar :size="13" /> {{ new Date(selectedPost.created_at || Date.now()).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}
          </span>
          <span style="font-size: 0.82rem; color: var(--color-text-muted); font-weight: 600;" class="flex items-center gap-1">
            <Clock :size="13" /> {{ getReadingTime(selectedPost.content) }}
          </span>
        </div>

        <h1 class="mb-6" style="font-size: 2.1rem; font-weight: 800; line-height: 1.25; color: var(--color-text);">{{ selectedPost.title }}</h1>
        
        <div style="color: var(--color-text-muted); line-height: 1.85; white-space: pre-wrap; font-size: 1.02rem; margin-bottom: 36px;">{{ selectedPost.content }}</div>

        <!-- Social Share Footer inside article -->
        <div class="flex items-center justify-between pt-6" style="border-top: 1px solid var(--color-border); flex-wrap: wrap; gap: 14px;">
          <span style="font-size: 0.88rem; color: var(--color-text-muted); font-weight: 700;">Partager cet article :</span>
          <div class="flex items-center gap-2">
            <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent('Découvrez cet article : ' + selectedPost.title + ' sur Yass Digital Lab')" target="_blank" class="btn btn-secondary flex items-center gap-1" style="padding: 6px 12px; font-size: 0.8rem; color: #10B981; border-color: rgba(16,185,129,0.3); background: transparent;">
              <MessageSquare :size="13" /> WhatsApp
            </a>
            <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent(selectedPost.title) + '&url=' + encodeURIComponent('https://yassdigitallab.com/blog')" target="_blank" class="btn btn-secondary flex items-center gap-1" style="padding: 6px 12px; font-size: 0.8rem; color: #0EA5E9; border-color: rgba(14,165,233,0.3); background: transparent;">
              <Share2 :size="13" /> Twitter/X
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../api';
import { 
  BookOpen, 
  Inbox, 
  Calendar, 
  ArrowLeft, 
  ArrowRight, 
  X,
  Search,
  Clock,
  RotateCcw,
  MessageSquare,
  Share2
} from 'lucide-vue-next';

const posts = ref([]);
const loading = ref(true);
const selectedPost = ref(null);
const searchQuery = ref('');
const activeCategory = ref('Tous');

const categories = ['Tous', 'Agents IA & RAG', 'SaaS Architecture', 'Laravel & Vue.js', 'Workflows'];

const filteredPosts = computed(() => {
  let list = [...posts.value];

  if (activeCategory.value !== 'Tous') {
    list = list.filter(p => p.category === activeCategory.value || (p.title && p.title.includes(activeCategory.value)));
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(p => 
      p.title?.toLowerCase().includes(q) || 
      p.content?.toLowerCase().includes(q)
    );
  }

  return list;
});

const getReadingTime = (content) => {
  if (!content) return '2 min de lecture';
  const words = content.trim().split(/\s+/).length;
  const minutes = Math.ceil(words / 200);
  return `${minutes} min de lecture`;
};

const getImageUrl = (imagePath) => {
  if (!imagePath) return '';
  if (imagePath.startsWith('http')) return imagePath;
  const baseUrl = import.meta.env.VITE_API_URL ? import.meta.env.VITE_API_URL.replace(/\/api\/?$/, '') : 'http://localhost:8000';
  return `${baseUrl}/storage/${imagePath}`;
};

onMounted(async () => {
  try {
    const res = await api.get('/posts');
    posts.value = Array.isArray(res.data) ? res.data : (res.data?.data || []);
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
});

const openPost = (post) => { selectedPost.value = post; };
const stripHtml = (html) => html?.replace(/<[^>]+>/g, '') || '';
</script>

<style scoped>
.blog-page {
  padding: 30px 0 80px;
}

.cat-filter-pill {
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 0.82rem;
  font-weight: 700;
  border: 1px solid var(--color-border);
  background: var(--color-bg-card);
  color: var(--color-text-muted);
  cursor: pointer;
  transition: all 0.2s ease;
}
.cat-filter-pill:hover,
.cat-filter-pill.active {
  background: var(--color-primary);
  color: #FFFFFF;
  border-color: var(--color-primary);
}
</style>

