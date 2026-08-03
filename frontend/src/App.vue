<template>
  <div class="app-layout" style="display: flex; flex-direction: column; min-height: 100vh;">
    <!-- Background Video -->
    <video class="bg-video" autoplay loop muted playsinline>
      <source src="/background.mp4" type="video/mp4" />
    </video>
    
    <!-- Global Toast Notifications -->
    <ToastContainer />
    
    <!-- Top Bar Notice -->
    <div style="background: linear-gradient(90deg, var(--color-primary), #1e293b, var(--color-primary)); color: #FFF; padding: 6px 16px; font-size: 0.8rem; text-align: center; border-bottom: 1px solid var(--color-border); font-weight: 500;">
      {{ t('promoNotice') }}
    </div>

    <!-- Header Navigation Redesigned -->
    <header translate="no" class="notranslate glass header-nav" style="padding: 0.75rem 0; position: sticky; top: 0; z-index: 100; backdrop-filter: blur(28px); border-bottom: 1px solid rgba(212, 175, 55, 0.2);">
      <div class="container flex justify-between items-center gap-3">
        
        <!-- Official Logo (Compact Header Mode) -->
        <Logo :size="36" :showTagline="false" />

        <!-- Central Navigation Links -->
        <nav class="flex gap-1 items-center" id="main-nav">
          <router-link to="/" class="nav-link">{{ t('home') }}</router-link>
          <router-link to="/products" class="nav-link">{{ t('products') }}</router-link>
          <router-link to="/services" class="nav-link">{{ t('services') }}</router-link>
          <router-link to="/blog" class="nav-link">{{ t('blog') }}</router-link>
          <router-link to="/about" class="nav-link">{{ t('about') }}</router-link>
          <router-link to="/contact" class="nav-link">{{ t('contact') }}</router-link>
        </nav>

        <!-- Right Side Action Controls -->
        <div class="flex items-center gap-2" id="header-actions">
          
          <!-- Live Search Bar -->
          <div style="position: relative; width: 170px;">
            <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); display: flex; align-items: center; pointer-events: none;">
              <Search :size="15" />
            </div>
            <input 
              v-model="searchQuery" 
              @focus="searchFocused = true"
              @blur="setTimeout(() => searchFocused = false, 200)"
              type="text" 
              placeholder="Rechercher..." 
              style="width: 100%; padding: 6px 14px 6px 32px; border-radius: 999px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;"
            />

            <!-- Dropdown Results -->
            <div v-if="searchFocused && searchResults.length > 0" class="glass" style="position: absolute; top: 38px; right: 0; width: 250px; border-radius: 14px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 1000; background: var(--color-bg-card);">
              <router-link 
                v-for="res in searchResults" 
                :key="res.id" 
                :to="res.url"
                class="flex items-center gap-2"
                style="padding: 10px 14px; text-decoration: none; color: var(--color-text); border-bottom: 1px solid var(--color-border); font-size: 0.85rem;"
              >
                <component :is="res.iconComponent" :size="16" style="color: var(--color-accent);" />
                <span style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ res.title }}</span>
              </router-link>
            </div>
          </div>

          <!-- Panier Pill -->
          <router-link to="/checkout" class="cart-pill" style="position: relative; display: flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(212,175,55,0.12); border-radius: 999px; border: 1px solid rgba(212,175,55,0.3); font-weight: 700; color: var(--color-text); text-decoration: none; font-size: 0.82rem; white-space: nowrap;">
            <ShoppingCart :size="16" style="color: var(--color-accent);" /> 
            <span>{{ t('cart') }}</span>
            <span v-if="cart.totalItems > 0" style="background: var(--color-accent); color: #050811; border-radius: 50%; width: 18px; height: 18px; font-size: 0.72rem; font-weight: 800; display: flex; align-items: center; justify-content: center; margin-left: 2px;">
              {{ cart.totalItems }}
            </span>
          </router-link>

          <!-- Auth Button -->
          <router-link v-if="!auth.token" to="/login" class="btn btn-secondary" style="padding: 6px 14px; border-radius: 999px; font-size: 0.82rem; border-color: var(--color-accent); color: var(--color-accent); font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
            <LogIn :size="15" />
            <span>{{ t('login') }}</span>
          </router-link>
          <router-link v-else to="/dashboard" class="btn btn-primary" style="padding: 6px 14px; border-radius: 999px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
            <User :size="15" />
            <span>{{ t('mySpace') }}</span>
          </router-link>

          <!-- Utility Group Pill (Notifs + FR/EN + Theme) -->
          <div class="flex items-center gap-1" style="background: rgba(255,255,255,0.05); padding: 3px; border-radius: 999px; border: 1px solid var(--color-border); white-space: nowrap;">
            <NotificationCenter />
            
            <button @click="handleToggleLang" title="Langue" style="background: none; border: none; padding: 4px 8px; font-size: 0.78rem; font-weight: 800; cursor: pointer; color: var(--color-text); display: inline-flex; align-items: center; gap: 4px;">
              <Globe :size="14" style="color: var(--color-accent);" />
              {{ currentLang === 'fr' ? 'FR' : 'EN' }}
            </button>

            <button @click="toggleTheme" title="Changer le thème" style="background: none; border: none; width: 28px; height: 28px; cursor: pointer; color: var(--color-accent); display: flex; align-items: center; justify-content: center;">
              <Sun v-if="isDark" :size="16" />
              <Moon v-else :size="16" />
            </button>
          </div>

        </div>

        <!-- Mobile Burger Trigger -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="burger-btn" style="display: none; background: none; border: 1px solid var(--color-border); border-radius: 10px; padding: 8px 12px; cursor: pointer; color: var(--color-text);">
          <X v-if="mobileMenuOpen" :size="20" />
          <Menu v-else :size="20" />
        </button>
      </div>

      <!-- Mobile Dropdown Menu -->
      <div v-if="mobileMenuOpen" class="mobile-menu" style="padding: 20px; display: flex; flex-direction: column; gap: 10px; border-top: 1px solid var(--color-border); margin-top: 12px; background: var(--color-bg-card);">
        <router-link to="/" class="nav-link" @click="mobileMenuOpen = false">{{ t('home') }}</router-link>
        <router-link to="/products" class="nav-link" @click="mobileMenuOpen = false">{{ t('products') }}</router-link>
        <router-link to="/services" class="nav-link" @click="mobileMenuOpen = false">{{ t('services') }}</router-link>
        <router-link to="/blog" class="nav-link" @click="mobileMenuOpen = false">{{ t('blog') }}</router-link>
        <router-link to="/about" class="nav-link" @click="mobileMenuOpen = false">{{ t('about') }}</router-link>
        <router-link to="/contact" class="nav-link" @click="mobileMenuOpen = false">{{ t('contact') }}</router-link>
        <router-link to="/checkout" class="nav-link flex items-center gap-2" @click="mobileMenuOpen = false">
          <ShoppingCart :size="16" /> {{ t('cart') }} ({{ cart.totalItems }})
        </router-link>
        <router-link v-if="!auth.token" to="/login" class="btn btn-primary flex justify-center items-center gap-2" @click="mobileMenuOpen = false">
          <LogIn :size="16" /> {{ t('login') }}
        </router-link>
        <router-link v-else to="/dashboard" class="btn btn-primary flex justify-center items-center gap-2" @click="mobileMenuOpen = false">
          <User :size="16" /> {{ t('mySpace') }}
        </router-link>
      </div>
    </header>

    <!-- Main View Outlet -->
    <main class="container" style="flex: 1; padding-bottom: 60px;">
      <router-view></router-view>
    </main>

    <!-- Footer -->
    <footer style="background: rgba(5, 8, 17, 0.88); backdrop-filter: blur(20px); border-top: 1px solid var(--color-accent); padding: 70px 20px 30px; margin-top: 40px; color: #FFFFFF; box-shadow: 0 -10px 40px rgba(0,0,0,0.5);">
      <div class="container">
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 40px; margin-bottom: 50px;">
          
          <!-- Brand Column -->
          <div>
            <div class="mb-4">
              <Logo :size="48" :showTagline="true" :inverted="true" />
            </div>
            <p style="color: rgba(255,255,255,0.92); font-size: 0.98rem; line-height: 1.7; margin-bottom: 20px;">
              Créateur d'outils numériques intelligents, templates SaaS et solutions d'intelligence artificielle sur mesure.
            </p>
            <div class="flex gap-3">
              <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" title="Mon Portfolio Vercel" style="width: 38px; height: 38px; border-radius: 8px; border: 1px solid var(--color-accent); background: rgba(212,175,55,0.2); color: var(--color-accent); display: flex; align-items: center; justify-content: center; text-decoration: none;">
                <Globe :size="19" />
              </a>
              <a href="https://github.com/LeYASSOUNG" target="_blank" title="GitHub" style="width: 38px; height: 38px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.4); color: #FFF; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                <Github :size="19" />
              </a>
              <a href="#" title="LinkedIn" style="width: 38px; height: 38px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.4); color: #FFF; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                <Linkedin :size="19" />
              </a>
            </div>
          </div>

          <!-- Navigation Links -->
          <div>
            <h4 style="margin-bottom: 18px; font-size: 1.08rem; color: #F0CC55; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">Navigation Rapide</h4>
            <div class="flex flex-col gap-2">
              <router-link to="/products" class="footer-link flex items-center gap-2" style="color: #FFFFFF; font-size: 0.96rem; font-weight: 500;"><Package :size="16" /> {{ t('products') }}</router-link>
              <router-link to="/services" class="footer-link flex items-center gap-2" style="color: #FFFFFF; font-size: 0.96rem; font-weight: 500;"><Briefcase :size="16" /> {{ t('services') }}</router-link>
              <router-link to="/blog" class="footer-link flex items-center gap-2" style="color: #FFFFFF; font-size: 0.96rem; font-weight: 500;"><FileText :size="16" /> {{ t('blog') }}</router-link>
              <router-link to="/about" class="footer-link flex items-center gap-2" style="color: #FFFFFF; font-size: 0.96rem; font-weight: 500;"><User :size="16" /> {{ t('about') }}</router-link>
              <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" class="footer-link flex items-center gap-2" style="color: #F0CC55; font-weight: 700; font-size: 0.96rem;">
                <Globe :size="16" /> Mon Portfolio <ExternalLink :size="14" />
              </a>
              <router-link to="/contact" class="footer-link flex items-center gap-2" style="color: #FFFFFF; font-size: 0.96rem; font-weight: 500;"><Mail :size="16" /> {{ t('contact') }}</router-link>
            </div>
          </div>

          <!-- Newsletter Column -->
          <div>
            <h4 style="margin-bottom: 14px; font-size: 1.08rem; color: #F0CC55; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">{{ t('newsletterTitle') }}</h4>
            <p style="color: rgba(255,255,255,0.92); font-size: 0.95rem; margin-bottom: 16px; line-height: 1.6;">
              {{ t('newsletterSub') }}
            </p>
            <form @submit.prevent="subscribeNewsletter" class="flex gap-2">
              <input v-model="newsletterEmail" type="email" placeholder="votre@email.com" required style="flex: 1; padding: 11px 14px; border-radius: 10px; border: 1px solid var(--color-accent); background: #FFFFFF; color: #050811; font-weight: 600; font-size: 0.95rem;" />
              <button type="submit" class="btn btn-primary" :disabled="subscribing" style="padding: 11px 16px; display: inline-flex; align-items: center; justify-content: center;">
                <ArrowRight v-if="!subscribing" :size="18" />
                <span v-else>...</span>
              </button>
            </form>
          </div>
        </div>

        <div style="border-top: 1px solid rgba(212,175,55,0.25); padding-top: 24px; text-align: center; color: rgba(255,255,255,0.9); font-size: 0.92rem; flex-wrap: wrap; gap: 10px;" class="flex justify-between items-center">
          <span>© 2026 Yass Digital Lab — {{ t('footerRights') }}</span>
          <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" style="color: #F0CC55; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <Globe :size="15" />
            <span>Portfolio Créateur : Diarrassouba Yassoungo Youssouf</span>
            <ExternalLink :size="13" />
          </a>
        </div>
      </div>
    </footer>
    <ChatWidget />
  </div>
