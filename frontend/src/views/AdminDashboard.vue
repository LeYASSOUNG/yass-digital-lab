<template>
  <div class="admin-dashboard container" style="margin-top: 24px; margin-bottom: 50px;">
    
    <!-- Admin Header & Tabs Banner Card -->
    <div class="glass mb-8" style="padding: 28px 32px 14px; border-radius: 24px; background: rgba(5, 8, 17, 0.92); backdrop-filter: blur(20px); border: 1px solid var(--color-accent); box-shadow: 0 15px 45px rgba(0,0,0,0.5); color: #FFFFFF;">
      
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
              <h1 style="font-size: 1.8rem; margin: 0; font-weight: 800; color: #FFFFFF; font-family: var(--font-heading);">Tableau de bord Admin</h1>
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
            <p style="color: rgba(255,255,255,0.85); margin: 0; font-size: 0.92rem;">Bienvenue, {{ auth.user?.name || 'Administrateur' }} 👋 — Espace de gestion centralisé</p>
          </div>
        </div>
        <div class="flex gap-2" style="flex-wrap: wrap;">
          <button @click="generatePDFReport" class="btn btn-secondary flex items-center gap-2" style="font-size: 0.85rem; background: rgba(255,255,255,0.95); color: #050811; font-weight: 700;">
            <FileDown :size="15" /> Rapport Financier (PDF)
          </button>
          <button @click="handleLogout" class="btn btn-secondary flex items-center gap-2" style="font-size: 0.85rem; background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: #FFF;">
            <LogOut :size="15" /> Déconnexion
          </button>
        </div>
      </div>

      <!-- Onglets de navigation avec badges dynamiques -->
      <div class="flex gap-2" style="border-top: 1px solid rgba(212,175,55,0.25); padding-top: 14px; flex-wrap: wrap;">
        <button v-for="tab in visibleTabs" :key="tab.id" @click="activeTab = tab.id"
          :style="activeTab === tab.id ? 'background: rgba(212,175,55,0.22); color: #F0CC55; border: 1px solid var(--color-accent); font-weight: 800;' : 'color: rgba(255,255,255,0.85); border: 1px solid transparent; background: rgba(255,255,255,0.05);'"
          style="padding: 8px 16px; border-radius: 12px; cursor: pointer; font-size: 0.88rem; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px;">
          <span>{{ tab.icon }}</span> 
          <span>{{ tab.label }}</span>
          <span v-if="tabCounts[tab.id] !== null && tabCounts[tab.id] !== undefined" style="background: rgba(212,175,55,0.25); color: #F0CC55; padding: 1px 7px; border-radius: 999px; font-size: 0.72rem; font-weight: 800; border: 1px solid rgba(212,175,55,0.3);">
            {{ tabCounts[tab.id] }}
          </span>
        </button>
      </div>
    </div>

    <!-- TAB 1 : VUE D'ENSEMBLE -->
    <div v-if="activeTab === 'overview'" class="fade-in">
      <!-- Cartes KPIs avec Tendances -->
      <div class="grid mb-8" style="grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 20px;">
        
        <!-- KPI 1 : Revenu Total -->
        <div class="glass" style="padding: 24px 26px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid var(--color-accent); background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
          <div class="flex justify-between items-center mb-3">
            <span style="color: var(--color-text-light); font-size: 0.88rem; font-weight: 700;">Revenu Total</span>
            <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(212,175,55,0.15); border: 1px solid rgba(212,175,55,0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <DollarSign :size="20" style="color: var(--color-accent);" />
            </div>
          </div>
          <h2 style="color: var(--color-text); font-size: 2.1rem; font-weight: 800; margin: 4px 0 10px; font-family: var(--font-heading);">{{ totalRevenue.toFixed(2) }} €</h2>
          <div>
            <span style="font-size: 0.76rem; color: #10b981; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25); padding: 4px 10px; border-radius: 999px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
              <TrendingUp :size="13" /> +15.4% vs mois dernier
            </span>
          </div>
        </div>

        <!-- KPI 2 : Commandes -->
        <div class="glass" style="padding: 24px 26px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #3b82f6; background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
          <div class="flex justify-between items-center mb-3">
            <span style="color: var(--color-text-light); font-size: 0.88rem; font-weight: 700;">Commandes</span>
            <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <ShoppingBag :size="20" style="color: #3b82f6;" />
            </div>
          </div>
          <h2 style="color: var(--color-text); font-size: 2.1rem; font-weight: 800; margin: 4px 0 10px; font-family: var(--font-heading);">{{ orders.length }}</h2>
          <div>
            <span style="font-size: 0.76rem; color: #3b82f6; background: rgba(59,130,246,0.12); border: 1px solid rgba(59,130,246,0.25); padding: 4px 10px; border-radius: 999px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
              <CheckCircle2 :size="13" /> {{ orders.filter(o => o.status === 'paid').length }} payée(s)
            </span>
          </div>
        </div>

        <!-- KPI 3 : Catalogue Produits -->
        <div class="glass" style="padding: 24px 26px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #10b981; background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
          <div class="flex justify-between items-center mb-3">
            <span style="color: var(--color-text-light); font-size: 0.88rem; font-weight: 700;">Catalogue Produits</span>
            <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <Package :size="20" style="color: #10b981;" />
            </div>
          </div>
          <h2 style="color: var(--color-text); font-size: 2.1rem; font-weight: 800; margin: 4px 0 10px; font-family: var(--font-heading);">{{ products.length }}</h2>
          <div>
            <span style="font-size: 0.76rem; color: #10b981; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25); padding: 4px 10px; border-radius: 999px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
              ⚡ Prêts au téléchargement
            </span>
          </div>
        </div>

        <!-- KPI 4 : Abonnés Newsletter -->
        <div class="glass" style="padding: 24px 26px; border-radius: 20px; border: 1px solid var(--color-border); border-top: 4px solid #a855f7; background: var(--color-bg-card); box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
          <div class="flex justify-between items-center mb-3">
            <span style="color: var(--color-text-light); font-size: 0.88rem; font-weight: 700;">Abonnés Newsletter</span>
            <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(168,85,247,0.15); border: 1px solid rgba(168,85,247,0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <Mail :size="20" style="color: #a855f7;" />
            </div>
          </div>
          <h2 style="color: var(--color-text); font-size: 2.1rem; font-weight: 800; margin: 4px 0 10px; font-family: var(--font-heading);">{{ subscribers.length }}</h2>
          <div>
            <span style="font-size: 0.76rem; color: #a855f7; background: rgba(168,85,247,0.12); border: 1px solid rgba(168,85,247,0.25); padding: 4px 10px; border-radius: 999px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
              📧 Audience active
            </span>
          </div>
        </div>

      </div>

      <!-- Graphique d'Analyse des Ventes -->
      <div class="mb-8">
        <SalesChart />
      </div>

      <!-- Dernières commandes avec filtre -->
      <div class="glass" style="padding: 28px; border-radius: 20px;">
        <div class="flex justify-between items-center mb-4" style="flex-wrap: wrap; gap: 12px;">
          <h3 class="flex items-center gap-2" style="font-size: 1.25rem; font-weight: 700; margin: 0;">
            <ShoppingBag :size="20" style="color: var(--color-accent);" /> Dernières Commandes Clients
          </h3>
          <div class="flex items-center gap-2">
            <div style="position: relative; width: 220px;">
              <Search :size="14" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);" />
              <input v-model="searchOrderQuery" type="text" placeholder="Filtrer commande/email..." style="width: 100%; padding: 6px 12px 6px 30px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
            </div>
            <button @click="exportOrdersCSV" class="btn btn-secondary flex items-center gap-2" style="padding: 6px 14px; font-size: 0.82rem;">
              <Download :size="14" /> Exporter CSV
            </button>
          </div>
        </div>

        <div v-if="filteredOrders.length === 0" style="color: var(--color-text-light); text-align: center; padding: 30px;">Aucune commande trouvée.</div>
        <div v-else style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
              <tr style="border-bottom: 2px solid var(--color-border);">
                <th style="padding: 12px; text-align: left;">ID</th>
                <th style="padding: 12px; text-align: left;">Email Client</th>
                <th style="padding: 12px; text-align: left;">Montant</th>
                <th style="padding: 12px; text-align: left;">Statut</th>
                <th style="padding: 12px; text-align: left;">Facture</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in filteredOrders.slice(0, 8)" :key="order.id" style="border-bottom: 1px solid var(--color-border);">
                <td style="padding: 12px; font-weight: 700; color: var(--color-text-light);">#{{ order.id }}</td>
                <td style="padding: 12px; font-weight: 600;">{{ order.email }}</td>
                <td style="padding: 12px; color: var(--color-accent); font-weight: 800;">{{ order.total_amount }} €</td>
                <td style="padding: 12px;">
                  <span :style="order.status === 'paid' ? 'background: rgba(34,197,94,0.15); color: #22c55e; border: 1px solid rgba(34,197,94,0.3);' : 'background: rgba(234,179,8,0.15); color: #eab308; border: 1px solid rgba(234,179,8,0.3);'" style="padding: 3px 10px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <CheckCircle2 v-if="order.status === 'paid'" :size="12" />
                    <Clock v-else :size="12" />
                    {{ order.status === 'paid' ? 'Payée' : 'En attente' }}
                  </span>
                </td>
                <td style="padding: 12px;">
                  <a :href="`/api/orders/${order.id}/invoice`" target="_blank" class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                    <Download :size="12" /> PDF
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 2 : GESTION DES UTILISATEURS & ROLES (SUPER ADMIN ONLY) -->
    <div v-if="activeTab === 'users' && auth.user?.role === 'super_admin'" class="fade-in">
      <div class="glass" style="padding: 28px; border-radius: 20px;">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 16px;">
          <div>
            <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">👑 Gestion des Utilisateurs & Rôles System</h3>
            <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Attribuez des rôles (Client, Admin, Créateur, Éditeur, Support, Super Admin) en temps réel.</p>
          </div>
          <div class="flex items-center gap-3">
            <div style="position: relative; width: 240px;">
              <Search :size="14" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);" />
              <input v-model="searchUserQuery" type="text" placeholder="Rechercher nom, email..." style="width: 100%; padding: 6px 12px 6px 30px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
            </div>
            <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 0.82rem; white-space: nowrap;">
              {{ filteredUsers.length }} utilisateur(s)
            </span>
          </div>
        </div>

        <div style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
            <thead>
              <tr style="border-bottom: 2px solid var(--color-border);">
                <th style="padding: 12px; text-align: left;">ID</th>
                <th style="padding: 12px; text-align: left;">Utilisateur</th>
                <th style="padding: 12px; text-align: left;">Email</th>
                <th style="padding: 12px; text-align: left;">Rôle Actuel</th>
                <th style="padding: 12px; text-align: left;">Attribuer un Rôle</th>
                <th style="padding: 12px; text-align: left;">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="u in filteredUsers" :key="u.id" style="border-bottom: 1px solid var(--color-border);">
                <td style="padding: 12px; color: var(--color-text-light);">#{{ u.id }}</td>
                <td style="padding: 12px; font-weight: 600;">
                  <div class="flex items-center gap-3">
                    <div style="width: 34px; height: 34px; border-radius: 50%; overflow: hidden; border: 1px solid var(--color-accent); flex-shrink: 0; background: var(--color-bg);">
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
                    🛠️ Créateur
                  </span>
                  <span v-else-if="u.role === 'editor'" style="background: rgba(168,85,247,0.15); color: #a855f7; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                    📝 Rédacteur
                  </span>
                  <span v-else-if="u.role === 'support'" style="background: rgba(236,72,153,0.15); color: #ec4899; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                    🎧 Support
                  </span>
                  <span v-else style="background: rgba(255,255,255,0.1); color: var(--color-text-light); padding: 3px 10px; border-radius: 999px; font-size: 0.75rem;">
                    👤 Client
                  </span>
                </td>
                <td style="padding: 12px;">
                  <select :value="u.role" @change="changeRole(u.id, $event.target.value)" style="padding: 5px 10px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.8rem; cursor: pointer;">
                    <option value="client">Client</option>
                    <option value="creator">Créateur</option>
                    <option value="editor">Rédacteur</option>
                    <option value="support">Support</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                  </select>
                </td>
                <td style="padding: 12px;">
                  <button v-if="u.id !== auth.user?.id" @click="deleteUser(u.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                    <Trash2 :size="13" /> Supprimer
                  </button>
                  <span v-else style="font-size: 0.78rem; color: var(--color-text-light); font-style: italic;">(Vous)</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 3 : PRODUITS -->
    <div v-if="activeTab === 'products'" class="fade-in">
      <div class="glass" style="padding: 28px; border-radius: 20px;">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 14px;">
          <div>
            <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">🛍️ Gestion des Produits & Ressources</h3>
            <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Gérez les packs, templates, e-books et outils au catalogue.</p>
          </div>
          <div class="flex items-center gap-3">
            <div style="position: relative; width: 220px;">
              <Search :size="14" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);" />
              <input v-model="searchProductQuery" type="text" placeholder="Rechercher produit..." style="width: 100%; padding: 6px 12px 6px 30px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
            </div>
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
              <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Prix (€) *</label>
              <input v-model="newProduct.price" type="number" step="0.01" required class="form-input" placeholder="19.99" />
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
              <tr v-for="product in filteredProducts" :key="product.id" style="border-bottom: 1px solid var(--color-border);">
                <td style="padding: 12px; color: var(--color-text-light);">#{{ product.id }}</td>
                <td style="padding: 12px; font-weight: 600;">{{ product.title }}</td>
                <td style="padding: 12px;"><span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 2px 9px; border-radius: 999px; font-size: 0.78rem; font-weight: 700;">{{ product.type }}</span></td>
                <td style="padding: 12px; color: var(--color-accent); font-weight: 800;">{{ product.price }} €</td>
                <td style="padding: 12px; display: flex; gap: 8px;">
                  <button @click="startEditProduct(product)" style="background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid #3b82f6; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                    ✏️ Modifier
                  </button>
                  <button @click="deleteProduct(product.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                    <Trash2 :size="13" /> Supprimer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
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
            <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Prix (€)</label>
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
            <button type="submit" class="btn btn-primary flex-1" style="padding: 10px;">💾 Enregistrer</button>
            <button type="button" @click="editingProduct = null" class="btn btn-secondary" style="padding: 10px;">Annuler</button>
          </div>
        </form>
      </div>
    </div>

    <!-- TAB 4 : DEMANDES DE DEVIS -->
    <div v-if="activeTab === 'quotes'" class="fade-in">
      <div class="glass" style="padding: 28px; border-radius: 20px;">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 14px;">
          <div>
            <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">📋 Demandes de Devis Client</h3>
            <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Consultez et répondez aux projets soumis par les utilisateurs.</p>
          </div>
          <div class="flex items-center gap-3">
            <div style="position: relative; width: 220px;">
              <Search :size="14" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);" />
              <input v-model="searchQuoteQuery" type="text" placeholder="Filtrer nom, service..." style="width: 100%; padding: 6px 12px 6px 30px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
            </div>
            <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 0.82rem;">
              {{ filteredQuoteRequests.length }} demande(s)
            </span>
          </div>
        </div>

        <div v-if="filteredQuoteRequests.length === 0" class="text-center" style="padding: 40px; color: var(--color-text-light);">
          Aucune demande de devis enregistrée.
        </div>

        <div v-else style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
            <thead>
              <tr style="border-bottom: 2px solid var(--color-border);">
                <th style="padding: 12px; text-align: left;">ID</th>
                <th style="padding: 12px; text-align: left;">Demandeur</th>
                <th style="padding: 12px; text-align: left;">Service Vise</th>
                <th style="padding: 12px; text-align: left;">Budget & Message</th>
                <th style="padding: 12px; text-align: left;">Statut</th>
                <th style="padding: 12px; text-align: left;">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="q in filteredQuoteRequests" :key="q.id" style="border-bottom: 1px solid var(--color-border);">
                <td style="padding: 12px; color: var(--color-text-light);">#{{ q.id }}</td>
                <td style="padding: 12px;">
                  <strong style="display: block; color: var(--color-text);">{{ q.name }}</strong>
                  <span style="font-size: 0.8rem; color: var(--color-text-light);">{{ q.email }} • {{ q.phone || 'N/C' }}</span>
                </td>
                <td style="padding: 12px; font-weight: 700; color: var(--color-accent);">{{ q.service_title || 'Général' }}</td>
                <td style="padding: 12px; max-width: 260px; font-size: 0.83rem;">
                  <span style="background: rgba(255,255,255,0.08); padding: 2px 8px; border-radius: 6px; font-size: 0.76rem; display: inline-block; margin-bottom: 4px;">💰 Budget: {{ q.budget || 'Non précisé' }}</span>
                  <p style="margin: 0; color: var(--color-text-light); overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">"{{ q.description }}"</p>
                </td>
                <td style="padding: 12px;">
                  <select :value="q.status || 'pending'" @change="updateQuoteStatus(q.id, $event.target.value)" style="padding: 4px 8px; border-radius: 6px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.78rem;">
                    <option value="pending">⏳ En attente</option>
                    <option value="in_progress">⚙️ En cours</option>
                    <option value="completed">✅ Traitée</option>
                    <option value="rejected">❌ Refusée</option>
                  </select>
                </td>
                <td style="padding: 12px;">
                  <button @click="deleteQuoteRequest(q.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                    <Trash2 :size="13" /> Supprimer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 5 : MODÉRATION AVIS -->
    <div v-if="activeTab === 'reviews'" class="fade-in">
      <div class="glass" style="padding: 28px; border-radius: 20px;">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 14px;">
          <div>
            <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">⭐ Modération des Avis Clients</h3>
            <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Consultez et supprimez les avis publiés sur la plateforme.</p>
          </div>
          <div class="flex items-center gap-3">
            <div style="position: relative; width: 220px;">
              <Search :size="14" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);" />
              <input v-model="searchReviewQuery" type="text" placeholder="Filtrer auteur, produit..." style="width: 100%; padding: 6px 12px 6px 30px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
            </div>
            <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 0.82rem;">
              {{ filteredReviews.length }} avis
            </span>
          </div>
        </div>

        <div v-if="filteredReviews.length === 0" class="text-center" style="padding: 40px; color: var(--color-text-light);">
          Aucun avis trouvé.
        </div>

        <div v-else style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; min-width: 650px;">
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
              <tr v-for="rev in filteredReviews" :key="rev.id" style="border-bottom: 1px solid var(--color-border);">
                <td style="padding: 12px; color: var(--color-text-light);">#{{ rev.id }}</td>
                <td style="padding: 12px; font-weight: 600;">{{ rev.name }}</td>
                <td style="padding: 12px; font-weight: 600; color: var(--color-text);">{{ rev.product?.title || 'Produit #' + rev.product_id }}</td>
                <td style="padding: 12px; color: var(--color-accent); font-weight: 800;">{{ '★'.repeat(rev.rating) }}</td>
                <td style="padding: 12px; max-width: 280px; font-size: 0.83rem; color: var(--color-text-light);">"{{ rev.comment }}"</td>
                <td style="padding: 12px;">
                  <button @click="deleteReview(rev.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                    <Trash2 :size="13" /> Supprimer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 6 : BLOG -->
    <div v-if="activeTab === 'blog'" class="fade-in">
      <div class="glass" style="padding: 28px; border-radius: 20px;">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 14px;">
          <div>
            <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">📝 Articles de Blog</h3>
            <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Rédigez et modifiez des articles SEO pour le blog.</p>
          </div>
          <div class="flex items-center gap-3">
            <div style="position: relative; width: 220px;">
              <Search :size="14" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--color-text-light);" />
              <input v-model="searchBlogQuery" type="text" placeholder="Rechercher article..." style="width: 100%; padding: 6px 12px 6px 30px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
            </div>
            <button @click="showPostForm = !showPostForm" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.88rem;">
              {{ showPostForm ? '✕ Annuler' : '+ Nouvel Article' }}
            </button>
          </div>
        </div>

        <div v-if="showPostForm" class="mb-8" style="background: rgba(212,175,55,0.05); border: 1px solid rgba(212,175,55,0.25); padding: 24px; border-radius: 16px;">
          <h4 class="mb-4" style="font-size: 1.1rem; font-weight: 700;">Publier un Article</h4>
          <form @submit.prevent="createPost" class="flex flex-col gap-4">
            <div>
              <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Titre de l'Article *</label>
              <input v-model="newPost.title" type="text" required class="form-input" placeholder="ex: 10 Prompts IA Indispensables" />
            </div>
            <div>
              <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Contenu Markdown / HTML *</label>
              <textarea v-model="newPost.content" required class="form-input" rows="6" placeholder="Rédigez votre article ici..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="padding: 11px; font-weight: 800;">
              📝 Publier l'article
            </button>
          </form>
        </div>

        <div style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
              <tr style="border-bottom: 2px solid var(--color-border);">
                <th style="padding: 12px; text-align: left;">ID</th>
                <th style="padding: 12px; text-align: left;">Titre</th>
                <th style="padding: 12px; text-align: left;">Date</th>
                <th style="padding: 12px; text-align: left;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="post in filteredPosts" :key="post.id" style="border-bottom: 1px solid var(--color-border);">
                <td style="padding: 12px; color: var(--color-text-light);">#{{ post.id }}</td>
                <td style="padding: 12px; font-weight: 600;">{{ post.title }}</td>
                <td style="padding: 12px; color: var(--color-text-light); font-size: 0.82rem;">{{ post.created_at ? new Date(post.created_at).toLocaleDateString('fr-FR') : 'Récents' }}</td>
                <td style="padding: 12px; display: flex; gap: 8px;">
                  <button @click="startEditPost(post)" style="background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid #3b82f6; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                    ✏️ Modifier
                  </button>
                  <button @click="deletePost(post.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                    <Trash2 :size="13" /> Supprimer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 7 : COUPONS DE RÉDUCTION -->
    <div v-if="activeTab === 'coupons'" class="fade-in">
      <div class="glass" style="padding: 28px; border-radius: 20px;">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">🎟️ Codes Promos & Réductions</h3>
            <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Créez des coupons pour stimuler vos ventes au checkout.</p>
          </div>
          <button @click="showCouponForm = !showCouponForm" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.88rem;">
            {{ showCouponForm ? '✕ Annuler' : '+ Nouveau Coupon' }}
          </button>
        </div>

        <div v-if="showCouponForm" class="mb-8" style="background: rgba(212,175,55,0.05); border: 1px solid rgba(212,175,55,0.25); padding: 24px; border-radius: 16px;">
          <h4 class="mb-4" style="font-size: 1.1rem; font-weight: 700;">Créer un Code Promo</h4>
          <form @submit.prevent="createCoupon" class="grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
              <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Code (ex: YASS20) *</label>
              <input v-model="newCoupon.code" type="text" required class="form-input" placeholder="YASS20" />
            </div>
            <div>
              <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Pourcentage de Réduction (%)</label>
              <input v-model="newCoupon.discount_percentage" type="number" step="1" min="1" max="100" class="form-input" placeholder="20" />
            </div>
            <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="grid-column: span 2; padding: 11px; font-weight: 800;">
              🎟️ Enregistrer le coupon
            </button>
          </form>
        </div>

        <div style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; min-width: 550px;">
            <thead>
              <tr style="border-bottom: 2px solid var(--color-border);">
                <th style="padding: 12px; text-align: left;">ID</th>
                <th style="padding: 12px; text-align: left;">Code Promo</th>
                <th style="padding: 12px; text-align: left;">Réduction</th>
                <th style="padding: 12px; text-align: left;">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="c in coupons" :key="c.id" style="border-bottom: 1px solid var(--color-border);">
                <td style="padding: 12px; color: var(--color-text-light);">#{{ c.id }}</td>
                <td style="padding: 12px; font-weight: 800; color: var(--color-accent); font-family: monospace; font-size: 0.95rem;">{{ c.code }}</td>
                <td style="padding: 12px; font-weight: 700;">{{ c.discount_percentage ? c.discount_percentage + '%' : (c.discount_amount + ' €') }}</td>
                <td style="padding: 12px;">
                  <button @click="deleteCoupon(c.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                    <Trash2 :size="13" /> Supprimer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 8 : NEWSLETTER -->
    <div v-if="activeTab === 'newsletter'" class="fade-in">
      <div class="glass" style="padding: 28px; border-radius: 20px;">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">📧 Liste des Abonnés Newsletter</h3>
            <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Téléchargez la liste pour vos campagnes de mailing.</p>
          </div>
          <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 0.82rem;">
            {{ subscribers.length }} abonné(s)
          </span>
        </div>

        <div v-if="subscribers.length === 0" class="text-center" style="padding: 40px; color: var(--color-text-light);">
          Aucun abonné pour le moment.
        </div>

        <div v-else style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; min-width: 500px;">
            <thead>
              <tr style="border-bottom: 2px solid var(--color-border);">
                <th style="padding: 12px; text-align: left;">ID</th>
                <th style="padding: 12px; text-align: left;">Email Abonné</th>
                <th style="padding: 12px; text-align: left;">Date d'inscription</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="sub in subscribers" :key="sub.id" style="border-bottom: 1px solid var(--color-border);">
                <td style="padding: 12px; color: var(--color-text-light);">#{{ sub.id }}</td>
                <td style="padding: 12px; font-weight: 600;">{{ sub.email }}</td>
                <td style="padding: 12px; color: var(--color-text-light); font-size: 0.82rem;">{{ sub.created_at ? new Date(sub.created_at).toLocaleDateString('fr-FR') : 'Récent' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useToastStore } from '../stores/toast';
import { useRouter } from 'vue-router';
import { ref, computed, onMounted } from 'vue';
import SalesChart from '../components/SalesChart.vue';
import api from '../api';
import { 
  Crown, 
  Shield, 
  Wrench, 
  Feather, 
  Headphones, 
  LogOut, 
  FileDown, 
  DollarSign, 
  ShoppingBag, 
  Package, 
  Mail, 
  Download, 
  CheckCircle2, 
  Clock, 
  Trash2, 
  Search, 
  TrendingUp 
} from 'lucide-vue-next';

const auth = useAuthStore();
const toastStore = useToastStore();
const router = useRouter();

const activeTab = ref('overview');

// Recherches réactives pour chaque onglet
const searchProductQuery = ref('');
const searchUserQuery = ref('');
const searchOrderQuery = ref('');
const searchQuoteQuery = ref('');
const searchReviewQuery = ref('');
const searchBlogQuery = ref('');

const visibleTabs = computed(() => {
  const role = auth.user?.role;
  if (role === 'super_admin') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'users', icon: '👑', label: 'Utilisateurs & Rôles' },
      { id: 'products', icon: '🛍️', label: 'Produits' },
      { id: 'quotes', icon: '📋', label: 'Devis' },
      { id: 'reviews', icon: '⭐', label: 'Avis' },
      { id: 'blog', icon: '📝', label: 'Blog' },
      { id: 'coupons', icon: '🎟️', label: 'Coupons' },
      { id: 'newsletter', icon: '📧', label: 'Newsletter' },
    ];
  }
  if (role === 'creator') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'products', icon: '🛍️', label: 'Mes Produits' },
      { id: 'quotes', icon: '📋', label: 'Demandes Devis' },
      { id: 'reviews', icon: '⭐', label: 'Avis Produits' },
    ];
  }
  if (role === 'editor') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'blog', icon: '📝', label: 'Articles Blog' },
    ];
  }
  if (role === 'support') {
    return [
      { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
      { id: 'quotes', icon: '📋', label: 'Demandes Devis' },
      { id: 'reviews', icon: '⭐', label: 'Modération Avis' },
      { id: 'newsletter', icon: '📧', label: 'Abonnés' },
    ];
  }
  return [
    { id: 'overview', icon: '📊', label: 'Vue d\'ensemble' },
    { id: 'products', icon: '🛍️', label: 'Produits' },
    { id: 'quotes', icon: '📋', label: 'Devis' },
    { id: 'reviews', icon: '⭐', label: 'Avis' },
    { id: 'blog', icon: '📝', label: 'Blog' },
    { id: 'coupons', icon: '🎟️', label: 'Coupons' },
    { id: 'newsletter', icon: '📧', label: 'Newsletter' },
  ];
});

