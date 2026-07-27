<template>
  <div class="admin-dashboard">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8" style="margin-top: 30px; flex-wrap: wrap; gap: 16px;">
      <div>
        <div class="flex items-center gap-3 mb-1">
          <h1 style="font-size: 2rem;">Tableau de bord</h1>
          <span v-if="auth.user?.role === 'super_admin'" style="background: linear-gradient(135deg, #F0CC55, #D4AF37); color: #050811; padding: 4px 12px; border-radius: 999px; font-weight: 800; font-size: 0.8rem;">
            👑 SUPER ADMIN
          </span>
          <span v-else-if="auth.user?.role === 'admin'" style="background: rgba(59,130,246,0.15); color: #3b82f6; padding: 4px 12px; border-radius: 999px; font-weight: 700; font-size: 0.8rem;">
            🛡️ ADMIN
          </span>
          <span v-else-if="auth.user?.role === 'creator'" style="background: rgba(16,185,129,0.15); color: #10b981; padding: 4px 12px; border-radius: 999px; font-weight: 700; font-size: 0.8rem;">
            🛠️ CRÉATEUR
          </span>
          <span v-else-if="auth.user?.role === 'editor'" style="background: rgba(168,85,247,0.15); color: #a855f7; padding: 4px 12px; border-radius: 999px; font-weight: 700; font-size: 0.8rem;">
            ✍️ RÉDACTEUR
          </span>
          <span v-else-if="auth.user?.role === 'support'" style="background: rgba(236,72,153,0.15); color: #ec4899; padding: 4px 12px; border-radius: 999px; font-weight: 700; font-size: 0.8rem;">
            🎧 SUPPORT
          </span>
        </div>
        <p style="color: var(--color-text-light);">Bienvenue, {{ auth.user?.name || 'Administrateur' }} 👋</p>
      </div>
      <div class="flex gap-2">
        <button @click="generatePDFReport" class="btn btn-secondary" style="font-size: 0.9rem;">📄 Rapport Financier (PDF)</button>
        <button @click="handleLogout" class="btn btn-secondary" style="font-size: 0.9rem;">🚪 Déconnexion</button>
      </div>
    </div>

    <!-- Onglets de navigation -->
    <div class="flex gap-2 mb-8" style="border-bottom: 1px solid var(--color-border); padding-bottom: 0; flex-wrap: wrap;">
      <button v-for="tab in visibleTabs" :key="tab.id" @click="activeTab = tab.id"
        :style="activeTab === tab.id ? 'border-bottom: 2px solid var(--color-accent); color: var(--color-accent); font-weight: 800;' : 'color: var(--color-text-light);'"
        style="background: none; border: none; padding: 12px 20px; cursor: pointer; font-size: 0.95rem; margin-bottom: -1px; transition: all 0.2s;">
        {{ tab.icon }} {{ tab.label }}
      </button>
    </div>

    <!-- TAB 1 : VUE D'ENSEMBLE -->
    <div v-if="activeTab === 'overview'">
      <div class="grid mb-8" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
        <div class="glass" style="padding: 24px; border-radius: 16px; border-left: 4px solid var(--color-accent);">
          <p style="color: var(--color-text-light); font-size: 0.9rem;">Ventes Totales</p>
          <h2 style="color: var(--color-accent); font-size: 2.2rem; font-weight: 800;">{{ totalRevenue.toFixed(2) }} €</h2>
        </div>
        <div class="glass" style="padding: 24px; border-radius: 16px; border-left: 4px solid #3b82f6;">
          <p style="color: var(--color-text-light); font-size: 0.9rem;">Commandes</p>
          <h2 style="color: #3b82f6; font-size: 2.2rem; font-weight: 800;">{{ orders.length }}</h2>
        </div>
        <div class="glass" style="padding: 24px; border-radius: 16px; border-left: 4px solid #22c55e;">
          <p style="color: var(--color-text-light); font-size: 0.9rem;">Produits Actifs</p>
          <h2 style="color: #22c55e; font-size: 2.2rem; font-weight: 800;">{{ products.length }}</h2>
        </div>
        <div class="glass" style="padding: 24px; border-radius: 16px; border-left: 4px solid #a855f7;">
          <p style="color: var(--color-text-light); font-size: 0.9rem;">Abonnés Newsletter</p>
          <h2 style="color: #a855f7; font-size: 2.2rem; font-weight: 800;">{{ subscribers.length }}</h2>
        </div>
      </div>

      <!-- Graphique d'Analyse des Ventes -->
      <div class="mb-8">
        <SalesChart />
      </div>

      <!-- Dernières commandes -->
      <div class="glass" style="padding: 28px; border-radius: 16px;">
        <div class="flex justify-between items-center mb-4" style="flex-wrap: wrap; gap: 12px;">
          <h3>📋 Dernières Commandes</h3>
          <button @click="exportOrdersCSV" class="btn btn-secondary" style="padding: 6px 14px; font-size: 0.85rem;">
            📥 Exporter CSV (Ventes)
          </button>
        </div>
        <div v-if="orders.length === 0" style="color: var(--color-text-light); text-align: center; padding: 30px;">Aucune commande pour le moment.</div>
        <table v-else style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Email</th>
              <th style="padding: 12px; text-align: left;">Total</th>
              <th style="padding: 12px; text-align: left;">Statut</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders.slice(0, 5)" :key="order.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px;">#{{ order.id }}</td>
              <td style="padding: 12px;">{{ order.email }}</td>
              <td style="padding: 12px; color: var(--color-accent); font-weight: 700;">{{ order.total_amount }} €</td>
              <td style="padding: 12px;">
                <span :style="order.status === 'paid' ? 'background: rgba(34,197,94,0.15); color: #22c55e;' : 'background: rgba(234,179,8,0.15); color: #eab308;'" style="padding: 3px 10px; border-radius: 999px; font-size: 0.8rem; font-weight: 600;">
                  {{ order.status === 'paid' ? '✅ Payée' : '⏳ En attente' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2 : GESTION DES UTILISATEURS & ROLES (SUPER ADMIN ONLY) -->
    <div v-if="activeTab === 'users' && auth.user?.role === 'super_admin'">
      <div class="glass" style="padding: 28px; border-radius: 16px;">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3>👑 Gestion des Utilisateurs & Rôles</h3>
            <p style="color: var(--color-text-light); font-size: 0.85rem;">Attribuez des rôles (Client, Admin, Super Admin) ou gérez les accès système.</p>
          </div>
          <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 0.85rem;">
            {{ usersList.length }} compte(s) au total
          </span>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Nom</th>
              <th style="padding: 12px; text-align: left;">Email</th>
              <th style="padding: 12px; text-align: left;">Rôle Actuel</th>
              <th style="padding: 12px; text-align: left;">Modifier le Rôle</th>
              <th style="padding: 12px; text-align: left;">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in usersList" :key="u.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ u.id }}</td>
              <td style="padding: 12px; font-weight: 600;">{{ u.name }}</td>
              <td style="padding: 12px;">{{ u.email }}</td>
              <td style="padding: 12px;">
                <span v-if="u.role === 'super_admin'" style="background: linear-gradient(135deg, #F0CC55, #D4AF37); color: #050811; padding: 3px 10px; border-radius: 999px; font-weight: 800; font-size: 0.75rem;">
                  👑 Super Admin
                </span>
                <span v-else-if="u.role === 'admin'" style="background: rgba(59,130,246,0.15); color: #3b82f6; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                  🛡️ Admin
                </span>
                <span v-else-if="u.role === 'creator'" style="background: rgba(16,185,129,0.15); color: #10b981; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                  🛠️ Créateur / Vendeur
                </span>
                <span v-else-if="u.role === 'editor'" style="background: rgba(168,85,247,0.15); color: #a855f7; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                  ✍️ Rédacteur Blog
                </span>
                <span v-else-if="u.role === 'support'" style="background: rgba(236,72,153,0.15); color: #ec4899; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                  🎧 Support Client
                </span>
                <span v-else style="background: rgba(100,116,139,0.15); color: var(--color-text-light); padding: 3px 10px; border-radius: 999px; font-weight: 600; font-size: 0.75rem;">
                  👤 Client
                </span>
              </td>
              <td style="padding: 12px;">
                <select :value="u.role" @change="changeRole(u.id, $event.target.value)" style="padding: 6px 12px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.85rem;">
                  <option value="client">👤 Client</option>
                  <option value="creator">🛠️ Créateur / Vendeur</option>
                  <option value="editor">✍️ Rédacteur Blog</option>
                  <option value="support">🎧 Support Client</option>
                  <option value="admin">🛡️ Admin</option>
                  <option value="super_admin">👑 Super Admin</option>
                </select>
              </td>
              <td style="padding: 12px;">
                <button v-if="u.id !== auth.user?.id" @click="deleteUser(u.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem;">
                  🗑️ Supprimer
                </button>
                <span v-else style="font-size: 0.8rem; color: var(--color-text-light); font-style: italic;">(Vous)</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3 : PRODUITS -->
    <div v-if="activeTab === 'products'">
      <div class="glass" style="padding: 28px; border-radius: 16px;">
        <div class="flex justify-between items-center mb-6">
          <h3>🛍️ Gestion des Produits</h3>
          <button @click="showForm = !showForm" class="btn btn-primary">
            {{ showForm ? '✕ Annuler' : '+ Ajouter un Produit' }}
          </button>
        </div>

        <div v-if="showForm" class="mb-8" style="background: rgba(212,175,55,0.05); border: 1px solid rgba(212,175,55,0.2); padding: 24px; border-radius: 12px;">
          <h4 class="mb-4">Nouveau Produit</h4>
          <form @submit.prevent="createProduct" class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
              <label class="form-label">Titre</label>
              <input v-model="newProduct.title" type="text" required class="form-input" placeholder="ex: Mega Pack Prompts" />
            </div>
            <div>
              <label class="form-label">Prix (€)</label>
              <input v-model="newProduct.price" type="number" step="0.01" required class="form-input" placeholder="9.99" />
            </div>
            <div>
              <label class="form-label">Type</label>
              <select v-model="newProduct.type" class="form-input">
                <option value="Pack">Pack</option>
                <option value="Template">Template</option>
                <option value="E-book">E-book</option>
                <option value="Guide">Guide</option>
                <option value="Outil">Outil</option>
              </select>
            </div>
            <div>
              <label class="form-label">Catégorie (ID)</label>
              <input v-model="newProduct.category_id" type="number" required class="form-input" placeholder="1" />
            </div>
            <div style="grid-column: span 2;">
              <label class="form-label">Description</label>
              <textarea v-model="newProduct.description" required class="form-input" rows="3" placeholder="Description détaillée du produit..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="grid-column: span 2;">✅ Créer le produit</button>
          </form>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Titre</th>
              <th style="padding: 12px; text-align: left;">Type</th>
              <th style="padding: 12px; text-align: left;">Prix</th>
              <th style="padding: 12px; text-align: left;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ product.id }}</td>
              <td style="padding: 12px; font-weight: 600;">{{ product.title }}</td>
              <td style="padding: 12px;"><span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 2px 8px; border-radius: 999px; font-size: 0.8rem;">{{ product.type }}</span></td>
              <td style="padding: 12px; color: var(--color-accent); font-weight: 700;">{{ product.price }} €</td>
              <td style="padding: 12px;">
                <button @click="deleteProduct(product.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem;">🗑️ Supprimer</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 4 : BLOG -->
    <div v-if="activeTab === 'blog'">
      <div class="glass" style="padding: 28px; border-radius: 16px;">
        <div class="flex justify-between items-center mb-6">
          <h3>📝 Gestion du Blog</h3>
          <button @click="showPostForm = !showPostForm" class="btn btn-primary">
            {{ showPostForm ? '✕ Annuler' : '+ Nouvel Article' }}
          </button>
        </div>

        <div v-if="showPostForm" class="mb-8" style="background: rgba(212,175,55,0.05); border: 1px solid rgba(212,175,55,0.2); padding: 24px; border-radius: 12px;">
          <h4 class="mb-4">Nouvel Article de Blog</h4>
          <form @submit.prevent="createPost" class="flex flex-col gap-4">
            <div>
              <label class="form-label">Titre de l'article</label>
              <input v-model="newPost.title" type="text" required class="form-input" placeholder="ex: 10 Prompts IA indispensables" />
            </div>
            <div>
              <label class="form-label">Contenu de l'article</label>
              <textarea v-model="newPost.content" required class="form-input" rows="6" placeholder="Rédigez votre article ici..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">✅ Publier l'article</button>
          </form>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Titre</th>
              <th style="padding: 12px; text-align: left;">Slug</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="post in posts" :key="post.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ post.id }}</td>
              <td style="padding: 12px; font-weight: 600;">{{ post.title }}</td>
              <td style="padding: 12px; font-size: 0.85rem; color: var(--color-accent);">{{ post.slug }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 5 : COUPONS -->
    <div v-if="activeTab === 'coupons'">
      <div class="glass" style="padding: 28px; border-radius: 16px;">
        <div class="flex justify-between items-center mb-6">
          <h3>🎟️ Gestion des Coupons</h3>
          <button @click="showCouponForm = !showCouponForm" class="btn btn-primary">
            {{ showCouponForm ? '✕ Annuler' : '+ Ajouter un Coupon' }}
          </button>
        </div>
        <div v-if="showCouponForm" class="mb-8" style="background: rgba(212,175,55,0.05); border: 1px solid rgba(212,175,55,0.2); padding: 24px; border-radius: 12px;">
          <form @submit.prevent="createCoupon" class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
              <label class="form-label">Code promo</label>
              <input v-model="newCoupon.code" type="text" required class="form-input" placeholder="ex: YASS20" />
            </div>
            <div>
              <label class="form-label">Réduction (€)</label>
              <input v-model="newCoupon.discount_amount" type="number" step="0.01" class="form-input" placeholder="ex: 5.00" />
            </div>
            <button type="submit" class="btn btn-primary" style="grid-column: span 2;">✅ Créer le coupon</button>
          </form>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">Code</th>
              <th style="padding: 12px; text-align: left;">Réduction €</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in coupons" :key="c.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; font-weight: 700; color: var(--color-accent);">{{ c.code }}</td>
              <td style="padding: 12px;">{{ c.discount_amount ? c.discount_amount + ' €' : '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 6 : NEWSLETTER -->
    <div v-if="activeTab === 'newsletter'">
      <div class="glass" style="padding: 28px; border-radius: 16px;">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 12px;">
          <h3>📧 Abonnés Newsletter ({{ subscribers.length }})</h3>
          <button @click="exportSubscribersCSV" class="btn btn-secondary" style="padding: 6px 14px; font-size: 0.85rem;">
            📥 Exporter CSV (Abonnés)
          </button>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">Email</th>
              <th style="padding: 12px; text-align: left;">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sub in subscribers" :key="sub.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px;">{{ sub.email }}</td>
              <td style="padding: 12px; color: var(--color-text-light);">{{ new Date(sub.created_at).toLocaleDateString('fr-FR') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import { ref, computed, onMounted } from 'vue';
import SalesChart from '../components/SalesChart.vue';
import axios from 'axios';

const auth = useAuthStore();
const router = useRouter();

const activeTab = ref('overview');

const baseTabs = [
  { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
  { id: 'products', icon: '🛍️', label: 'Produits' },
  { id: 'blog', icon: '📝', label: 'Blog' },
  { id: 'coupons', icon: '🎟️', label: 'Coupons' },
  { id: 'newsletter', icon: '📧', label: 'Newsletter' },
];

const visibleTabs = computed(() => {
  const role = auth.user?.role;
  if (role === 'super_admin') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'users', icon: '👑', label: 'Gestion Utilisateurs & Rôles' },
      { id: 'products', icon: '🛍️', label: 'Produits' },
      { id: 'blog', icon: '📝', label: 'Blog' },
      { id: 'coupons', icon: '🎟️', label: 'Coupons' },
      { id: 'newsletter', icon: '📧', label: 'Newsletter' },
    ];
  }
  if (role === 'creator') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'products', icon: '🛍️', label: 'Mes Produits' },
    ];
  }
  if (role === 'editor') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'blog', icon: '📝', label: 'Articles de Blog' },
    ];
  }
  if (role === 'support') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'newsletter', icon: '📧', label: 'Contacts & Abonnés' },
    ];
  }
  return baseTabs;
});

