<template>
  <div class="fade-in">
    <div class="glass" style="padding: 28px; border-radius: 20px;">
      <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 14px;">
        <div>
          <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">🛍️ Gestion des Produits & Ressources</h3>
          <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Gérez les packs, templates, e-books et outils au catalogue.</p>
        </div>
        <div class="flex items-center gap-3" style="flex-wrap: wrap;">
          <div style="position: relative; width: 200px;">
            <input v-model="searchProductQuery" type="text" placeholder="Rechercher produit..." style="width: 100%; padding: 6px 12px 6px 15px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
          </div>
          <button @click="exportProductsCSV" class="btn btn-secondary flex items-center gap-1" style="padding: 6px 12px; font-size: 0.8rem;">
            Exporter CSV
          </button>
          <button @click="showForm = !showForm" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.88rem;">
            {{ showForm ? '✕ Annuler' : '+ Nouveau Produit' }}
          </button>
        </div>
      </div>

      <div v-if="showForm" class="mb-8" style="background: rgba(212,175,55,0.05); border: 1px solid rgba(212,175,55,0.25); padding: 24px; border-radius: 16px;">
        <h4 class="mb-4" style="font-size: 1.1rem; font-weight: 700;">Ajouter un Nouveau Produit</h4>
        <form @submit.prevent="createProduct" class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Titre du Produit *</label>
            <input v-model="newProduct.title" type="text" required class="form-input" placeholder="ex: Mega Pack Prompts AI" />
          </div>
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Prix (FCFA) *</label>
            <input v-model="newProduct.price" type="number" step="0.01" required class="form-input" placeholder="19000" />
          </div>
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Type de Ressource</label>
            <select v-model="newProduct.type" class="form-input">
              <option value="Pack">Pack</option>
              <option value="Template">Template</option>
              <option value="E-book">E-book</option>
              <option value="Guide">Guide</option>
              <option value="Outil">Outil</option>
            </select>
          </div>
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Catégorie (ID)</label>
            <input v-model="newProduct.category_id" type="number" required class="form-input" placeholder="1" />
          </div>
          <div style="grid-column: span 2;">
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Description Détaillée</label>
            <textarea v-model="newProduct.description" required class="form-input" rows="3" placeholder="Description complète de la ressource numérique..."></textarea>
          </div>
          <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="grid-column: span 2; padding: 11px 20px; font-weight: 800;">
            ✅ Créer et publier le produit
          </button>
        </form>
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 650px;">
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
            <tr v-for="product in paginatedProducts" :key="product.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ product.id }}</td>
              <td style="padding: 12px; font-weight: 600;">{{ product.title }}</td>
              <td style="padding: 12px;"><span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 2px 9px; border-radius: 999px; font-size: 0.78rem; font-weight: 700;">{{ product.type }}</span></td>
              <td style="padding: 12px; color: var(--color-accent); font-weight: 800;">{{ product.price }} FCFA</td>
              <td style="padding: 12px; display: flex; gap: 8px;">
                <button @click="startEditProduct(product)" style="background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid #3b82f6; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                  ✏️ Modifier
                </button>
                <button @click="deleteProduct(product.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                  🗑️ Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Navigation Pagination Produits -->
        <div class="flex justify-between items-center mt-4 pt-3" style="border-top: 1px solid var(--color-border); font-size: 0.85rem;">
          <span style="color: var(--color-text-light);">Page {{ productPage }} sur {{ totalProductPages }} ({{ filteredProducts.length }} produits)</span>
          <div class="flex gap-2">
            <button @click="productPage = Math.max(1, productPage - 1)" :disabled="productPage === 1" class="btn btn-secondary" style="padding: 4px 12px; font-size: 0.8rem;">
              ← Précédent
            </button>
            <button @click="productPage = Math.min(totalProductPages, productPage + 1)" :disabled="productPage === totalProductPages" class="btn btn-secondary" style="padding: 4px 12px; font-size: 0.8rem;">
              Suivant →
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Édition Produit -->
    <div v-if="editingProduct" style="position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px;">
      <div class="glass" style="padding: 32px; border-radius: 20px; width: 100%; max-width: 520px; position: relative; background: var(--color-bg-card);">
        <button @click="editingProduct = null" style="position: absolute; right: 16px; top: 16px; background: none; border: none; cursor: pointer; color: var(--color-text-light); font-size: 1.2rem;">✕</button>
        <h3 class="mb-4" style="font-size: 1.2rem; font-weight: 700;">✏️ Modifier le Produit #{{ editingProduct.id }}</h3>
        <form @submit.prevent="updateProduct" class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Titre</label>
            <input v-model="editingProduct.title" type="text" required class="form-input" />
          </div>
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Prix (FCFA)</label>
            <input v-model="editingProduct.price" type="number" step="0.01" required class="form-input" />
          </div>
          <div>
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Type</label>
            <select v-model="editingProduct.type" class="form-input">
              <option value="Pack">Pack</option>
              <option value="Template">Template</option>
              <option value="E-book">E-book</option>
              <option value="Guide">Guide</option>
              <option value="Outil">Outil</option>
            </select>
          </div>
          <div style="grid-column: span 2;">
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Description</label>
            <textarea v-model="editingProduct.description" required class="form-input" rows="3"></textarea>
          </div>
          <div class="flex gap-2" style="grid-column: span 2;">
            <button type="submit" class="btn btn-primary flex-1" style="padding: 10px;">Enregistrer</button>
            <button type="button" @click="editingProduct = null" class="btn btn-secondary" style="padding: 10px;">Annuler</button>
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
const products = ref([]);
const searchProductQuery = ref('');
const showForm = ref(false);
const newProduct = ref({ title: '', price: '', type: 'Pack', description: '', category_id: 1 });
const editingProduct = ref(null);

