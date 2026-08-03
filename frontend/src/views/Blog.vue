<template>
  <div class="blog-page">
    <!-- Header -->
    <section class="glass text-center" style="padding: 70px 20px; border-radius: 20px; margin-top: 20px; margin-bottom: 40px;">
      <div style="font-size: 3rem; margin-bottom: 12px;">📝</div>
      <h1 class="mb-2" style="font-size: 2.2rem;">Le Blog Yass Digital Lab</h1>
      <p style="color: var(--color-text-light); font-size: 1.05rem;">Astuces IA, guides développement et ressources numériques.</p>
    </section>

    <!-- Chargement -->
    <div v-if="loading" class="text-center" style="padding: 80px;">
      <div class="spinner"></div>
      <p style="color: var(--color-accent); margin-top: 16px;">Chargement des articles...</p>
    </div>

    <!-- Aucun article -->
    <div v-else-if="posts.length === 0" class="glass text-center" style="padding: 80px; border-radius: 20px;">
      <div style="font-size: 4rem; margin-bottom: 20px;">📭</div>
      <h2 class="mb-4">Aucun article pour le moment</h2>
      <p style="color: var(--color-text-light); margin-bottom: 30px;">Les premiers articles arrivent bientôt ! Inscrivez-vous à la newsletter pour être notifié.</p>
      <router-link to="/" class="btn btn-primary">← Retour à l'accueil</router-link>
    </div>

    <!-- Grille des articles -->
    <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 28px;">
      <article
        v-for="post in posts"
        :key="post.id"
        class="card glass"
        style="border-radius: 16px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;"
        @click="openPost(post)"
      >
        <!-- Image / bannière -->
        <div style="height: 200px; background: linear-gradient(135deg, var(--color-primary), #1e3a5f); display: flex; align-items: center; justify-content: center; position: relative;">
          <img v-if="post.image" :src="getImageUrl(post.image)" :alt="post.title" style="width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0;" />
          <span v-else style="font-size: 4rem;">📄</span>
          <div style="position: absolute; top: 12px; left: 12px; background: var(--color-accent); color: #000; font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 999px;">Article</div>
        </div>
        <!-- Contenu -->
        <div style="padding: 24px;">
          <p style="font-size: 0.8rem; color: var(--color-text-light); margin-bottom: 8px;">
            📅 {{ new Date(post.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}
          </p>
          <h3 class="mb-2" style="font-size: 1.15rem; line-height: 1.4;">{{ post.title }}</h3>
          <p style="color: var(--color-text-light); font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">{{ stripHtml(post.content) }}</p>
          <div class="flex justify-end" style="margin-top: 16px;">
            <span style="color: var(--color-accent); font-size: 0.9rem; font-weight: 600;">Lire l'article →</span>
          </div>
        </div>
      </article>
    </div>

    <!-- Modal Article -->
    <div v-if="selectedPost" @click.self="selectedPost = null" style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; display: flex; align-items: flex-start; justify-content: center; padding: 40px 20px; overflow-y: auto;">
      <div class="glass" style="max-width: 750px; width: 100%; border-radius: 20px; padding: 40px; position: relative;">
        <button @click="selectedPost = null" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--color-text);">✕</button>
        <p style="font-size: 0.85rem; color: var(--color-text-light); margin-bottom: 12px;">
          📅 {{ new Date(selectedPost.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}
        </p>
        <h1 class="mb-6" style="font-size: 1.8rem;">{{ selectedPost.title }}</h1>
        <div style="color: var(--color-text-light); line-height: 1.8; white-space: pre-wrap;">{{ selectedPost.content }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';

const posts = ref([]);
const loading = ref(true);
const selectedPost = ref(null);

const getImageUrl = (imagePath) => {
  if (!imagePath) return '';
  if (imagePath.startsWith('http')) return imagePath;
  const baseUrl = import.meta.env.VITE_API_URL ? import.meta.env.VITE_API_URL.replace(/\/api\/?$/, '') : 'http://localhost:8000';
  return `${baseUrl}/storage/${imagePath}`;
};

onMounted(async () => {
  try {
    const res = await api.get('/posts');
    posts.value = res.data;
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
.card:hover { transform: translateY(-5px); box-shadow: 0 20px 50px rgba(212,175,55,0.12); }
.spinner { width: 48px; height: 48px; border: 4px solid rgba(212,175,55,0.2); border-top-color: var(--color-accent); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
