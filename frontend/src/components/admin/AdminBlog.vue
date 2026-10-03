<template>
  <div class="fade-in">
    <div class="glass" style="padding: 28px; border-radius: 20px;">
      <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 14px;">
        <div>
          <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">📝 Articles de Blog</h3>
          <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Rédigez et modifiez des articles SEO pour le blog.</p>
        </div>
        <div class="flex items-center gap-3">
          <div style="position: relative; width: 220px;">
            <input v-model="searchBlogQuery" type="text" placeholder="Rechercher article..." style="width: 100%; padding: 6px 12px 6px 15px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
          </div>
          <button @click="showPostForm = !showPostForm" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.88rem;">
            {{ showPostForm ? '✕ Annuler' : '+ Nouvel Article' }}
          </button>
        </div>
      </div>

      <div v-if="showPostForm" class="mb-8" style="background: rgba(212,175,55,0.05); border: 1px solid rgba(212,175,55,0.25); padding: 24px; border-radius: 16px;">
        <h4 class="mb-4" style="font-size: 1.1rem; font-weight: 700;">Publier un Article</h4>
        <form @submit.prevent="createPost" class="flex flex-col gap-4">
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Titre de l'article *</label>
            <input v-model="newPost.title" type="text" required class="form-input" placeholder="ex: 10 astuces pour..." />
          </div>
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Contenu Markdown / HTML *</label>
            <textarea v-model="newPost.content" required class="form-input" rows="6" placeholder="Rédigez votre article ici..."></textarea>
          </div>
          <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="padding: 11px; font-weight: 800;">
            📝 Publier l'article
          </button>
        </form>
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Titre</th>
              <th style="padding: 12px; text-align: left;">Date</th>
              <th style="padding: 12px; text-align: left;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="post in filteredPosts" :key="post.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ post.id }}</td>
              <td style="padding: 12px; font-weight: 600;">{{ post.title }}</td>
              <td style="padding: 12px; color: var(--color-text-light); font-size: 0.82rem;">{{ post.created_at ? new Date(post.created_at).toLocaleDateString('fr-FR') : 'Récents' }}</td>
              <td style="padding: 12px; display: flex; gap: 8px;">
                <button @click="startEditPost(post)" style="background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid #3b82f6; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                  ✏️ Modifier
                </button>
                <button @click="deletePost(post.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                  🗑️ Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Édition Article -->
    <div v-if="editingPost" style="position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px;">
      <div style="padding: 32px; border-radius: 20px; width: 100%; max-width: 520px; position: relative; background: var(--color-bg-card); border: 1px solid var(--color-border);">
        <button @click="editingPost = null" style="position: absolute; right: 16px; top: 16px; background: none; border: none; cursor: pointer; color: var(--color-text-light); font-size: 1.2rem;">✕</button>
        <h3 class="mb-4 flex items-center gap-2" style="font-size: 1.2rem; font-weight: 700;">Modifier l'Article #{{ editingPost.id }}</h3>
        <form @submit.prevent="updatePost" class="flex flex-col gap-4">
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Titre</label>
            <input v-model="editingPost.title" type="text" required class="form-input" />
          </div>
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Contenu</label>
            <textarea v-model="editingPost.content" required class="form-input" rows="5"></textarea>
          </div>
          <div class="flex gap-2">
            <button type="submit" class="btn btn-primary flex-1 flex items-center justify-center gap-2" style="padding: 10px;">Enregistrer</button>
            <button type="button" @click="editingPost = null" class="btn btn-secondary" style="padding: 10px;">Annuler</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../api';
import { useToastStore } from '../../stores/toast';

const toastStore = useToastStore();
const posts = ref([]);
const searchBlogQuery = ref('');
const showPostForm = ref(false);
const newPost = ref({ title: '', content: '', is_published: true });
const editingPost = ref(null);
const emit = defineEmits(['count-updated']);

const filteredPosts = computed(() => {
  if (!searchBlogQuery.value.trim()) return posts.value;
  const q = searchBlogQuery.value.toLowerCase();
  return posts.value.filter(p => p.title?.toLowerCase().includes(q));
});

const loadPosts = async () => {
  try {
    const res = await api.get('/posts');
    posts.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
    emit('count-updated', posts.value.length);
  } catch(e) { 
    posts.value = []; 
  }
};

const createPost = async () => {
  try {
    await api.post('/posts', newPost.value);
    showPostForm.value = false;
    newPost.value = { title: '', content: '', is_published: true };
    toastStore.showToast('Article de blog publié !', 'success');
    await loadPosts();
  } catch(e) { 
    toastStore.showToast('Erreur publication article.', 'error'); 
  }
};

const startEditPost = (post) => { editingPost.value = { ...post }; };

const updatePost = async () => {
  if (!editingPost.value) return;
  try {
    await api.put(`/posts/${editingPost.value.id}`, editingPost.value);
    editingPost.value = null;
    toastStore.showToast('Article mis à jour avec succès !', 'success');
    await loadPosts();
  } catch(e) { 
    toastStore.showToast('Erreur lors de la modification de l\'article.', 'error'); 
  }
};

const deletePost = async (id) => {
  if (confirm('Supprimer cet article ?')) {
    try {
      await api.delete(`/posts/${id}`);
      toastStore.showToast('Article supprimé.', 'info');
      await loadPosts();
    } catch(e) { 
      toastStore.showToast('Erreur lors de la suppression.', 'error'); 
    }
  }
};

onMounted(() => {
  loadPosts();
});
</script>

<style scoped>
.form-label {
  display: block;
  margin-bottom: 6px;
  color: var(--color-text);
}
.form-input {
  width: 100%;
  padding: 10px 14px;
  border-radius: 10px;
  border: 1px solid var(--color-border);
  background: var(--color-bg);
  color: var(--color-text);
  font-size: 0.9rem;
}
.form-input:focus {
  outline: none;
  border-color: var(--color-accent);
}
</style>