const products = ref([]);
const orders = ref([]);
const posts = ref([]);
const subscribers = ref([]);
const coupons = ref([]);
const usersList = ref([]);

const showForm = ref(false);
const showPostForm = ref(false);
const showCouponForm = ref(false);

const newProduct = ref({ title: '', price: '', type: 'Pack', description: '', category_id: 1 });
const newPost = ref({ title: '', content: '', is_published: true });
const newCoupon = ref({ code: '', discount_amount: '', discount_percentage: '', expires_at: '' });

const totalRevenue = computed(() => orders.value.reduce((sum, o) => sum + parseFloat(o.total_amount || 0), 0));
const authHeaders = computed(() => ({ headers: { Authorization: `Bearer ${auth.token}` } }));

onMounted(async () => {
  const allowedRoles = ['admin', 'super_admin', 'creator', 'editor', 'support'];
  if (!auth.token || !allowedRoles.includes(auth.user?.role)) { 
    router.push('/login'); 
    return; 
  }

  // Chargement individuel sécurisé
  loadProducts();
  loadOrders();
  loadPosts();
  loadSubscribers();
  loadCoupons();
  if (auth.user?.role === 'super_admin') {
    loadUsers();
  }
});

const loadProducts = async () => {
  const res = await axios.get('http://localhost:8000/api/products');
  products.value = res.data;
};
const loadOrders = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/orders', authHeaders.value);
    orders.value = res.data;
  } catch(e) { orders.value = []; }
};
const loadPosts = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/posts');
    posts.value = res.data;
  } catch(e) { posts.value = []; }
};
const loadSubscribers = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/newsletter', authHeaders.value);
    subscribers.value = res.data;
  } catch(e) { subscribers.value = []; }
};
const loadCoupons = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/coupons', authHeaders.value);
    coupons.value = res.data;
  } catch(e) { coupons.value = []; }
};
const loadUsers = async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/users', authHeaders.value);
    usersList.value = res.data;
  } catch(e) { 
    console.error('Erreur chargement utilisateurs', e);
    usersList.value = []; 
  }
};

