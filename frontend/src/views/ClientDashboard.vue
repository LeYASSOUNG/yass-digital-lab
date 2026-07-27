<template>
  <div class="client-dashboard container" style="margin-top: 30px;">
    <!-- Welcome Header Banner -->
    <div class="glass flex justify-between items-center mb-8" style="padding: 36px 40px; border-radius: 24px; background: linear-gradient(135deg, rgba(212,175,55,0.12), rgba(15,23,42,0.4)); flex-wrap: wrap; gap: 20px;">
      <div class="flex items-center gap-4">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--color-accent); color: #000; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 800; box-shadow: var(--shadow-accent);">
          👤
        </div>
        <div>
          <h1 style="font-size: 1.8rem; margin-bottom: 2px;">Espace Client</h1>
          <p style="color: var(--color-text-light); font-size: 0.95rem;">Bienvenue {{ profile.name }} ! Retrouvez vos achats, factures et profil.</p>
        </div>
      </div>
      <div class="flex gap-2">
        <button @click="activeSection = activeSection === 'profile' ? 'purchases' : 'profile'" class="btn btn-secondary" style="font-size: 0.9rem;">
          {{ activeSection === 'profile' ? '📦 Mes Achats' : '⚙️ Mon Profil' }}
        </button>
        <button @click="handleLogout" class="btn btn-secondary" style="font-size: 0.9rem;">🚪 Déconnexion</button>
      </div>
    </div>

    <!-- SECTION 1 : ACHATS & FICHIERS -->
    <div v-if="activeSection === 'purchases'">
      <div class="glass" style="padding: 36px; border-radius: 20px; margin-bottom: 30px;">
        <div class="flex justify-between items-center mb-6">
          <h2>📦 Mes Produits Numériques & Licences</h2>
          <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 4px 12px; border-radius: 999px; font-weight: 700; font-size: 0.85rem;">
            {{ purchases.length }} produit(s) acquis
          </span>
        </div>

        <!-- State : Aucun achat -->
        <div v-if="purchases.length === 0" class="text-center" style="padding: 60px;">
          <div style="font-size: 4rem; margin-bottom: 16px;">🛍️</div>
          <h3 class="mb-2">Vous n'avez pas encore effectué d'achats</h3>
          <p style="color: var(--color-text-light); margin-bottom: 24px;">Explorez nos catalogues pour trouver les meilleurs outils et templates.</p>
          <router-link to="/products" class="btn btn-primary">Découvrir le catalogue →</router-link>
        </div>

        <!-- State : Liste des achats -->
        <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px;">
          <div v-for="purchase in purchases" :key="purchase.id" class="card glass flex flex-col justify-between" style="padding: 24px; border-radius: 16px; border: 1px solid var(--color-border); transition: transform 0.25s;">
            <div>
              <div class="flex justify-between items-center mb-3">
                <span style="font-size: 0.75rem; background: rgba(34,197,94,0.15); color: #22c55e; padding: 3px 10px; border-radius: 999px; font-weight: 700;">✅ Licence Active à vie</span>
                <span style="font-size: 0.8rem; color: var(--color-text-light);">📅 {{ purchase.date }}</span>
              </div>
              <h3 class="mb-2" style="font-size: 1.2rem;">{{ purchase.title }}</h3>
              
              <!-- Key License Box -->
              <div class="mb-4" style="background: rgba(0,0,0,0.15); padding: 10px 14px; border-radius: 8px; border: 1px dashed var(--color-accent); font-family: monospace; font-size: 0.8rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--color-accent); font-weight: 700;">🔑 Key: {{ purchase.licenseKey }}</span>
                <span style="color: var(--color-text-light); font-size: 0.75rem;">(1/10 DL)</span>
              </div>
            </div>

            <div class="flex gap-2">
              <button @click="downloadFile(purchase)" class="btn btn-primary" style="flex: 1; padding: 0.6rem 1rem; font-size: 0.9rem;">
                📥 Fichier (.zip)
              </button>
              <button @click="downloadInvoice(purchase.order_id)" class="btn btn-secondary" style="flex: 1; padding: 0.6rem 1rem; font-size: 0.9rem;">
                📄 Facture PDF
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Produits Favoris / Wishlist -->
      <div v-if="wishlist.items.length > 0" class="glass" style="padding: 36px; border-radius: 20px; margin-bottom: 30px;">
        <div class="flex justify-between items-center mb-6">
          <h2>❤️ Mes Produits Favoris ({{ wishlist.items.length }})</h2>
        </div>

        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
          <div v-for="fav in wishlist.items" :key="fav.id" class="card glass p-4" style="border-radius: 16px;">
            <h4 class="mb-2" style="font-size: 1.1rem;">{{ fav.title }}</h4>
            <p style="color: var(--color-accent); font-weight: 700; margin-bottom: 14px;">{{ fav.price }} €</p>
            <div class="flex gap-2">
              <router-link :to="`/products/${fav.id}`" class="btn btn-secondary" style="flex: 1; padding: 6px 12px; font-size: 0.85rem;">
                Voir →
              </router-link>
              <button @click="wishlist.toggleWishlist(fav)" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.85rem; color: #ef4444;">
                🗑️
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Programme de Parrainage & Affiliation -->
      <div class="glass" style="padding: 36px; border-radius: 20px; margin-bottom: 40px;">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 16px;">
          <div>
            <h2>🤝 Programme de Parrainage & Affiliation</h2>
            <p style="color: var(--color-text-light); font-size: 0.9rem;">Gagnez 10% de commission sur chaque achat effectué par vos filleuls.</p>
          </div>
          <div style="background: rgba(16,185,129,0.15); color: #10b981; padding: 8px 18px; border-radius: 999px; font-weight: 800; font-size: 0.95rem;">
            Solde crédité : 15.00 €
          </div>
        </div>

        <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 24px; flex-wrap: wrap;">
          <div style="background: rgba(0,0,0,0.06); padding: 24px; border-radius: 16px; border: 1px solid var(--color-border);">
            <label style="display: block; margin-bottom: 8px; font-weight: 700; font-size: 0.9rem;">Votre Lien de Parrainage Unique</label>
            <div class="flex gap-2">
              <input 
                :value="referralLink" 
                readonly 
                type="text" 
                style="flex: 1; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-family: monospace; font-size: 0.9rem;"
              />
              <button @click="copyReferralLink" class="btn btn-primary" style="white-space: nowrap;">
                📋 Copier le lien
              </button>
            </div>
          </div>

          <div style="background: rgba(212,175,55,0.08); padding: 24px; border-radius: 16px; border: 1px solid rgba(212,175,55,0.25); display: flex; flex-direction: column; justify-content: center;">
            <h4 style="font-size: 0.95rem; margin-bottom: 6px;">💡 Vos Statistiques</h4>
            <p style="font-size: 0.85rem; color: var(--color-text-light); margin: 0;">• 3 Filleuls inscrits</p>
            <p style="font-size: 0.85rem; color: var(--color-text-light); margin: 0;">• 2 Achats validés</p>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION 2 : ÉDITION DU PROFIL & SÉCURITÉ -->
    <div v-else class="glass" style="padding: 36px; border-radius: 20px; margin-bottom: 40px;">
      <h2 class="mb-6">⚙️ Mes Coordonnées & Sécurité</h2>
      
      <form @submit.prevent="updateProfile" class="flex flex-col gap-5" style="max-width: 650px;">
        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Nom complet</label>
            <input v-model="profile.name" type="text" required style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Adresse Email</label>
            <input v-model="profile.email" type="email" required style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" />
          </div>
        </div>

        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Téléphone</label>
            <input v-model="profile.phone" type="tel" style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" placeholder="+33 6 12 34 56 78" />
          </div>
          <div>
            <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Entreprise</label>
            <input v-model="profile.company" type="text" style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" placeholder="Nom de société" />
          </div>
        </div>

        <div>
          <label style="display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem;">Adresse de facturation</label>
          <input v-model="profile.address" type="text" style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" placeholder="Paris, France" />
        </div>

        <div style="border-top: 1px solid var(--color-border); padding-top: 20px; margin-top: 10px;">
          <h4 style="margin-bottom: 12px; font-size: 1rem;">🔒 Nouveau mot de passe (optionnel)</h4>
          <input v-model="profile.newPassword" type="password" style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text);" placeholder="Laisser vide pour ne pas modifier" />
        </div>

        <button type="submit" class="btn btn-primary" style="align-self: flex-start; padding: 10px 24px;">
          ✅ Enregistrer les modifications
        </button>
      </form>
    </div>

  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useWishlistStore } from '../stores/wishlist';
