<template>
  <div class="courses-page">
    <!-- Hero SaaS -->
    <section class="courses-hero text-center fade-in">
      <div class="hero-glow"></div>
      <div style="position: relative; z-index: 2;">
        <span class="badge-pill badge-indigo mb-3"><BookOpen :size="13" /> PROGRAMMES & FORMATIONS PRATIQUES</span>
        <h1 class="hero-title mb-3">Formations & E-Learning</h1>
        <p class="hero-sub mb-4">
          Maîtrisez le développement d'Agents IA, l'architecture SaaS et les automatisations de workflows.
        </p>
      </div>
    </section>

    <!-- State : Chargement -->
    <div v-if="loading" class="grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
      <div v-for="i in 3" :key="i" class="skeleton skeleton-card"></div>
    </div>

    <!-- State : Erreur -->
    <div v-else-if="error" class="text-center no-courses-card">
      <p style="color: #EF4444; font-weight: 700;">{{ error }}</p>
    </div>

    <!-- Grille des formations -->
    <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
      <div v-for="course in courses" :key="course.id" class="course-item-card flex flex-col justify-between">
        <!-- Thumbnail area -->
        <div class="course-thumb-wrap">
          <img v-if="course.thumbnail" :src="course.thumbnail" :alt="course.title" class="course-img" />
          <div v-else class="thumb-icon-placeholder">
            <GraduationCap :size="56" />
          </div>
          <span class="course-level-tag">
            {{ course.level || 'Tous Niveaux' }}
          </span>
        </div>

        <!-- Info area -->
        <div class="course-info-wrap">
          <div>
            <div class="course-meta-row flex items-center gap-3 mb-2">
              <span class="meta-item flex items-center gap-1">
                <BookOpen :size="13" /> {{ course.chapters_count || '8' }} modules
              </span>
              <span class="meta-item flex items-center gap-1">
                <Clock :size="13" /> {{ course.duration || '6h 30m' }}
              </span>
            </div>
            <h3 class="course-title">{{ course.title }}</h3>
            <p class="course-desc">{{ course.description }}</p>
          </div>

          <div class="course-footer">
            <router-link :to="`/courses/${course.id}`" class="btn-course-action">
              <span>Suivre la formation</span> <ArrowRight :size="15" />
            </router-link>
          </div>
        </div>
      </div>

      <div v-if="courses.length === 0" class="no-courses-card text-center col-span-full">
        <div class="no-course-icon">
          <GraduationCap :size="40" />
        </div>
        <h3 style="color: var(--color-text); font-size: 1.3rem; margin-bottom: 8px;">Nouvelles formations en cours de tournage</h3>
        <p style="color: var(--color-text-muted); font-size: 0.92rem; max-width: 500px; margin: 0 auto 20px;">
          Les modules de formation sur la création d'Agents IA avec LangChain et Laravel SaaS arrivent très prochainement.
        </p>
        <router-link to="/products" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
          <span>Découvrir nos Templates & E-books</span> <ArrowRight :size="16" />
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { BookOpen, Clock, BarChart, GraduationCap, ArrowRight } from 'lucide-vue-next';
import api from '../api';

const courses = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
    const response = await api.get('/courses');
    courses.value = Array.isArray(response.data) ? response.data : (response.data?.data || []);
  } catch (err) {
    // Si pas encore de table de cours, fallback gracieux
    courses.value = [];
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.courses-page {
  padding: 30px 0 80px;
  color: var(--color-text);
}

.courses-hero {
  padding: 60px 24px;
  margin-bottom: 40px;
  position: relative;
  overflow: hidden;
}
.hero-title {
  color: var(--color-text) !important;
  font-weight: 800;
}
.hero-sub {
  color: var(--color-text-muted) !important;
  font-size: 1.05rem;
  max-width: 620px;
  margin: 0 auto;
}

.course-item-card {
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.course-item-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}

.course-thumb-wrap {
  height: 190px;
  background: var(--color-bg-2);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom: 1px solid var(--color-border);
}
.course-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.thumb-icon-placeholder {
  color: #818CF8;
}
.course-level-tag {
  position: absolute;
  top: 12px;
  right: 12px;
  font-size: 0.75rem;
  font-weight: 800;
  padding: 4px 10px;
  border-radius: 999px;
  background: rgba(99, 102, 241, 0.1);
  color: var(--color-primary);
  border: 1px solid rgba(99, 102, 241, 0.2);
}

.course-info-wrap {
  padding: 24px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.course-meta-row {
  font-size: 0.8rem;
  color: #64748B;
  font-weight: 700;
}
.meta-item {
  color: #818CF8;
}
.course-title {
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--color-text) !important;
  margin: 0 0 8px 0;
  line-height: 1.35;
}
.course-desc {
  color: var(--color-text-muted) !important;
  font-size: 0.88rem;
  line-height: 1.6;
  margin-bottom: 16px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.course-footer {
  border-top: 1px solid var(--color-border);
  padding-top: 16px;
}
.btn-course-action {
  width: 100%;
  padding: 10px 16px;
  border-radius: 10px;
  background: linear-gradient(135deg, #6366F1, #4F46E5);
  border: none;
  color: var(--color-text) !important;
  font-weight: 800;
  font-size: 0.88rem;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.2s;
}
.btn-course-action:hover {
  filter: brightness(1.1);
  transform: translateY(-1px);
}

.no-courses-card {
  padding: 60px 24px;
  border-radius: 20px;
  background: var(--color-bg-elevated);
  border: 1px dashed var(--color-border);
}
.no-course-icon {
  width: 68px;
  height: 68px;
  border-radius: 50%;
  background: rgba(99, 102, 241, 0.15);
  color: #818CF8;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px auto;
}
</style>