const changeRole = async (userId, newRole) => {
  try {
    const res = await axios.put(`http://localhost:8000/api/users/${userId}/role`, { role: newRole }, authHeaders.value);
    alert(res.data.message);
    await loadUsers();
  } catch(e) {
    alert(e.response?.data?.message || 'Erreur lors de la modification du rôle.');
  }
};

const deleteUser = async (userId) => {
  if (confirm('Supprimer définitivement cet utilisateur ?')) {
    try {
      const res = await axios.delete(`http://localhost:8000/api/users/${userId}`, authHeaders.value);
      alert(res.data.message);
      await loadUsers();
    } catch(e) {
      alert(e.response?.data?.message || 'Erreur lors de la suppression.');
    }
  }
};

const createProduct = async () => {
  try {
    await axios.post('http://localhost:8000/api/products', newProduct.value, authHeaders.value);
    showForm.value = false;
    newProduct.value = { title: '', price: '', type: 'Pack', description: '', category_id: 1 };
    await loadProducts();
  } catch(e) { alert('Erreur lors de la création du produit'); }
};

const createPost = async () => {
  try {
    await axios.post('http://localhost:8000/api/posts', newPost.value, authHeaders.value);
    showPostForm.value = false;
    newPost.value = { title: '', content: '', is_published: true };
    await loadPosts();
  } catch(e) { alert('Erreur lors de la création de l\'article'); }
};