import { useToastStore } from '../stores/toast';
import { useRouter } from 'vue-router';
import { ref, computed, onMounted } from 'vue';

const auth = useAuthStore();
const wishlist = useWishlistStore();
const toastStore = useToastStore();
const router = useRouter();

const activeSection = ref('purchases');

const purchases = ref([
  { id: 1, order_id: 1, title: 'Mega Pack Prompts ChatGPT & Claude', date: '25/07/2026', licenseKey: 'YDL-PACK-98F4' },
  { id: 2, order_id: 2, title: 'Template SaaS Starter Vue 3 + Laravel 12', date: '20/07/2026', licenseKey: 'YDL-SAAS-A12B' }
]);

const profile = ref({
  name: auth.user?.name || 'Jean Dupont',
  email: auth.user?.email || 'client@yassdigital.lab',
  phone: auth.user?.phone || '+33 6 12 34 56 78',
  company: auth.user?.company || 'Yass Agency',
  address: auth.user?.address || 'Paris, France',
  newPassword: ''
});

const referralLink = computed(() => {
  const code = profile.value.name ? profile.value.name.replace(/\s+/g, '').toUpperCase() : 'USER123';
  return `http://localhost:5174/?ref=${code}`;
});

onMounted(() => {
  if (!auth.token) {
    router.push('/login');
  }
});

const updateProfile = () => {
  if (auth.user) {
    auth.user.name = profile.value.name;
    auth.user.email = profile.value.email;
    auth.user.phone = profile.value.phone;
    auth.user.company = profile.value.company;
    auth.user.address = profile.value.address;
    localStorage.setItem('user', JSON.stringify(auth.user));
  }
  toastStore.showToast('Profil et coordonnées mis à jour avec succès ! ✅', 'success');
};

const downloadFile = (purchase) => {
  toastStore.showToast(`Début du téléchargement sécurisé pour : ${purchase.title}`, 'info');
};

const downloadInvoice = (orderId) => {
  window.open(`http://localhost:8000/api/orders/${orderId}/invoice`, '_blank');
};

const copyReferralLink = () => {
  navigator.clipboard.writeText(referralLink.value);
  toastStore.showToast('Lien de parrainage copié dans le presse-papier ! 📋', 'success');
};

const handleLogout = async () => {
  await auth.logout();
  router.push('/');
};
</script>

<style scoped>
.card:hover {
  transform: translateY(-4px);
  border-color: var(--color-accent) !important;
}
</style>
