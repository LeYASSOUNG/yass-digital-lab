<template>
  <div class="not-found-page">

    <!-- Animated Background Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="nf-container glass fade-in">

      <!-- Animated 404 Number -->
      <div class="nf-number" aria-hidden="true">
        <span class="digit">4</span>
        <span class="digit digit-zero">
          <svg width="96" height="96" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="48" cy="48" r="44" stroke="var(--color-primary)" stroke-width="4" stroke-dasharray="276" stroke-dashoffset="0" class="spin-circle"/>
            <circle cx="48" cy="48" r="28" fill="rgba(99,102,241,0.12)"/>
            <circle cx="48" cy="48" r="12" fill="var(--color-primary)" opacity="0.8"/>
          </svg>
        </span>
        <span class="digit">4</span>
      </div>

      <!-- Icon badge -->
      <div class="nf-icon-badge">
        <SearchXIcon :size="28" style="color: var(--color-primary);" />
      </div>

      <!-- Content -->
      <h1 class="nf-title">Page introuvable</h1>
      <p class="nf-desc">
        La page que vous recherchez n'existe pas, a été déplacée ou n'est plus disponible.
      </p>

      <!-- Quick Links -->
      <div class="nf-links">
        <p class="nf-links-label">Que souhaitez-vous faire ?</p>
        <div class="nf-links-grid">
          <router-link to="/products" class="nf-quick-link">
            <PackageIcon :size="20" />
            <span>Voir les Produits</span>
          </router-link>
          <router-link to="/services" class="nf-quick-link">
            <BriefcaseIcon :size="20" />
            <span>Nos Services</span>
          </router-link>
          <router-link to="/contact" class="nf-quick-link">
            <MailIcon :size="20" />
            <span>Nous Contacter</span>
          </router-link>
        </div>
      </div>

      <!-- CTA Buttons -->
      <div class="nf-actions">
        <router-link to="/" class="btn-primary nf-btn-home">
          <HomeIcon :size="18" />
          Retour à l'accueil
        </router-link>
        <button @click="goBack" class="btn-secondary nf-btn-back">
          <ArrowLeftIcon :size="18" />
          Page précédente
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import {
  SearchX as SearchXIcon,
  Package as PackageIcon,
  Briefcase as BriefcaseIcon,
  GraduationCap as GraduationCapIcon,
  Mail as MailIcon,
  Home as HomeIcon,
  ArrowLeft as ArrowLeftIcon,
} from 'lucide-vue-next';

const router = useRouter();
const goBack = () => router.back();
</script>

<style scoped>
.not-found-page {
  min-height: calc(100vh - var(--nav-height));
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  position: relative;
  overflow: hidden;
}

/* Animated background orbs */
.orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.25;
  animation: float 8s ease-in-out infinite;
  pointer-events: none;
}
.orb-1 {
  width: 400px; height: 400px;
  background: radial-gradient(circle, #6366F1, transparent);
  top: -100px; left: -100px;
  animation-delay: 0s;
}
.orb-2 {
  width: 300px; height: 300px;
  background: radial-gradient(circle, #8B5CF6, transparent);
  bottom: -80px; right: -80px;
  animation-delay: -3s;
}
.orb-3 {
  width: 200px; height: 200px;
  background: radial-gradient(circle, #D4AF37, transparent);
  top: 50%; left: 60%;
  animation-delay: -5s;
}
@keyframes float {
  0%, 100% { transform: translateY(0px) scale(1); }
  50%       { transform: translateY(-30px) scale(1.05); }
}

/* Main card */
.nf-container {
  position: relative;
  z-index: 1;
  max-width: 580px;
  width: 100%;
  padding: 56px 48px;
  border-radius: var(--radius-xl);
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 24px;
}

/* Animated 404 */
.nf-number {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: -8px;
}
.digit {
  font-size: 6rem;
  font-weight: 900;
  font-family: var(--font-heading);
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1;
}
.digit-zero {
  animation: spin-slow 8s linear infinite;
}
@keyframes spin-slow {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}
.spin-circle {
  animation: dash 3s ease-in-out infinite alternate;
}
@keyframes dash {
  from { stroke-dashoffset: 0; }
  to   { stroke-dashoffset: 100; }
}

/* Icon badge */
.nf-icon-badge {
  width: 56px; height: 56px;
  border-radius: var(--radius-md);
  background: rgba(99, 102, 241, 0.1);
  border: 1px solid rgba(99, 102, 241, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  animation: pulse-badge 2.5s ease-in-out infinite;
}
@keyframes pulse-badge {
  0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.3); }
  50%       { box-shadow: 0 0 0 10px rgba(99,102,241,0); }
}

/* Text */
.nf-title {
  font-size: 1.8rem;
  font-weight: 800;
  font-family: var(--font-heading);
  color: var(--color-text);
  margin: 0;
}
.nf-desc {
  color: var(--color-text-muted);
  font-size: 1rem;
  line-height: 1.7;
  margin: 0;
  max-width: 380px;
}

/* Quick Links */
.nf-links {
  width: 100%;
}
.nf-links-label {
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 12px;
}
.nf-links-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}
.nf-quick-link {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
  color: var(--color-text);
  text-decoration: none;
  font-size: 0.88rem;
  font-weight: 600;
  transition: all 200ms ease;
  background: transparent;
  cursor: pointer;
}
.nf-quick-link:hover {
  background: rgba(99, 102, 241, 0.08);
  border-color: rgba(99, 102, 241, 0.4);
  color: var(--color-primary);
  transform: translateY(-2px);
  box-shadow: var(--shadow-sm);
}

/* Action Buttons */
.nf-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
}
.nf-btn-home {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  background: var(--color-primary);
  color: var(--color-text);
  padding: 12px 24px;
  border-radius: var(--radius-pill);
  font-weight: 700;
  font-size: 0.95rem;
  transition: all 200ms ease;
  cursor: pointer;
}
.nf-btn-home:hover {
  background: var(--color-primary-hover);
  transform: translateY(-2px);
  box-shadow: var(--shadow-glow);
}
.nf-btn-back {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: transparent;
  color: var(--color-text-muted);
  border: 1px solid var(--color-border);
  padding: 12px 24px;
  border-radius: var(--radius-pill);
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 200ms ease;
  cursor: pointer;
  font-family: var(--font-body);
}
.nf-btn-back:hover {
  background: rgba(99, 102, 241, 0.06);
  border-color: var(--color-primary);
  color: var(--color-primary);
  transform: translateY(-2px);
}

@media (max-width: 480px) {
  .nf-container {
    padding: 36px 24px;
  }
  .digit {
    font-size: 4rem;
  }
  .nf-links-grid {
    grid-template-columns: 1fr;
  }
  .nf-actions {
    flex-direction: column;
    width: 100%;
  }
  .nf-btn-home, .nf-btn-back {
    justify-content: center;
  }
}
</style>

