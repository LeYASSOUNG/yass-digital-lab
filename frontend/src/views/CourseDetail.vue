<template>
  <div class="course-detail-page container">
    <div v-if="loading" class="loading">Chargement...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="course">
      <header class="course-header">
        <h1>{{ course.title }}</h1>
        <p class="course-meta">
          <span><ClockIcon class="icon" /> {{ course.duration }}</span>
          <span><BarChartIcon class="icon" /> {{ course.level }}</span>
        </p>
        <p class="course-desc">{{ course.description }}</p>
        
        <div v-if="isAuthenticated" class="progress-container">
          <div class="progress-header">
            <span>Votre progression</span>
            <span>{{ progressPercentage }}%</span>
          </div>
          <div class="progress-bar-bg">
            <div class="progress-bar-fill" :style="{ width: progressPercentage + '%' }"></div>
          </div>
        </div>
      </header>

      <div class="course-content">
        <div class="video-player card">
          <div v-if="activeChapter" class="video-placeholder">
            <VideoIcon class="video-icon" />
            <p>Vidéo du chapitre : {{ activeChapter.title }}</p>
            <p class="video-url">{{ activeChapter.video_url || 'Aucune URL fournie' }}</p>
          </div>
          <div v-else class="video-placeholder">
            <p>Sélectionnez une leçon pour commencer</p>
          </div>
        </div>

        <div class="chapters-list card">
          <h3>Programme de la formation</h3>
          <ul class="chapter-items">
            <li 
              v-for="chapter in course.chapters" 
              :key="chapter.id" 
              class="chapter-item"
              :class="{ active: activeChapter && activeChapter.id === chapter.id }"
              @click="setActiveChapter(chapter)"
            >
              <div class="chapter-title">
                <span class="chapter-order">{{ chapter.order_num }}.</span>
                {{ chapter.title }}
              </div>
              <div class="chapter-actions">
                <span class="chapter-duration">{{ chapter.duration }}</span>
                <button 
                  v-if="isAuthenticated"
                  class="btn-toggle-progress" 
                  :class="{ completed: isCompleted(chapter.id) }"
                  @click.stop="toggleProgress(chapter.id)"
                  :disabled="actionLoading === chapter.id"
                  title="Marquer comme terminé/non terminé"
                >
                  <CheckCircleIcon v-if="isCompleted(chapter.id)" class="icon-check" />
                  <CircleIcon v-else class="icon-uncheck" />
                </button>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Reviews Section -->
      <div class="reviews-section mt-12 mb-12">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 16px;">
          <h2 style="font-size: 1.8rem; font-weight: 800; font-family: var(--font-heading); color: var(--color-text);">Avis des apprenants</h2>
          <div class="flex items-center gap-2" style="background: rgba(212,175,55,0.1); border: 1px solid rgba(212,175,55,0.25); padding: 6px 14px; border-radius: 999px;">
            <StarIcon :size="16" style="color: var(--color-accent); fill: var(--color-accent);" />
            <span style="font-weight: 800; color: var(--color-accent); font-size: 0.95rem;">
              {{ avgRating }} / 5
            </span>
            <span style="color: var(--color-text-muted); font-size: 0.8rem; margin-left: 4px;">({{ course.reviews?.length || 0 }} avis)</span>
          </div>
        </div>

        <div class="grid" style="grid-template-columns: 2fr 1fr; gap: var(--space-xl);" id="reviews-grid">
          
          <!-- Reviews List -->
          <div>
            <div v-if="!course.reviews || course.reviews.length === 0" class="text-center" style="padding: 40px; border-radius: 20px; background: var(--color-bg-elevated); border: 1px dashed var(--color-border);">
              <MessageSquareIcon :size="40" style="color: var(--color-text-muted); margin: 0 auto 12px;" />
              <p style="color: var(--color-text-light);">Aucun avis pour cette formation pour le moment. Soyez le premier !</p>
            </div>
            <div v-else class="flex flex-col gap-4">
              <div v-for="review in course.reviews" :key="review.id" style="padding: 24px; border-radius: 16px; background: var(--color-bg-elevated); border: 1px solid var(--color-border); position: relative;">
                <div class="flex justify-between items-start mb-3">
                  <div>
                    <h4 style="font-weight: 700; margin-bottom: 2px; color: var(--color-text);">{{ review.name }}</h4>
                    <span v-if="review.verified_buyer" class="badge-pill badge-indigo" style="font-size: 0.65rem; padding: 2px 8px;">
                      ✓ Apprenant vérifié
                    </span>
                  </div>
                  <div class="flex" style="gap: 2px;">
                    <StarIcon v-for="i in 5" :key="i" :size="14" :style="{ color: i <= review.rating ? 'var(--color-accent)' : 'var(--color-text-muted)', fill: i <= review.rating ? 'var(--color-accent)' : 'none' }" />
                  </div>
                </div>
                <p style="color: var(--color-text-light); font-size: 0.9rem; line-height: 1.5; margin: 0;">"{{ review.comment }}"</p>
                
                <div v-if="review.admin_reply" style="margin-top: 16px; padding: 12px 16px; background: rgba(99,102,241,0.08); border-left: 3px solid var(--color-primary); border-radius: 8px;">
                  <span style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Réponse du formateur</span>
                  <p style="margin: 0; font-size: 0.85rem; color: var(--color-text-light);">{{ review.admin_reply }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Add Review Form -->
          <div>
            <div style="padding: 28px; border-radius: 20px; position: sticky; top: 100px; background: var(--color-bg-elevated); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
              <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 20px;">Laissez votre avis</h3>
              <form @submit.prevent="submitReview" class="flex flex-col gap-4">
                <div>
                  <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--color-text-light);">Note sur 5</label>
                  <div class="flex gap-2">
                    <button type="button" v-for="i in 5" :key="i" @click="newReview.rating = i" style="background: none; border: none; cursor: pointer; padding: 4px;">
                      <StarIcon :size="24" :style="{ color: i <= newReview.rating ? 'var(--color-accent)' : 'var(--color-border)', fill: i <= newReview.rating ? 'var(--color-accent)' : 'none', transition: 'all 0.2s' }" />
                    </button>
                  </div>
                </div>
                <div>
                  <label class="form-label">Votre nom *</label>
                  <input v-model="newReview.name" type="text" required class="form-input" placeholder="ex: Jean Dupont" />
                </div>
                <div>
                  <label class="form-label">Email (pour badge vérifié)</label>
                  <input v-model="newReview.email" type="email" class="form-input" placeholder="L'email de votre commande" />
                </div>
                <div>
                  <label class="form-label">Commentaire *</label>
                  <textarea v-model="newReview.comment" required class="form-input" rows="4" placeholder="Qu'avez-vous pensé de cette formation ?"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" :disabled="submittingReview" style="padding: 12px; font-weight: 700; display: flex; justify-content: center; align-items: center; gap: 8px;">
                  <span v-if="submittingReview" class="spinner"></span>
                  {{ submittingReview ? 'Envoi...' : 'Publier mon avis' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { ClockIcon, BarChartIcon, CheckCircleIcon, CircleIcon, VideoIcon, StarIcon, MessageSquareIcon } from 'lucide-vue-next';
import { useToastStore } from '../stores/toast';
import api from '../api';

const route = useRoute();
const toastStore = useToastStore();

// Basic auth check using localStorage token
const isAuthenticated = computed(() => {
  return !!localStorage.getItem('token');
});

const course = ref(null);
const completedChapterIds = ref([]);
const progressPercentage = ref(0);
const loading = ref(true);
const error = ref(null);
const activeChapter = ref(null);
const actionLoading = ref(null);
const submittingReview = ref(false);

const newReview = ref({
  name: '',
  email: '',
  rating: 5,
  comment: ''
});

const avgRating = computed(() => {
  if (!course.value?.reviews || course.value.reviews.length === 0) return 0;
  const total = course.value.reviews.reduce((acc, curr) => acc + curr.rating, 0);
  return (total / course.value.reviews.length).toFixed(1);
});

const fetchCourseDetail = async () => {
  try {
    const response = await api.get(`/courses/${route.params.id}`);
    course.value = response.data.course;
    completedChapterIds.value = response.data.completed_chapter_ids || [];
    progressPercentage.value = response.data.progress_percentage || 0;
    
    if (course.value.chapters && course.value.chapters.length > 0) {
      activeChapter.value = course.value.chapters[0];
    }
  } catch (err) {
    error.value = 'Erreur lors du chargement des détails.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const isCompleted = (chapterId) => {
  return completedChapterIds.value.includes(chapterId);
};

const setActiveChapter = (chapter) => {
  activeChapter.value = chapter;
};

const toggleProgress = async (chapterId) => {
  if (!isAuthenticated.value) return;
  
  actionLoading.value = chapterId;
  try {
    const response = await api.post(`/chapters/${chapterId}/progress`);
    const isNowCompleted = response.data.is_completed;
    
    if (isNowCompleted) {
      completedChapterIds.value.push(chapterId);
    } else {
      completedChapterIds.value = completedChapterIds.value.filter(id => id !== chapterId);
    }
    
    // Recalculate progress locally
    const total = course.value.chapters.length;
    progressPercentage.value = Math.round((completedChapterIds.value.length / total) * 100);
    
  } catch (err) {
    console.error('Erreur lors de la mise à jour de la progression', err);
    alert('Veuillez vous reconnecter pour mettre à jour votre progression.');
  } finally {
    actionLoading.value = null;
  }
};

const submitReview = async () => {
  if (!newReview.value.name || !newReview.value.comment) return;
  submittingReview.value = true;
  try {
    const res = await api.post(`/courses/${route.params.id}/reviews`, newReview.value);
    toastStore.showToast(res.data.message || 'Avis publié !', 'success');
    
    // Add dynamically to UI
    if (res.data.review) {
      if (!course.value.reviews) course.value.reviews = [];
      course.value.reviews.unshift(res.data.review);
    }
    
    // Reset form
    newReview.value = { name: '', email: '', rating: 5, comment: '' };
  } catch (err) {
    toastStore.showToast('Erreur lors de la publication de l\'avis', 'error');
    console.error(err);
  } finally {
    submittingReview.value = false;
  }
};

onMounted(() => {
  fetchCourseDetail();
});
</script>

<style scoped>
.course-detail-page {
  padding: 30px 0 80px;
  color: var(--color-text);
}
.course-header {
  margin-bottom: var(--space-xl);
}
.course-header h1 {
  color: var(--color-text) !important;
  font-weight: 800;
  margin-bottom: var(--space-sm);
}
.course-meta {
  display: flex;
  gap: var(--space-lg);
  color: var(--color-text-muted) !important;
  margin-bottom: var(--space-md);
}
.icon {
  width: 18px;
  height: 18px;
  vertical-align: sub;
}
.progress-container {
  margin-top: var(--space-lg);
  background: var(--color-muted);
  padding: var(--space-md);
  border-radius: 8px;
  max-width: 600px;
}
.progress-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: var(--space-sm);
  font-weight: 600;
  color: var(--color-primary);
}
.progress-bar-bg {
  height: 10px;
  background: #E2E8F0;
  border-radius: 5px;
  overflow: hidden;
}
.progress-bar-fill {
  height: 100%;
  background: var(--color-accent);
  transition: width 300ms ease;
}

.course-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: var(--space-xl);
}

.video-player {
  aspect-ratio: 16 / 9;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-bg-2);
  border: 1px solid var(--color-border);
  color: var(--color-text);
  border-radius: 12px;
}
.video-placeholder {
  text-align: center;
}
.video-icon {
  width: 48px;
  height: 48px;
  margin-bottom: var(--space-sm);
  opacity: 0.7;
}
.video-url {
  font-size: 0.875rem;
  color: #9CA3AF;
  margin-top: var(--space-xs);
  word-break: break-all;
  padding: 0 var(--space-md);
}

