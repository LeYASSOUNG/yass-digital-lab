<template>
  <div class="admin-dashboard">
    <!-- Admin Header & Tabs Banner Card -->
    <div class="glass mb-8" style="padding: 28px 32px 12px; border-radius: 24px; background: rgba(5, 8, 17, 0.88); backdrop-filter: blur(20px); border: 1px solid var(--color-accent); box-shadow: 0 15px 45px rgba(0,0,0,0.5); margin-top: 24px; color: #FFFFFF;">
      
      <!-- Header Top Row -->
      <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 16px;">
        <div class="flex items-center gap-4">
          <div style="width: 58px; height: 58px; border-radius: 50%; overflow: hidden; border: 2px solid var(--color-accent); flex-shrink: 0; box-shadow: 0 4px 14px rgba(212,175,55,0.4);">
            <img v-if="auth.user?.avatar" :src="auth.user.avatar" alt="Admin Avatar" style="width: 100%; height: 100%; object-fit: cover;" />
            <div v-else style="width: 100%; height: 100%; background: var(--color-accent); color: #050811; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.4rem;">
              {{ auth.user?.name ? auth.user.name.charAt(0).toUpperCase() : 'A' }}
            </div>
          </div>
          <div>
            <div class="flex items-center gap-3 mb-1" style="flex-wrap: wrap;">
              <h1 style="font-size: 1.9rem; margin: 0; font-weight: 800; color: #FFFFFF;">Tableau de bord</h1>
              <span v-if="auth.user?.role === 'super_admin'" style="background: linear-gradient(135deg, #F0CC55, #D4AF37); color: #050811; padding: 4px 14px; border-radius: 999px; font-weight: 800; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 10px rgba(212,175,55,0.4);">
                <Crown :size="14" /> SUPER ADMIN
              </span>
              <span v-else-if="auth.user?.role === 'admin'" style="background: rgba(59,130,246,0.25); color: #60a5fa; border: 1px solid #3b82f6; padding: 4px 14px; border-radius: 999px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                <Shield :size="14" /> ADMIN
              </span>
              <span v-else-if="auth.user?.role === 'creator'" style="background: rgba(16,185,129,0.25); color: #34d399; border: 1px solid #10b981; padding: 4px 14px; border-radius: 999px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                <Wrench :size="14" /> CRÉATEUR
              </span>
              <span v-else-if="auth.user?.role === 'editor'" style="background: rgba(168,85,247,0.25); color: #c084fc; border: 1px solid #a855f7; padding: 4px 14px; border-radius: 999px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                <Feather :size="14" /> RÉDACTEUR
              </span>
              <span v-else-if="auth.user?.role === 'support'" style="background: rgba(236,72,153,0.25); color: #f472b6; border: 1px solid #ec4899; padding: 4px 14px; border-radius: 999px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                <Headphones :size="14" /> SUPPORT
              </span>
            </div>
            <p style="color: rgba(255,255,255,0.85); margin: 0; font-size: 0.95rem;">Bienvenue, {{ auth.user?.name || 'Administrateur' }} 👋</p>
          </div>
        </div>
        <div class="flex gap-2">
          <button @click="generatePDFReport" class="btn btn-secondary flex items-center gap-2" style="font-size: 0.9rem; background: rgba(255,255,255,0.95); color: #050811; font-weight: 700;">
            <FileDown :size="16" /> Rapport Financier (PDF)
          </button>
          <button @click="handleLogout" class="btn btn-secondary flex items-center gap-2" style="font-size: 0.9rem; background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: #FFF;">
            <LogOut :size="16" /> Déconnexion
          </button>
        </div>
      </div>

      <!-- Onglets de navigation -->
      <div class="flex gap-2" style="border-top: 1px solid rgba(212,175,55,0.25); padding-top: 12px; flex-wrap: wrap;">
        <button v-for="tab in visibleTabs" :key="tab.id" @click="activeTab = tab.id"
          :style="activeTab === tab.id ? 'background: rgba(212,175,55,0.2); color: #F0CC55; border: 1px solid var(--color-accent); font-weight: 800;' : 'color: rgba(255,255,255,0.85); border: 1px solid transparent; background: rgba(255,255,255,0.05);'"
          style="padding: 9px 18px; border-radius: 12px; cursor: pointer; font-size: 0.9rem; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px;">
          <component :is="tab.iconComponent" :size="16" /> {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- TAB 1 : VUE D'ENSEMBLE -->
    <div v-if="activeTab === 'overview'">
      <div class="grid mb-8" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
        <div class="glass" style="padding: 24px; border-radius: 16px; border-left: 4px solid var(--color-accent);">
          <div class="flex justify-between items-center mb-2">
            <p style="color: var(--color-text-light); font-size: 0.9rem; margin: 0;">Ventes Totales</p>
            <DollarSign :size="20" style="color: var(--color-accent);" />
          </div>
          <h2 style="color: var(--color-accent); font-size: 2.2rem; font-weight: 800;">{{ totalRevenue.toFixed(2) }} €</h2>
        </div>
        <div class="glass" style="padding: 24px; border-radius: 16px; border-left: 4px solid #3b82f6;">
          <div class="flex justify-between items-center mb-2">
            <p style="color: var(--color-text-light); font-size: 0.9rem; margin: 0;">Commandes</p>
            <ShoppingBag :size="20" style="color: #3b82f6;" />
          </div>
          <h2 style="color: #3b82f6; font-size: 2.2rem; font-weight: 800;">{{ orders.length }}</h2>
        </div>
        <div class="glass" style="padding: 24px; border-radius: 16px; border-left: 4px solid #22c55e;">
          <div class="flex justify-between items-center mb-2">
            <p style="color: var(--color-text-light); font-size: 0.9rem; margin: 0;">Produits Actifs</p>
            <Package :size="20" style="color: #22c55e;" />
          </div>
          <h2 style="color: #22c55e; font-size: 2.2rem; font-weight: 800;">{{ products.length }}</h2>
        </div>
        <div class="glass" style="padding: 24px; border-radius: 16px; border-left: 4px solid #a855f7;">
          <div class="flex justify-between items-center mb-2">
            <p style="color: var(--color-text-light); font-size: 0.9rem; margin: 0;">Abonnés Newsletter</p>
            <Mail :size="20" style="color: #a855f7;" />
          </div>
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
          <h3 class="flex items-center gap-2"><ShoppingBag :size="20" style="color: var(--color-accent);" /> Dernières Commandes</h3>
          <button @click="exportOrdersCSV" class="btn btn-secondary flex items-center gap-2" style="padding: 6px 14px; font-size: 0.85rem;">
            <Download :size="14" /> Exporter CSV (Ventes)
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
                <span :style="order.status === 'paid' ? 'background: rgba(34,197,94,0.15); color: #22c55e;' : 'background: rgba(234,179,8,0.15); color: #eab308;'" style="padding: 3px 10px; border-radius: 999px; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                  <CheckCircle2 v-if="order.status === 'paid'" :size="12" />
                  <Clock v-else :size="12" />
                  {{ order.status === 'paid' ? 'Payée' : 'En attente' }}
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
              <td style="padding: 12px; font-weight: 600;">
                <div class="flex items-center gap-3">
                  <div style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; border: 1px solid var(--color-accent); flex-shrink: 0; background: var(--color-bg);">
                    <img v-if="u.avatar" :src="u.avatar" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;" />
                    <div v-else style="width: 100%; height: 100%; background: var(--color-accent); color: #050811; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">
                      {{ u.name ? u.name.charAt(0).toUpperCase() : 'U' }}
                    </div>
                  </div>
                  <span>{{ u.name }}</span>
                </div>
              </td>
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
              <td style="padding: 12px; display: flex; gap: 8px;">
                <button @click="startEditProduct(product)" style="background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid #3b82f6; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                  ✏️ Modifier
                </button>
                <button @click="deleteProduct(product.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                  <Trash2 :size="14" /> Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Édition Produit -->
    <div v-if="editingProduct" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px;">
      <div class="glass" style="padding: 32px; border-radius: 20px; width: 100%; max-width: 500px; position: relative;">
        <button @click="editingProduct = null" style="position: absolute; right: 16px; top: 16px; background: none; border: none; cursor: pointer; color: var(--color-text-light); font-size: 1.2rem;">✕</button>
        <h3 class="mb-4">✏️ Modifier le Produit #{{ editingProduct.id }}</h3>
        <form @submit.prevent="updateProduct" class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label class="form-label">Titre</label>
            <input v-model="editingProduct.title" type="text" required class="form-input" />
          </div>
          <div>
            <label class="form-label">Prix (€)</label>
            <input v-model="editingProduct.price" type="number" step="0.01" required class="form-input" />
          </div>
          <div>
            <label class="form-label">Type</label>
            <select v-model="editingProduct.type" class="form-input">
              <option value="Pack">Pack</option>
              <option value="Template">Template</option>
              <option value="E-book">E-book</option>
              <option value="Guide">Guide</option>
              <option value="Outil">Outil</option>
            </select>
          </div>
          <div>
            <label class="form-label">Catégorie ID</label>
            <input v-model="editingProduct.category_id" type="number" required class="form-input" />
          </div>
          <div style="grid-column: span 2;">
            <label class="form-label">Description</label>
            <textarea v-model="editingProduct.description" required class="form-input" rows="3"></textarea>
          </div>
          <button type="submit" class="btn btn-primary" style="grid-column: span 2;">💾 Enregistrer les modifications</button>
        </form>
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
              <th style="padding: 12px; text-align: left;">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="post in posts" :key="post.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ post.id }}</td>
              <td style="padding: 12px; font-weight: 600;">{{ post.title }}</td>
              <td style="padding: 12px; font-size: 0.85rem; color: var(--color-accent);">{{ post.slug }}</td>
              <td style="padding: 12px; display: flex; gap: 8px;">
                <button @click="startEditPost(post)" style="background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid #3b82f6; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                  ✏️ Modifier
                </button>
                <button @click="deletePost(post.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                  <Trash2 :size="14" /> Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Édition Article Blog -->
    <div v-if="editingPost" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px;">
      <div class="glass" style="padding: 32px; border-radius: 20px; width: 100%; max-width: 550px; position: relative;">
        <button @click="editingPost = null" style="position: absolute; right: 16px; top: 16px; background: none; border: none; cursor: pointer; color: var(--color-text-light); font-size: 1.2rem;">✕</button>
        <h3 class="mb-4">✏️ Modifier l'Article #{{ editingPost.id }}</h3>
        <form @submit.prevent="updatePost" class="flex flex-col gap-4">
          <div>
            <label class="form-label">Titre</label>
            <input v-model="editingPost.title" type="text" required class="form-input" />
          </div>
          <div>
            <label class="form-label">Contenu</label>
            <textarea v-model="editingPost.content" required class="form-input" rows="6"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">💾 Enregistrer l'article</button>
        </form>
      </div>
    </div>

    <!-- TAB 5 : COUPONS -->
    <div v-if="activeTab === 'coupons'">
      <div class="glass" style="padding: 28px; border-radius: 16px;">
        <div class="flex justify-between items-center mb-6">
          <h3 class="flex items-center gap-2"><Ticket :size="20" style="color: var(--color-accent);" /> Gestion des Coupons</h3>
          <button @click="showCouponForm = !showCouponForm" class="btn btn-primary flex items-center gap-2">
            <Plus v-if="!showCouponForm" :size="16" />
            <span>{{ showCouponForm ? '✕ Annuler' : 'Ajouter un Coupon' }}</span>
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
            <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="grid-column: span 2;">
              <Plus :size="16" /> Créer le coupon
            </button>
          </form>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">Code</th>
              <th style="padding: 12px; text-align: left;">Réduction €</th>
              <th style="padding: 12px; text-align: left;">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in coupons" :key="c.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; font-weight: 700; color: var(--color-accent);">{{ c.code }}</td>
              <td style="padding: 12px;">{{ c.discount_amount ? c.discount_amount + ' €' : '-' }}</td>
              <td style="padding: 12px; display: flex; gap: 8px;">
                <button @click="startEditCoupon(c)" style="background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid #3b82f6; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                  ✏️ Modifier
                </button>
                <button @click="deleteCoupon(c.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                  <Trash2 :size="14" /> Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Édition Coupon -->
    <div v-if="editingCoupon" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px;">
      <div class="glass" style="padding: 32px; border-radius: 20px; width: 100%; max-width: 450px; position: relative;">
        <button @click="editingCoupon = null" style="position: absolute; right: 16px; top: 16px; background: none; border: none; cursor: pointer; color: var(--color-text-light); font-size: 1.2rem;">✕</button>
        <h3 class="mb-4">✏️ Modifier le Coupon #{{ editingCoupon.id }}</h3>
        <form @submit.prevent="updateCoupon" class="flex flex-col gap-4">
          <div>
            <label class="form-label">Code Promo</label>
            <input v-model="editingCoupon.code" type="text" required class="form-input" />
          </div>
          <div>
            <label class="form-label">Réduction (€)</label>
            <input v-model="editingCoupon.discount_amount" type="number" step="0.01" class="form-input" />
          </div>
          <button type="submit" class="btn btn-primary">💾 Enregistrer le coupon</button>
        </form>
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

    <!-- TAB 7 : DEMANDES DE DEVIS -->
    <div v-if="activeTab === 'quotes'">
      <div class="glass" style="padding: 28px; border-radius: 16px;">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 12px;">
          <div>
            <h3>📋 Demandes de Devis (Services)</h3>
            <p style="color: var(--color-text-light); font-size: 0.85rem;">Consultez et gérez toutes les demandes de devis soumises par les prospects.</p>
          </div>
          <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 0.85rem;">
            {{ quoteRequests.length }} demande(s) au total
          </span>
        </div>

        <div v-if="quoteRequests.length === 0" class="text-center" style="padding: 40px; color: var(--color-text-light);">
          Aucune demande de devis enregistrée pour le moment.
        </div>

        <table v-else style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Client</th>
              <th style="padding: 12px; text-align: left;">Email</th>
              <th style="padding: 12px; text-align: left;">Service Sollicité</th>
              <th style="padding: 12px; text-align: left;">Détails du Projet</th>
              <th style="padding: 12px; text-align: left;">Statut</th>
              <th style="padding: 12px; text-align: left;">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="q in quoteRequests" :key="q.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ q.id }}</td>
              <td style="padding: 12px; font-weight: 600;">{{ q.name }}</td>
              <td style="padding: 12px;">
                <a :href="'mailto:' + q.email" style="color: var(--color-accent); text-decoration: underline;">{{ q.email }}</a>
              </td>
              <td style="padding: 12px; font-weight: 600; color: var(--color-text);">{{ q.service_title }}</td>
              <td style="padding: 12px; max-width: 250px; font-size: 0.85rem; color: var(--color-text-light);">
                {{ q.details || 'Aucun détail précisé' }}
              </td>
              <td style="padding: 12px;">
                <select :value="q.status" @change="updateQuoteStatus(q.id, $event.target.value)" style="padding: 4px 8px; border-radius: 6px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.8rem;">
                  <option value="pending">⏳ En attente</option>
                  <option value="contacted">📩 Prospect contacté</option>
                  <option value="completed">✅ Terminé / Validé</option>
                </select>
              </td>
              <td style="padding: 12px;">
                <button @click="deleteQuoteRequest(q.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                  <Trash2 :size="13" /> Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 8 : MODERATION DES AVIS & COMMENTAIRES -->
    <div v-if="activeTab === 'reviews'">
      <div class="glass" style="padding: 28px; border-radius: 16px;">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 12px;">
          <div>
            <h3>⭐ Avis & Modération des Commentaires</h3>
            <p style="color: var(--color-text-light); font-size: 0.85rem;">Consultez et modérez les avis laissés par les utilisateurs sur vos produits.</p>
          </div>
          <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 0.85rem;">
            {{ reviewsList.length }} commentaire(s) au total
          </span>
        </div>

        <div v-if="reviewsList.length === 0" class="text-center" style="padding: 40px; color: var(--color-text-light);">
          Aucun commentaire pour le moment.
        </div>

        <table v-else style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Auteur</th>
              <th style="padding: 12px; text-align: left;">Produit Concerné</th>
              <th style="padding: 12px; text-align: left;">Note</th>
              <th style="padding: 12px; text-align: left;">Commentaire</th>
              <th style="padding: 12px; text-align: left;">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="rev in reviewsList" :key="rev.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ rev.id }}</td>
              <td style="padding: 12px; font-weight: 600;">{{ rev.name }}</td>
              <td style="padding: 12px; font-weight: 600; color: var(--color-text);">
                {{ rev.product?.title || 'Produit #' + rev.product_id }}
              </td>
              <td style="padding: 12px; color: var(--color-accent); font-weight: 800;">
                {{ '★'.repeat(rev.rating) }}
              </td>
              <td style="padding: 12px; max-width: 300px; font-size: 0.85rem; color: var(--color-text-light);">
                "{{ rev.comment }}"
              </td>
              <td style="padding: 12px;">
                <button @click="deleteReview(rev.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                  <Trash2 :size="13" /> Supprimer
                </button>
              </td>
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
import api from '../api';

const auth = useAuthStore();
const router = useRouter();

const activeTab = ref('overview');

const baseTabs = [
  { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
  { id: 'products', icon: '🛍️', label: 'Produits' },
  { id: 'quotes', icon: '📋', label: 'Demandes de Devis' },
  { id: 'reviews', icon: '⭐', label: 'Modération Avis' },
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
      { id: 'quotes', icon: '📋', label: 'Demandes de Devis' },
      { id: 'reviews', icon: '⭐', label: 'Modération Avis' },
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
      { id: 'quotes', icon: '📋', label: 'Demandes de Devis' },
      { id: 'reviews', icon: '⭐', label: 'Avis Produits' },
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
      { id: 'quotes', icon: '📋', label: 'Demandes de Devis' },
      { id: 'reviews', icon: '⭐', label: 'Modération Avis' },
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
const quoteRequests = ref([]);
const reviewsList = ref([]);

const editingProduct = ref(null);
const editingPost = ref(null);
const editingCoupon = ref(null);

const showForm = ref(false);
const showPostForm = ref(false);
const showCouponForm = ref(false);

const newProduct = ref({ title: '', price: '', type: 'Pack', description: '', category_id: 1 });
const newPost = ref({ title: '', content: '', is_published: true });
const newCoupon = ref({ code: '', discount_amount: '', discount_percentage: '', expires_at: '' });

const totalRevenue = computed(() => orders.value.reduce((sum, o) => sum + parseFloat(o.total_amount || 0), 0));
const authHeaders = computed(() => ({ headers: { Authorization: `Bearer ${auth.token}` } }));

const startEditProduct = (product) => {
  editingProduct.value = { ...product };
};

const updateProduct = async () => {
  if (!editingProduct.value) return;
  try {
    await api.put(`/products/${editingProduct.value.id}`, editingProduct.value);
    editingProduct.value = null;
    await loadProducts();
  } catch(e) { alert('Erreur lors de la modification du produit'); }
};

const startEditPost = (post) => {
  editingPost.value = { ...post };
};

const updatePost = async () => {
  if (!editingPost.value) return;
  try {
    await api.put(`/posts/${editingPost.value.id}`, editingPost.value);
    editingPost.value = null;
    await loadPosts();
  } catch(e) { alert('Erreur lors de la modification de l\'article'); }
};

const startEditCoupon = (coupon) => {
  editingCoupon.value = { ...coupon };
};

const updateCoupon = async () => {
  if (!editingCoupon.value) return;
  try {
    await api.put(`/coupons/${editingCoupon.value.id}`, editingCoupon.value);
    editingCoupon.value = null;
    await loadCoupons();
  } catch(e) { alert('Erreur lors de la modification du coupon'); }
};

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
  loadQuoteRequests();
  loadReviews();
  if (auth.user?.role === 'super_admin') {
    loadUsers();
  }
});

const loadReviews = async () => {
  try {
    const res = await api.get('/reviews');
    reviewsList.value = res.data;
  } catch(e) { reviewsList.value = []; }
};

const deleteReview = async (id) => {
  if (confirm('Supprimer ce commentaire / avis ?')) {
    try {
      await api.delete(`/reviews/${id}`);
      await loadReviews();
    } catch(e) { alert('Erreur lors de la suppression du commentaire.'); }
  }
};

const loadQuoteRequests = async () => {
  try {
    const res = await api.get('/quote-requests');
    quoteRequests.value = res.data;
  } catch(e) { quoteRequests.value = []; }
};

const updateQuoteStatus = async (id, status) => {
  try {
    await api.put(`/quote-requests/${id}/status`, { status });
    await loadQuoteRequests();
  } catch(e) { alert('Erreur lors de la mise à jour du statut'); }
};

const deleteQuoteRequest = async (id) => {
  if (confirm('Supprimer cette demande de devis ?')) {
    try {
      await api.delete(`/quote-requests/${id}`);
      await loadQuoteRequests();
    } catch(e) { alert('Erreur lors de la suppression'); }
  }
};

const loadProducts = async () => {
  const res = await api.get('/products');
  products.value = res.data;
};
const loadOrders = async () => {
  try {
    const res = await api.get('/orders');
    orders.value = res.data;
  } catch(e) { orders.value = []; }
};
const loadPosts = async () => {
  try {
    const res = await api.get('/posts');
    posts.value = res.data;
  } catch(e) { posts.value = []; }
};
const loadSubscribers = async () => {
  try {
    const res = await api.get('/newsletter');
    subscribers.value = res.data;
  } catch(e) { subscribers.value = []; }
};
const loadCoupons = async () => {
  try {
    const res = await api.get('/coupons');
    coupons.value = res.data;
  } catch(e) { coupons.value = []; }
};
const loadUsers = async () => {
  try {
    const res = await api.get('/users');
    usersList.value = res.data;
  } catch(e) { 
    console.error('Erreur chargement utilisateurs', e);
    usersList.value = []; 
  }
};

const changeRole = async (userId, newRole) => {
  try {
    const res = await api.put(`/users/${userId}/role`, { role: newRole });
    alert(res.data.message);
    await loadUsers();
  } catch(e) {
    alert(e.response?.data?.message || 'Erreur lors de la modification du rôle.');
  }
};

const deleteUser = async (userId) => {
  if (confirm('Supprimer définitivement cet utilisateur ?')) {
    try {
      const res = await api.delete(`/users/${userId}`);
      alert(res.data.message);
      await loadUsers();
    } catch(e) {
      alert(e.response?.data?.message || 'Erreur lors de la suppression.');
    }
  }
};

const createProduct = async () => {
  try {
    await api.post('/products', newProduct.value);
    showForm.value = false;
    newProduct.value = { title: '', price: '', type: 'Pack', description: '', category_id: 1 };
    await loadProducts();
  } catch(e) { alert('Erreur lors de la création du produit'); }
};

const createPost = async () => {
  try {
    await api.post('/posts', newPost.value);
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
    await api.delete(`/products/${id}`);
    await loadProducts();
  }
};

const deletePost = async (id) => {
  if (confirm('Supprimer cet article ?')) {
    try {
      await api.delete(`/posts/${id}`);
      await loadPosts();
    } catch(e) { alert('Erreur lors de la suppression de l\'article.'); }
  }
};

const deleteCoupon = async (id) => {
  if (confirm('Supprimer ce coupon ?')) {
    try {
      await api.delete(`/coupons/${id}`);
      await loadCoupons();
    } catch(e) { alert('Erreur lors de la suppression du coupon.'); }
  }
};

const generatePDFReport = () => {
  const dateStr = new Date().toLocaleDateString('fr-FR');
  const reportHTML = `<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Rapport Financier & d'Activité — Yass Digital Lab</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #0F172A; padding: 40px; background: #F8FAFC; }
    .box { background: #FFFFFF; border: 2px solid #D4AF37; padding: 36px; border-radius: 16px; max-width: 800px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .header { display: flex; justify-content: space-between; border-bottom: 2px solid #E2E8F0; padding-bottom: 20px; margin-bottom: 24px; }
    .title { font-size: 26px; font-weight: 800; color: #0F172A; }
    .accent { color: #D4AF37; }
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin: 24px 0; }
    .kpi-card { background: #F8FAFC; padding: 16px; border-radius: 10px; border: 1px solid #E2E8F0; text-align: center; }
    .kpi-value { font-size: 20px; font-weight: 800; color: #D4AF37; }
    table { width: 100%; border-collapse: collapse; margin: 24px 0; }
    th { background: #0F172A; color: #FFF; padding: 12px; text-align: left; font-size: 13px; }
    td { padding: 12px; border-bottom: 1px solid #E2E8F0; font-size: 13px; }
  </style>
</head>
<body>
  <div class="box">
    <div class="header">
      <div>
        <div class="title">YASS<span class="accent">DIGITAL</span>LAB</div>
        <p style="margin: 4px 0 0; color: #64748B; font-size: 13px;">Rapport d'Activité & Bilan Financier Officiel</p>
      </div>
      <div style="text-align: right;">
        <h2 style="color: #D4AF37; margin:0; font-size: 18px;">BILAN FINANCIER</h2>
        <p style="margin: 4px 0 0; font-size: 13px;">Généré le : ${dateStr}</p>
      </div>
    </div>

    <div class="kpi-grid">
      <div class="kpi-card">
        <div style="font-size: 11px; color: #64748B;">Revenu Total</div>
        <div class="kpi-value">${totalRevenue.value.toFixed(2)} €</div>
      </div>
      <div class="kpi-card">
        <div style="font-size: 11px; color: #64748B;">Commandes</div>
        <div class="kpi-value">${orders.value.length}</div>
      </div>
      <div class="kpi-card">
        <div style="font-size: 11px; color: #64748B;">Produits Actifs</div>
        <div class="kpi-value">${products.value.length}</div>
      </div>
      <div class="kpi-card">
        <div style="font-size: 11px; color: #64748B;">Abonnés</div>
        <div class="kpi-value">${subscribers.value.length}</div>
      </div>
    </div>

    <h3>Détail des Dernières Ventes Enregistrées</h3>
    <table>
      <thead>
        <tr>
          <th>ID Commande</th>
          <th>Client (Email)</th>
          <th>Montant Net</th>
          <th>Statut Règlement</th>
        </tr>
      </thead>
      <tbody>
        ${orders.value.map(o => `
          <tr>
            <td>#${o.id}</td>
            <td>${o.email}</td>
            <td style="font-weight: bold; color: #D4AF37;">${o.total_amount} €</td>
            <td style="color: #10B981; font-weight: bold;">${o.status === 'paid' ? 'Payée' : o.status}</td>
          </tr>
        `).join('') || '<tr><td colspan="4" style="text-align: center;">Aucune donnée enregistrée</td></tr>'}
      </tbody>
    </table>

    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #64748B; border-top: 1px solid #E2E8F0; padding-top: 16px;">
      Document confidentiel d'administration — Yass Digital Lab (https://portfolio-tau-inky-96i2vyeddb.vercel.app/)
    </div>
  </div>
</body>
</html>`;

  const blob = new Blob([reportHTML], { type: 'text/html;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `Rapport_Financier_YassDigitalLab_${dateStr.replace(/\//g, '-')}.html`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
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