const products = ref([]);
const orders = ref([]);
const posts = ref([]);
const subscribers = ref([]);
const coupons = ref([]);
const usersList = ref([]);
const quoteRequests = ref([]);
const reviewsList = ref([]);

const tabCounts = computed(() => ({
  overview: null,
  users: usersList.value.length,
  products: products.value.length,
  quotes: quoteRequests.value.length,
  reviews: reviewsList.value.length,
  blog: posts.value.length,
  coupons: coupons.value.length,
  newsletter: subscribers.value.length,
}));

// Données filtrées selon la recherche
const filteredProducts = computed(() => {
  if (!searchProductQuery.value.trim()) return products.value;
  const q = searchProductQuery.value.toLowerCase();
  return products.value.filter(p => p.title?.toLowerCase().includes(q) || p.type?.toLowerCase().includes(q));
});

const filteredUsers = computed(() => {
  if (!searchUserQuery.value.trim()) return usersList.value;
  const q = searchUserQuery.value.toLowerCase();
  return usersList.value.filter(u => u.name?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q) || u.role?.toLowerCase().includes(q));
});

const filteredOrders = computed(() => {
  if (!searchOrderQuery.value.trim()) return orders.value;
  const q = searchOrderQuery.value.toLowerCase();
  return orders.value.filter(o => String(o.id).includes(q) || o.email?.toLowerCase().includes(q) || o.status?.toLowerCase().includes(q));
});

