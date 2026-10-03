<template>
  <div class="contact-page">
    <div class="glass fade-in" style="margin-top: 30px; padding: 60px 45px; border-radius: var(--radius-lg); max-width: 960px; margin-inline: auto; position: relative; overflow: hidden;">
      <div class="hero-glow"></div>

      <!-- Header -->
      <div class="text-center mb-10" style="position: relative; z-index: 2;">
        <span class="badge-pill badge-indigo mb-3">UNE QUESTION UN PROJET ?</span>
        <h1 class="mb-2">Contactez-nous</h1>
        <p style="color: var(--color-text-light); font-size: 1.08rem; max-width: 520px; margin: 0 auto;">
          Une demande de prestation, un projet ou une question sur un produit ? Notre équipe vous répond sous 24h.
        </p>
      </div>

      <div class="grid" style="grid-template-columns: 1fr 1.4fr; gap: 50px; align-items: start; position: relative; z-index: 2;">
        
        <!-- Coordonnées & Support -->
        <div>
          <h3 class="mb-5" style="color: var(--color-primary); font-size: 1.25rem;">Nos Coordonnées</h3>
          <div class="flex flex-col gap-4">
            <div class="flex items-center gap-3.5" style="padding: 16px 20px; background: rgba(124,58,237,0.06); border-radius: var(--radius-md); border: 1px solid rgba(124,58,237,0.15);">
              <div style="color: var(--color-primary);">
                <Mail :size="24" />
              </div>
              <div>
                <p style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 600;">Email direct</p>
                <p style="font-weight: 700; font-size: 0.95rem;">yassoungo@yassdigital.lab</p>
              </div>
            </div>

            <div class="flex items-center gap-3.5" style="padding: 16px 20px; background: rgba(124,58,237,0.06); border-radius: var(--radius-md); border: 1px solid rgba(124,58,237,0.15);">
              <div style="color: #06B6D4;">
                <Globe :size="24" />
              </div>
              <div>
                <p style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 600;">Localisation</p>
                <p style="font-weight: 700; font-size: 0.95rem;">Côte d'Ivoire / Remote Worldwide</p>
              </div>
            </div>

            <div class="flex items-center gap-3.5" style="padding: 16px 20px; background: rgba(124,58,237,0.06); border-radius: var(--radius-md); border: 1px solid rgba(124,58,237,0.15);">
              <div style="color: #F59E0B;">
                <Clock :size="24" />
              </div>
              <div>
                <p style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 600;">Temps de réponse</p>
                <p style="font-weight: 700; font-size: 0.95rem;">Moins de 24h GMT (7j/7)</p>
              </div>
            </div>
          </div>

          <!-- Réseaux & Portfolio -->
          <h3 class="mb-4 mt-8" style="color: var(--color-primary); font-size: 1.15rem;">Liens & Réseaux</h3>
          <div class="flex gap-2.5" style="flex-wrap: wrap;">
            <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" class="social-btn btn-gold">
              <Globe :size="15" /> Portfolio Vercel
            </a>
            <a href="https://github.com/LeYASSOUNG" target="_blank" class="social-btn">
              <Github :size="15" /> GitHub
            </a>
            <a href="#" class="social-btn">
              <Linkedin :size="15" /> LinkedIn
            </a>
          </div>
        </div>

        <!-- Formulaire de Contact avec Formspree -->
        <div>
          <div v-if="sent" class="text-center" style="padding: 40px; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); border-radius: var(--radius-lg);">
            <div style="display: inline-flex; width: 60px; height: 60px; border-radius: 50%; background: #10B981; color: var(--color-text); align-items: center; justify-content: center; margin-bottom: 16px;">
              <CheckCircle2 :size="32" />
            </div>
            <h3 class="mb-2">Message transmis avec succès !</h3>
            <p style="color: var(--color-text-light);">Merci pour votre message. Nous l'avons bien reçu et revenons vers vous sous 24h.</p>
            <button @click="sent = false" class="btn btn-secondary" style="margin-top: 22px;">
              Envoyer un autre message
            </button>
          </div>

          <form v-else @submit.prevent="sendMessage" class="flex flex-col gap-4">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <label class="form-label">Nom complet *</label>
                <input v-model="form.name" type="text" required class="form-input" placeholder="John Doe" />
              </div>
              <div>
                <label class="form-label">Email professionnel *</label>
                <input v-model="form.email" type="email" required class="form-input" placeholder="john@example.com" />
              </div>
            </div>
            <div>
              <label class="form-label">Sujet de votre demande</label>
              <select v-model="form.subject" class="form-input">
                <option value="projet">Nouveau projet sur-mesure</option>
                <option value="service">Demande de service / devis</option>
                <option value="produit">Question sur un produit / pack</option>
                <option value="partenariat">Partenariat</option>
                <option value="autre">Autre question</option>
              </select>
            </div>
            <div>
              <label class="form-label">Message *</label>
              <textarea v-model="form.message" required class="form-input" rows="6" placeholder="Expliquez votre projet ou posez vos questions..."></textarea>
            </div>

            <!-- Notice Contact direct -->
            <div style="font-size: 0.78rem; color: var(--color-text-muted);" class="flex items-center gap-1.5">
              <ShieldCheck :size="14" style="color: var(--color-primary);" />
              <span>Vos données sont sécurisées et envoyées directement à notre équipe.</span>
            </div>

            <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" :disabled="sending" style="width: 100%; padding: 0.85rem;">
              <Send v-if="!sending" :size="17" />
              <span>{{ sending ? 'Envoi du message...' : 'Envoyer le message' }}</span>
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useToastStore } from '../stores/toast';
import { 
  Mail, 
  Globe, 
  Clock, 
  Github, 
  Linkedin, 
  CheckCircle2, 
  Send,
  ShieldCheck
} from 'lucide-vue-next';
import api from '../api';