</template>

<script setup>
import { useCartStore } from './stores/cart';
import { useAuthStore } from './stores/auth';
import { useToastStore } from './stores/toast';
import { currentLang, toggleLang, t } from './i18n';
import { ref, computed, onMounted } from 'vue';
import Logo from './components/Logo.vue';
import ToastContainer from './components/ToastContainer.vue';
import ChatWidget from './components/ChatWidget.vue';
import NotificationCenter from './components/NotificationCenter.vue';
import api from './api';
import { 
  Search, 
  ShoppingCart, 
  User, 
  Sun, 
  Moon, 
  Menu, 
  X, 
  Globe, 
  LogIn, 
  Send, 
  ArrowRight, 
  Package, 
  Briefcase, 
  FileText, 
  Mail, 
  Github, 
  Linkedin, 
  ExternalLink 
} from 'lucide-vue-next';

const cart = useCartStore();
const auth = useAuthStore();
const toastStore = useToastStore();
const isDark = ref(false);
const newsletterEmail = ref('');
const subscribing = ref(false);
const mobileMenuOpen = ref(false);

const searchQuery = ref('');
const searchFocused = ref(false);

const allSearchItems = [
  { id: 1, title: 'Mega Pack Prompts ChatGPT & Claude', iconComponent: Package, url: '/products/1' },
  { id: 2, title: 'Template SaaS Starter Vue 3 + Laravel 12', iconComponent: Briefcase, url: '/products/2' },
  { id: 3, title: '10 Prompts IA indispensables', iconComponent: FileText, url: '/blog' },
  { id: 4, title: 'Création de site web sur mesure', iconComponent: Globe, url: '/services' }
];