.chapters-list h3 {
  margin-bottom: var(--space-md);
  color: var(--color-primary);
}
.chapter-items {
  list-style: none;
  padding: 0;
  margin: 0;
}
.chapter-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--space-sm) 0;
  border-bottom: 1px solid var(--color-border);
  cursor: pointer;
  transition: background 200ms ease;
}
.chapter-item:last-child {
  border-bottom: none;
}
.chapter-item:hover, .chapter-item.active {
  background: var(--color-muted);
  border-radius: 4px;
  padding-left: var(--space-sm);
  padding-right: var(--space-sm);
  margin-left: calc(-1 * var(--space-sm));
  margin-right: calc(-1 * var(--space-sm));
}
.chapter-title {
  font-weight: 500;
  color: var(--color-foreground);
}
.chapter-order {
  color: var(--color-primary);
  margin-right: var(--space-xs);
}
.chapter-actions {
  display: flex;
  align-items: center;
  gap: var(--space-md);
}
.chapter-duration {
  font-size: 0.875rem;
  color: #64748B;
}
.btn-toggle-progress {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  color: var(--color-text-muted);
  transition: all 200ms ease;
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-toggle-progress.completed {
  color: var(--color-accent);
}
.btn-toggle-progress:hover:not(:disabled) {
  color: var(--color-primary);
  transform: scale(1.1);
}
.btn-toggle-progress:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

@media (max-width: 1024px) {
  .course-content {
    grid-template-columns: 1fr;
  }
  #reviews-grid {
    grid-template-columns: 1fr !important;
  }
}

.form-label {
  display: block;
  margin-bottom: 6px;
  color: var(--color-text-light);
  font-size: 0.85rem;
  font-weight: 600;
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
.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: var(--color-text);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>

