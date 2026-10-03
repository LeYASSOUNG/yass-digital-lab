<template>
  <footer style="background: linear-gradient(180deg, #060A14 0%, #04070F 100%); backdrop-filter: blur(24px); border-top: 1px solid rgba(124,58,237,0.15); padding: 72px 20px 32px; margin-top: 50px; color: #FFFFFF;">
    <div class="container">
      <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 48px; margin-bottom: 56px;">
        
        <!-- Brand -->
        <div>
          <div class="mb-4"><Logo :size="44" :showTagline="true" :inverted="true" /></div>
          <p style="color: #94A3B8; font-size: 0.93rem; line-height: 1.75; margin-bottom: 24px;">
            Plateforme de ressources numériques intelligentes — templates SaaS, packs IA, et solutions sur mesure pour les créateurs d'aujourd'hui.
          </p>
          <div class="flex gap-3">
            <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" class="footer-social-btn" title="Portfolio">
              <Globe :size="17" />
            </a>
            <a href="https://github.com/LeYASSOUNG" target="_blank" class="footer-social-btn" title="GitHub">
              <Github :size="17" />
            </a>
            <a href="#" class="footer-social-btn" title="LinkedIn">
              <Linkedin :size="17" />
            </a>
          </div>
        </div>

        <!-- Navigation -->
        <div>
          <h4 style="margin-bottom: 20px; font-size: 0.78rem; color: #94A3B8; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Plateforme</h4>
          <div style="display: flex; flex-direction: column; gap: 10px;">
            <router-link to="/products" class="footer-link flex items-center gap-2"><Package :size="14" /> {{ t('products') }}</router-link>
            <router-link to="/services" class="footer-link flex items-center gap-2"><Briefcase :size="14" /> {{ t('services') }}</router-link>
            <router-link to="/suivi-devis" class="footer-link flex items-center gap-2" style="color: #F5C027 !important;"><ClipboardList :size="14" /> Suivi de Devis</router-link>
            <router-link to="/coupons" class="footer-link flex items-center gap-2" style="color: var(--color-accent) !important;"><Ticket :size="14" /> Codes Promo</router-link>
            <router-link to="/blog" class="footer-link flex items-center gap-2"><FileText :size="14" /> {{ t('blog') }}</router-link>
            <router-link to="/about" class="footer-link flex items-center gap-2"><User :size="14" /> {{ t('about') }}</router-link>
            <router-link to="/contact" class="footer-link flex items-center gap-2"><Mail :size="14" /> {{ t('contact') }}</router-link>
          </div>
        </div>

        <!-- Account -->
        <div>
          <h4 style="margin-bottom: 20px; font-size: 0.78rem; color: #94A3B8; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Compte</h4>
          <div style="display: flex; flex-direction: column; gap: 10px;">
            <router-link to="/login" class="footer-link flex items-center gap-2"><LogIn :size="14" /> {{ t('login') }}</router-link>
            <router-link to="/dashboard" class="footer-link flex items-center gap-2"><User :size="14" /> {{ t('mySpace') }}</router-link>
            <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" class="footer-link flex items-center gap-2" style="color: #F0CC55 !important; font-weight: 700;">
              <Globe :size="14" /> Portfolio créateur <ExternalLink :size="11" />
            </a>
          </div>
        </div>

        <!-- Newsletter -->
        <div>
          <h4 style="margin-bottom: 14px; font-size: 0.78rem; color: #94A3B8; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">{{ t('newsletterTitle') }}</h4>
          <p style="color: #94A3B8; font-size: 0.9rem; margin-bottom: 18px; line-height: 1.65;">
            {{ t('newsletterSub') }}
          </p>
          <form @submit.prevent="subscribeNewsletter" class="flex gap-2" style="flex-wrap: wrap;">
            <input v-model="newsletterEmail" type="email" placeholder="votre@email.com" required style="flex: 1; min-width: 160px; padding: 11px 14px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.12); background: rgba(255,255,255,0.06); color: #FFFFFF; font-size: 0.88rem;" />
            <button type="submit" class="btn btn-primary" :class="{ 'btn-loading': subscribing }" style="padding: 11px 16px;" :disabled="subscribing">
              <span v-if="!subscribing"><ArrowRight :size="16" /></span>
            </button>
          </form>
        </div>
      </div>

      <!-- Footer Bottom -->
      <div style="border-top: 1px solid rgba(255,255,255,0.07); padding-top: 24px; flex-wrap: wrap; gap: 12px;" class="flex justify-between items-center">
        <div class="flex items-center gap-3" style="flex-wrap: wrap;">
          <span style="color: #64748B; font-size: 0.85rem;">© 2026 Yass Digital Lab — {{ t('footerRights') }}</span>
          <button @click="$emit('open-status-modal')" class="flex items-center gap-1.5" style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: #34D399; font-size: 0.75rem; font-weight: 700; padding: 2px 10px; border-radius: 999px; cursor: pointer; transition: all 0.2s;">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
            <span>Systèmes opérationnels (100%)</span>
          </button>
        </div>
        <div class="flex items-center gap-3" style="flex-wrap: wrap;">
          <span style="color: #475569; font-size: 0.78rem;">v2.0.0</span>
          <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" style="color: #818CF8; font-weight: 600; text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px;">
            <Globe :size="13" /> Diarrassouba Y. Youssouf <ExternalLink :size="11" />
          </a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToastStore } from '../../stores/toast';
import api from '../../api';
import Logo from '../Logo.vue';
import { 
  Globe, Github, Linkedin, Package, Briefcase, ClipboardList, 
  Ticket, FileText, User, Mail, LogIn, ExternalLink, ArrowRight 
} from 'lucide-vue-next';

const { t } = useI18n();
const toastStore = useToastStore();
const newsletterEmail = ref('');
const subscribing = ref(false);

defineEmits(['open-status-modal']);

const subscribeNewsletter = async () => {
  if (!newsletterEmail.value) return;
  subscribing.value = true;
  try {
    const res = await api.post('/newsletter/subscribe', { email: newsletterEmail.value });
    toastStore.showToast(res.data.message || 'Inscription réussie ! ✅', 'success');
    newsletterEmail.value = '';
  } catch (error) {
    toastStore.showToast(error.response?.status === 422 ? 'Cet e-mail est déjà inscrit.' : 'Erreur, veuillez réessayer.', 'error');
  }
  subscribing.value = false;
};
</script>

<style scoped>
.footer-link {
  color: #475569;
  text-decoration: none;
  font-size: 0.88rem;
  font-weight: 500;
  transition: color 0.2s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
}
.footer-link:hover { color: var(--color-primary); }

.footer-social-btn {
  width: 38px; height: 38px;
  border-radius: 10px;
  border: 1px solid rgba(255,255,255,0.1);
  background: rgba(255,255,255,0.04);
  color: #64748B;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: all 0.22s ease;
  cursor: pointer;
}
.footer-social-btn:hover {
  background: rgba(124,58,237,0.18);
  border-color: rgba(124,58,237,0.4);
  color: var(--color-primary);
  transform: translateY(-2px);
}
</style>