const filteredQuoteRequests = computed(() => {
  if (!searchQuoteQuery.value.trim()) return quoteRequests.value;
  const q = searchQuoteQuery.value.toLowerCase();
  return quoteRequests.value.filter(r => r.name?.toLowerCase().includes(q) || r.email?.toLowerCase().includes(q) || r.service_title?.toLowerCase().includes(q));
});

const filteredReviews = computed(() => {
  if (!searchReviewQuery.value.trim()) return reviewsList.value;
  const q = searchReviewQuery.value.toLowerCase();
  return reviewsList.value.filter(r => r.name?.toLowerCase().includes(q) || r.comment?.toLowerCase().includes(q));
});

const filteredPosts = computed(() => {
  if (!searchBlogQuery.value.trim()) return posts.value;
  const q = searchBlogQuery.value.toLowerCase();
  return posts.value.filter(p => p.title?.toLowerCase().includes(q));
});

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

onMounted(async () => {
  const allowedRoles = ['admin', 'super_admin', 'creator', 'editor', 'support'];
  if (!auth.token || !allowedRoles.includes(auth.user?.role)) { 
    router.push('/login'); 
    return; 
  }

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

const loadProducts = async () => {
  try {
    const res = await api.get('/products');
    products.value = res.data;
  } catch(e) { products.value = []; }
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
  } catch(e) { usersList.value = []; }
};
const loadReviews = async () => {
  try {
    const res = await api.get('/reviews');
    reviewsList.value = res.data;
  } catch(e) { reviewsList.value = []; }
};
const loadQuoteRequests = async () => {
  try {
    const res = await api.get('/quote-requests');
    quoteRequests.value = res.data;
  } catch(e) { quoteRequests.value = []; }
};

const createProduct = async () => {
  try {
    await api.post('/products', newProduct.value);
    showForm.value = false;
    newProduct.value = { title: '', price: '', type: 'Pack', description: '', category_id: 1 };
    toastStore.showToast('Nouveau produit créé et publié avec succès !', 'success');
    await loadProducts();
  } catch(e) { toastStore.showToast('Erreur lors de la création du produit.', 'error'); }
};

const startEditProduct = (product) => { editingProduct.value = { ...product }; };

const updateProduct = async () => {
  if (!editingProduct.value) return;
  try {
    await api.put(`/products/${editingProduct.value.id}`, editingProduct.value);
    editingProduct.value = null;
    toastStore.showToast('Produit mis à jour avec succès !', 'success');
    await loadProducts();
  } catch(e) { toastStore.showToast('Erreur lors de la modification du produit.', 'error'); }
};

const deleteProduct = async (id) => {
  if (confirm('Supprimer définitivement ce produit ?')) {
    try {
      await api.delete(`/products/${id}`);
      toastStore.showToast('Produit supprimé du catalogue.', 'info');
      await loadProducts();
    } catch(e) { toastStore.showToast('Erreur lors de la suppression.', 'error'); }
  }
};

const changeRole = async (userId, newRole) => {
  try {
    const res = await api.put(`/users/${userId}/role`, { role: newRole });
    toastStore.showToast(res.data.message || 'Rôle mis à jour !', 'success');
    await loadUsers();
  } catch(e) { toastStore.showToast('Erreur lors de la modification du rôle.', 'error'); }
};

const deleteUser = async (userId) => {
  if (confirm('Supprimer définitivement cet utilisateur ?')) {
    try {
      const res = await api.delete(`/users/${userId}`);
      toastStore.showToast(res.data.message || 'Utilisateur supprimé.', 'info');
      await loadUsers();
    } catch(e) { toastStore.showToast('Erreur lors de la suppression.', 'error'); }
  }
};

const updateQuoteStatus = async (id, status) => {
  try {
    await api.put(`/quote-requests/${id}/status`, { status });
    toastStore.showToast('Statut de devis mis à jour !', 'success');
    await loadQuoteRequests();
  } catch(e) { toastStore.showToast('Erreur lors de la mise à jour.', 'error'); }
};

const deleteQuoteRequest = async (id) => {
  if (confirm('Supprimer cette demande de devis ?')) {
    try {
      await api.delete(`/quote-requests/${id}`);
      toastStore.showToast('Demande supprimée.', 'info');
      await loadQuoteRequests();
    } catch(e) { toastStore.showToast('Erreur lors de la suppression.', 'error'); }
  }
};

const deleteReview = async (id) => {
  if (confirm('Supprimer cet avis client ?')) {
    try {
      await api.delete(`/reviews/${id}`);
      toastStore.showToast('Avis supprimé.', 'info');
      await loadReviews();
    } catch(e) { toastStore.showToast('Erreur lors de la suppression.', 'error'); }
  }
};

const createPost = async () => {
  try {
    await api.post('/posts', newPost.value);
    showPostForm.value = false;
    newPost.value = { title: '', content: '', is_published: true };
    toastStore.showToast('Article de blog publié !', 'success');
    await loadPosts();
  } catch(e) { toastStore.showToast('Erreur publication article.', 'error'); }
};

const deletePost = async (id) => {
  if (confirm('Supprimer cet article ?')) {
    try {
      await api.delete(`/posts/${id}`);
      toastStore.showToast('Article supprimé.', 'info');
      await loadPosts();
    } catch(e) { toastStore.showToast('Erreur lors de la suppression.', 'error'); }
  }
};

const createCoupon = async () => {
  try {
    await api.post('/coupons', newCoupon.value);
    showCouponForm.value = false;
    newCoupon.value = { code: '', discount_amount: '', discount_percentage: '', expires_at: '' };
    toastStore.showToast('Code promo créé avec succès !', 'success');
    await loadCoupons();
  } catch(e) { toastStore.showToast('Erreur création du coupon.', 'error'); }
};

const deleteCoupon = async (id) => {
  if (confirm('Supprimer ce coupon ?')) {
    try {
      await api.delete(`/coupons/${id}`);
      toastStore.showToast('Coupon supprimé.', 'info');
      await loadCoupons();
    } catch(e) { toastStore.showToast('Erreur lors de la suppression.', 'error'); }
  }
};

const handleLogout = async () => {
  await auth.logout();
  router.push('/');
};

const exportOrdersCSV = () => {
  if (orders.value.length === 0) {
    toastStore.showToast('Aucune commande à exporter.', 'info');
    return;
  }
  const headers = ['ID', 'Email Client', 'Montant EUR', 'Statut', 'Date'];
  const rows = orders.value.map(o => [o.id, o.email, o.total_amount, o.status, o.created_at || '']);
  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `rapport_ventes_${new Date().toISOString().slice(0,10)}.csv`);
  document.body.appendChild(link);
  link.click();
  link.remove();
  toastStore.showToast('Export CSV des ventes téléchargé avec succès !', 'success');
};

