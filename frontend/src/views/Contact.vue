<template>
  <div class="contact-page">
    <div class="glass" style="margin-top: 40px; padding: 60px 40px; border-radius: 24px; max-width: 900px; margin-inline: auto;">
      
      <!-- Header -->
      <div class="text-center mb-8">
        <div style="font-size: 3rem; margin-bottom: 12px;">📬</div>
        <h1 class="mb-2" style="font-size: 2.2rem;">Contactez-moi</h1>
        <p style="color: var(--color-text-light); font-size: 1.05rem;">Une question, un projet, une collaboration ? Je réponds sous 24h.</p>
      </div>

      <div class="grid" style="grid-template-columns: 1fr 1.5fr; gap: 50px; align-items: start; flex-wrap: wrap;">
        
        <!-- Infos -->
        <div>
          <h3 class="mb-6" style="color: var(--color-accent);">Mes coordonnées</h3>
          <div class="flex flex-col gap-4">
            <div class="flex items-center gap-3" style="padding: 16px; background: rgba(212,175,55,0.08); border-radius: 12px; border: 1px solid rgba(212,175,55,0.2);">
              <span style="font-size: 1.5rem;">📧</span>
              <div>
                <p style="font-size: 0.8rem; color: var(--color-text-light);">Email</p>
                <p style="font-weight: 600;">yassoungo@yassdigital.lab</p>
              </div>
            </div>
            <div class="flex items-center gap-3" style="padding: 16px; background: rgba(212,175,55,0.08); border-radius: 12px; border: 1px solid rgba(212,175,55,0.2);">
              <span style="font-size: 1.5rem;">🌍</span>
              <div>
                <p style="font-size: 0.8rem; color: var(--color-text-light);">Localisation</p>
                <p style="font-weight: 600;">Côte d'Ivoire / Remote</p>
              </div>
            </div>
            <div class="flex items-center gap-3" style="padding: 16px; background: rgba(212,175,55,0.08); border-radius: 12px; border: 1px solid rgba(212,175,55,0.2);">
              <span style="font-size: 1.5rem;">⏰</span>
              <div>
                <p style="font-size: 0.8rem; color: var(--color-text-light);">Disponibilité</p>
                <p style="font-weight: 600;">Lun–Ven, 8h–18h GMT</p>
              </div>
            </div>
          </div>

          <!-- Réseaux sociaux -->
          <h3 class="mb-4 mt-8" style="color: var(--color-accent);">Réseaux sociaux</h3>
          <div class="flex gap-3" style="flex-wrap: wrap;">
            <a href="#" class="social-btn">🐙 GitHub</a>
            <a href="#" class="social-btn">💼 LinkedIn</a>
            <a href="#" class="social-btn">🐦 Twitter</a>
          </div>
        </div>

        <!-- Formulaire -->
        <div>
          <div v-if="sent" class="text-center" style="padding: 40px; background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); border-radius: 16px;">
            <div style="font-size: 3rem; margin-bottom: 12px;">✅</div>
            <h3 class="mb-2">Message envoyé !</h3>
            <p style="color: var(--color-text-light);">Merci de m'avoir contacté. Je vous répondrai dans les 24 heures.</p>
            <button @click="sent = false" class="btn btn-secondary" style="margin-top: 20px;">Envoyer un autre message</button>
          </div>
          <form v-else @submit.prevent="sendMessage" class="flex flex-col gap-4">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <label class="form-label">Nom complet</label>
                <input v-model="form.name" type="text" required class="form-input" placeholder="John Doe" />
              </div>
              <div>
                <label class="form-label">Email</label>
                <input v-model="form.email" type="email" required class="form-input" placeholder="john@example.com" />
              </div>
            </div>
            <div>
              <label class="form-label">Sujet</label>
              <select v-model="form.subject" class="form-input">
                <option value="projet">💼 Nouveau projet</option>
                <option value="service">🛠️ Demande de service</option>
                <option value="produit">📦 Question sur un produit</option>
                <option value="partenariat">🤝 Partenariat</option>
                <option value="autre">💬 Autre</option>
              </select>
            </div>
            <div>
              <label class="form-label">Message</label>
              <textarea v-model="form.message" required class="form-input" rows="6" placeholder="Décrivez votre projet ou votre question..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" :disabled="sending" style="width: 100%; justify-content: center;">
              {{ sending ? '⏳ Envoi en cours...' : '📤 Envoyer le message' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const sent = ref(false);
const sending = ref(false);
const form = ref({ name: '', email: '', subject: 'projet', message: '' });

const sendMessage = async () => {
  sending.value = true;
  // Simulation d'envoi (à connecter à un vrai backend mail type Mailgun/Brevo)
  await new Promise(r => setTimeout(r, 1500));
  sent.value = true;
  sending.value = false;
  form.value = { name: '', email: '', subject: 'projet', message: '' };
};
</script>

<style scoped>
.form-label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem; color: var(--color-text-light); }
.form-input { width: 100%; padding: 12px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.95rem; }
.form-input:focus { outline: none; border-color: var(--color-accent); }
.social-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; border: 1px solid var(--color-border); color: var(--color-text); text-decoration: none; font-size: 0.9rem; transition: all 0.2s; }
.social-btn:hover { border-color: var(--color-accent); color: var(--color-accent); }
@media (max-width: 768px) { .grid { grid-template-columns: 1fr !important; } }
</style>