const toastStore = useToastStore();
const sent = ref(false);
const sending = ref(false);
const form = ref({ name: '', email: '', subject: 'projet', message: '' });

const sendMessage = async () => {
  sending.value = true;

  try {
    const response = await api.post('/contact', {
      name: form.value.name,
      email: form.value.email,
      subject: form.value.subject,
      message: form.value.message
    });

    sent.value = true;
    toastStore.showToast('Message transmis avec succès ! ðŸšFCFA', 'success');
    form.value = { name: '', email: '', subject: 'projet', message: '' };
  } catch (err) {
    console.error('Contact error:', err);
    toastStore.showToast('Erreur lors de l\'envoi du message. Veuillez réessayer.', 'error');
  } finally {
    sending.value = false;
  }
};
</script>

<style scoped>
.contact-page {
  background: var(--page-gradient);
  padding: 30px 0 80px;
  color: var(--color-text);
}

.form-label { 
  display: block; 
  margin-bottom: 6px; 
  font-weight: 700; 
  font-size: 0.88rem; 
  color: var(--color-text); 
}
.form-input { 
  width: 100%; 
  padding: 12px 16px; 
  border-radius: var(--radius-md); 
  border: 1.5px solid rgba(255, 255, 255, 0.08); 
  background: var(--color-bg-elevated); 
  color: var(--color-text); 
  font-size: 0.92rem; 
  transition: all 0.22s ease;
}
.form-input:focus {
  outline: none;
  border-color: rgba(124,58,237,0.7);
  box-shadow: 0 0 0 3px rgba(124,58,237,0.2);
}
.social-btn { 
  display: inline-flex; 
  align-items: center; 
  gap: 6px; 
  padding: 8px 15px; 
  border-radius: var(--radius-pill); 
  border: 1px solid rgba(255, 255, 255, 0.15); 
  color: var(--color-text); 
  text-decoration: none; 
  font-size: 0.88rem; 
  font-weight: 700;
  transition: all 0.2s ease; 
}
.social-btn:hover { 
  border-color: var(--color-primary); 
  color: var(--color-primary); 
}
.contact-page h1, .contact-page h2, .contact-page h3 {
  color: var(--color-text) !important;
}
.contact-page p {
  color: var(--color-text-muted) !important;
}
@media (max-width: 768px) { 
  .grid { grid-template-columns: 1fr !important; } 
}
</style>