const searchResults = computed(() => {
  if (!searchQuery.value.trim()) return [];
  const q = searchQuery.value.toLowerCase();
  return allSearchItems.filter(item => item.title.toLowerCase().includes(q));
});

const handleToggleLang = () => {
  toggleLang();
  toastStore.showToast(currentLang.value === 'fr' ? 'Langue : Français 🇫🇷' : 'Language: English 🇬🇧', 'info');
};

const toggleTheme = () => {
  isDark.value = !isDark.value;
  document.documentElement.setAttribute('data-theme', isDark.value ? 'dark' : '');
  if (!isDark.value) document.documentElement.removeAttribute('data-theme');
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

const subscribeNewsletter = async () => {
  if (!newsletterEmail.value) return;
  subscribing.value = true;
  try {
    const res = await api.post('/newsletter/subscribe', { email: newsletterEmail.value });
    toastStore.showToast(res.data.message || 'Inscription réussie !', 'success');
    newsletterEmail.value = '';
  } catch (error) {
    toastStore.showToast(error.response?.status === 422 ? 'Cet e-mail est déjà inscrit.' : 'Erreur, veuillez réessayer.', 'error');
  }
  subscribing.value = false;
};

onMounted(() => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true;
    document.documentElement.setAttribute('data-theme', 'dark');
  }

  // Détection automatique du lien de parrainage
  const urlParams = new URLSearchParams(window.location.search);
  const refCode = urlParams.get('ref');
  if (refCode) {
    localStorage.setItem('referral_code', refCode);
    toastStore.showToast(`🎁 Bienvenue ! Parrainé par ${refCode} — 20% de réduction appliqués avec le code YASS20.`, 'success');
  }
});
</script>

<style scoped>
.app-layout {
  min-height: 100vh;
}
.nav-link {
  padding: 6px 12px;
  border-radius: 999px;
  color: var(--color-text);
  text-decoration: none;
  font-weight: 600;
  font-size: 0.88rem;
  white-space: nowrap !important;
  display: inline-block;
  transition: all 0.2s;
}
.nav-link:hover, .nav-link.router-link-active {
  color: var(--color-accent);
  background: rgba(212, 175, 55, 0.12);
}
.cart-pill:hover {
  background: rgba(212, 175, 55, 0.22) !important;
  transform: translateY(-1px);
}
.footer-link {
  color: var(--color-text-light);
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.2s;
}
.footer-link:hover {
  color: var(--color-accent);
}
@media (max-width: 1100px) {
  #main-nav { display: none; }
  #header-actions { display: none; }
  .burger-btn { display: block !important; }
}
</style>