const productPage = ref(1);
const productPerPage = 5;

const emit = defineEmits(['count-updated', 'data-loaded']);

const filteredProducts = computed(() => {
  if (!searchProductQuery.value.trim()) return products.value;
  const q = searchProductQuery.value.toLowerCase();
  return products.value.filter(p => p.title?.toLowerCase().includes(q) || p.type?.toLowerCase().includes(q));
});

const paginatedProducts = computed(() => {
  const start = (productPage.value - 1) * productPerPage;
  return filteredProducts.value.slice(start, start + productPerPage);
});

const totalProductPages = computed(() => Math.ceil(filteredProducts.value.length / productPerPage) || 1);

const loadProducts = async () => {
  try {
    const res = await api.get('/products');
    products.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
    emit('count-updated', products.value.length);
    emit('data-loaded', products.value); // Pour vue d'ensemble (Dashboard)
  } catch(e) { 
    products.value = []; 
  }
};

const createProduct = async () => {
  try {
    await api.post('/products', newProduct.value);
    showForm.value = false;
    newProduct.value = { title: '', price: '', type: 'Pack', description: '', category_id: 1 };
    toastStore.showToast('Nouveau produit créé et publié avec succès !', 'success');
    await loadProducts();
  } catch(e) { 
    toastStore.showToast('Erreur lors de la création du produit.', 'error'); 
  }
};

const startEditProduct = (product) => { editingProduct.value = { ...product }; };

const updateProduct = async () => {
  if (!editingProduct.value) return;
  try {
    await api.put(`/products/${editingProduct.value.id}`, editingProduct.value);
    editingProduct.value = null;
    toastStore.showToast('Produit mis à jour avec succès !', 'success');
    await loadProducts();
  } catch(e) { 
    toastStore.showToast('Erreur lors de la modification du produit.', 'error'); 
  }
};

const deleteProduct = async (id) => {
  if (confirm('Supprimer définitivement ce produit ?')) {
    try {
      await api.delete(`/products/${id}`);
      toastStore.showToast('Produit supprimé du catalogue.', 'info');
      await loadProducts();
    } catch(e) { 
      toastStore.showToast('Erreur lors de la suppression.', 'error'); 
    }
  }
};

const exportProductsCSV = () => {
  if (products.value.length === 0) {
    toastStore.showToast('Aucun produit à exporter.', 'info');
    return;
  }
  const headers = ['ID', 'Titre', 'Type', 'Prix FCFA', 'Date'];
  const rows = products.value.map(p => [p.id, `"${p.title.replace(/"/g, '""')}"`, p.type, p.price, p.created_at || '']);
  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `catalogue_produits_${new Date().toISOString().slice(0,10)}.csv`);
  document.body.appendChild(link);
  link.click();
  link.remove();
  toastStore.showToast('Export CSV du catalogue produits téléchargé !', 'success');
};

onMounted(() => {
  loadProducts();
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