const exportOrdersCSV = () => {
  if (orders.value.length === 0) return alert('Aucune commande à exporter');
  const headers = ['ID', 'Email', 'Total_Euro', 'Statut', 'Date'];
  const rows = orders.value.map(o => [o.id, o.email, o.total_amount, o.status, o.created_at]);
  downloadCSV('ventes_yass_digital_lab.csv', [headers, ...rows]);
};

const exportSubscribersCSV = () => {
  if (subscribers.value.length === 0) return alert('Aucun abonné à exporter');
  const headers = ['ID', 'Email', 'Statut', 'Date_Inscription'];
  const rows = subscribers.value.map(s => [s.id, s.email, s.is_active ? 'Actif' : 'Inactif', s.created_at]);
  downloadCSV('abonnes_newsletter_yass_digital_lab.csv', [headers, ...rows]);
};

const downloadCSV = (filename, content) => {
  const csvContent = 'data:text/csv;charset=utf-8,' + content.map(e => e.join(',')).join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', filename);
  document.body.appendChild(link);
  link.click();
  link.remove();
};

const deleteProduct = async (id) => {
  if (confirm('Supprimer ce produit ?')) {
    await axios.delete(`http://localhost:8000/api/products/${id}`, authHeaders.value);
    await loadProducts();
  }
};

const generatePDFReport = () => {
  window.print();
};

const handleLogout = async () => {
  await auth.logout();
  router.push('/');
};
</script>

<style scoped>
.form-label {
  display: block;
  margin-bottom: 6px;
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--color-text-light);
}
.form-input {
  width: 100%;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid var(--color-border);
  background: var(--color-bg);
  color: var(--color-text);
  font-size: 0.95rem;
}
.form-input:focus {
  outline: none;
  border-color: var(--color-accent);
}
</style>