const generatePDFReport = () => {
  toastStore.showToast('Génération du rapport financier PDF...', 'info');
  const reportHTML = `<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Rapport Financier - Yass Digital Lab</title>
  <style>
    body { font-family: sans-serif; padding: 40px; color: #0f172a; }
    h1 { color: #d4af37; border-bottom: 2px solid #d4af37; padding-bottom: 10px; }
    .kpi { display: flex; gap: 20px; margin: 20px 0; }
    .card { background: #f8fafc; border: 1px solid #cbd5e1; padding: 15px 25px; border-radius: 10px; flex: 1; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
    th { background: #f1f5f9; }
  </style>
</head>
<body>
  <h1>Yass Digital Lab — Rapport financier & Ventes</h1>
  <p>Généré le: ${new Date().toLocaleString('fr-FR')}</p>
  <div class="kpi">
    <div class="card"><h3>Revenu Total</h3><h2>${totalRevenue.value.toFixed(2)} €</h2></div>
    <div class="card"><h3>Total Commandes</h3><h2>${orders.value.length}</h2></div>
    <div class="card"><h3>Produits Actifs</h3><h2>${products.value.length}</h2></div>
  </div>
  <h3>Dernières Transactions</h3>
  <table>
    <thead><tr><th>ID</th><th>Client</th><th>Montant</th><th>Statut</th></tr></thead>
    <tbody>
      ${orders.value.map(o => `<tr><td>#${o.id}</td><td>${o.email}</td><td>${o.total_amount} €</td><td>${o.status}</td></tr>`).join('')}
    </tbody>
  </table>
</body>
</html>`;
  const blob = new Blob([reportHTML], { type: 'text/html;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `rapport_financier_yassdigitallab_${new Date().toISOString().slice(0,10)}.html`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
  toastStore.showToast('Rapport financier généré !', 'success');
};
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
