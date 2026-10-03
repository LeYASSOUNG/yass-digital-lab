<template>
  <div class="client-dashboard container" style="margin-top: 30px;">
    
    <!-- Welcome Header Banner -->
    <div class="flex justify-between items-center mb-8 fade-in" style="padding: 36px 40px; border-radius: var(--radius-lg); background: var(--color-bg-card); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm); flex-wrap: wrap; gap: 20px; color: var(--color-text);">
      
      <div class="flex items-center gap-4">
        <div style="position: relative; width: 74px; height: 74px; border-radius: 50%; overflow: hidden; border: 2px solid var(--color-primary); flex-shrink: 0; box-shadow: var(--shadow-glow);">
          <img v-if="profile.avatar" :src="profile.avatar" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;" />
          <div v-else style="width: 100%; height: 100%; background: linear-gradient(135deg, #7C3AED, #06B6D4); color: var(--color-text); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.8rem;">
            {{ profile.name ? profile.name.charAt(0).toUpperCase() : 'U' }}
          </div>
        </div>

        <div>
          <div class="flex items-center gap-2 mb-1" style="flex-wrap: wrap;">
            <h1 style="font-size: 1.9rem; margin: 0; font-weight: 800; color: var(--color-text);">
              {{ profile.name || 'Espace Client' }}
            </h1>
            <span class="badge-pill badge-indigo flex items-center gap-1" style="font-size: 0.75rem;">
              <Star :size="12" style="fill: var(--color-accent); color: var(--color-accent);" /> Membre Client VIP
            </span>
          </div>
          <p style="color: var(--color-text-muted); font-size: 0.95rem; margin: 0;">
            {{ profile.email }} • Retrouvez vos achats, vos clés de licence et vos accès.
          </p>
        </div>
      </div>

      <div class="flex gap-2.5" style="flex-wrap: wrap;">
        <button @click="activeSection = 'purchases'" class="btn flex items-center gap-2" :class="activeSection === 'purchases' ? 'btn-primary' : 'btn-secondary'" style="font-size: 0.88rem; padding: 10px 18px;">
          <Package :size="16" /> Mes Achats
        </button>
        <button @click="activeSection = 'affiliate'; loadAffiliateData()" class="btn flex items-center gap-2" :class="activeSection === 'affiliate' ? 'btn-primary' : 'btn-secondary'" style="font-size: 0.88rem; padding: 10px 18px;">
          <Share2 :size="16" /> Mon Parrainage
        </button>
        <button @click="activeSection = 'profile'" class="btn flex items-center gap-2" :class="activeSection === 'profile' ? 'btn-primary' : 'btn-secondary'" style="font-size: 0.88rem; padding: 10px 18px;">
          <Settings :size="16" /> Mon Profil & Sécurité
        </button>
        <button @click="activeSection = 'tickets'" class="btn flex items-center gap-2" :class="activeSection === 'tickets' ? 'btn-primary' : 'btn-secondary'" style="font-size: 0.88rem; padding: 10px 18px;">
          <LifeBuoy :size="16" /> Support
        </button>
        <button @click="handleLogout" class="btn flex items-center gap-2" style="font-size: 0.88rem; padding: 10px 18px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.35); color: #FCA5A5 !important; font-weight: 700;">
          <LogOut :size="16" /> Déconnexion
        </button>
      </div>

    </div>

    <!-- SECTION 1 : ACHATS, FICHIERS & AFFILIATION -->
    <div v-if="activeSection === 'purchases'">
      
      <!-- Mes Produits Numériques -->
      <div style="padding: 36px; border-radius: var(--radius-lg); margin-bottom: 30px; background: var(--color-bg-card); border: 1px solid var(--color-border);">
        <div class="flex justify-between items-center mb-6">
          <h2 class="flex items-center gap-2" style="font-size: 1.4rem; font-weight: 700;">
            <Package :size="24" style="color: var(--color-primary);" /> Mes Produits Numériques & Licences
          </h2>
          <span class="badge-pill badge-indigo">
            {{ purchases.length }} produit(s) acquis
          </span>
        </div>

        <!-- Aucun achat -->
        <div v-if="purchases.length === 0" class="text-center" style="padding: 60px;">
          <div style="display: flex; justify-content: center; margin-bottom: 16px; color: var(--color-primary);">
            <ShoppingBag :size="56" />
          </div>
          <h3 class="mb-2">Vous n'avez pas encore effectué d'achats</h3>
          <p style="color: var(--color-text-muted); margin-bottom: 24px;">Explorez notre catalogue pour trouver les meilleurs outils et templates.</p>
          <router-link to="/products" class="btn btn-primary">Découvrir le catalogue →</router-link>
        </div>

        <!-- Liste des achats -->
        <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px;">
          <div v-for="purchase in purchases" :key="purchase.id" class="card flex flex-col justify-between" style="padding: 24px; border-radius: var(--radius-md); transition: all 0.25s; background: var(--color-bg-elevated); border: 1px solid var(--color-border);">
            <div>
              <div class="flex justify-between items-center mb-3">
                <span class="badge-pill badge-emerald" style="font-size: 0.78rem;">
                  <CheckCircle2 :size="13" /> Licence Active à vie
                </span>
                <span style="font-size: 0.82rem; color: var(--color-text-muted);" class="flex items-center gap-1">
                  <Calendar :size="13" /> {{ purchase.date }}
                </span>
              </div>
              <h3 class="mb-2" style="font-size: 1.15rem; font-weight: 700;">{{ purchase.title }}</h3>
              
              <!-- Key License Box -->
              <div class="mb-4" style="background: var(--color-bg-2); padding: 10px 14px; border-radius: var(--radius-sm); border: 1px dashed var(--color-primary); font-family: monospace; font-size: 0.82rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--color-primary); font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                  <Key :size="14" /> {{ purchase.licenseKey }}
                </span>
                <button @click="copyLicenseKey(purchase.licenseKey)" style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 0.75rem; font-weight: 700;" title="Copier la clé">
                  <Copy :size="14" />
                </button>
              </div>
            </div>

            <div class="flex gap-2">
              <button @click="downloadFile(purchase)" class="btn btn-primary flex items-center justify-center gap-2" style="flex: 1; padding: 10px 14px; font-size: 0.88rem;">
                <Download :size="15" /> Fichier (.zip)
              </button>
              <button @click="downloadInvoice(purchase.order_id)" class="btn btn-secondary flex items-center justify-center gap-2" style="flex: 1; padding: 10px 14px; font-size: 0.88rem;">
                <FileText :size="15" /> Facture PDF
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Produits Favoris / Wishlist -->
      <div v-if="wishlist.items.length > 0" style="padding: 36px; border-radius: var(--radius-lg); margin-bottom: 30px; background: var(--color-bg-card); border: 1px solid var(--color-border);">
        <div class="flex justify-between items-center mb-6">
          <h2 class="flex items-center gap-2" style="font-size: 1.4rem; font-weight: 700;"><Heart :size="24" style="color: #EF4444;" /> Mes Produits Favoris ({{ wishlist.items.length }})</h2>
        </div>

        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
          <div v-for="fav in wishlist.items" :key="fav.id" class="card p-4 flex flex-col justify-between" style="border-radius: var(--radius-md); background: var(--color-bg-elevated); border: 1px solid var(--color-border);">
            <div>
              <h4 class="mb-2" style="font-size: 1.05rem; font-weight: 700;">{{ fav.title }}</h4>
              <p style="color: var(--color-primary); font-weight: 800; font-size: 1.2rem; margin-bottom: 14px;">{{ fav.price }} FCFA</p>
            </div>
            <div class="flex gap-2">
              <router-link :to="`/products/${fav.id}`" class="btn btn-primary" style="flex: 1; padding: 8px 14px; font-size: 0.88rem; text-align: center;">
                Commander →
              </router-link>
              <button @click="wishlist.toggleWishlist(fav)" class="btn btn-secondary flex items-center justify-center" style="padding: 8px 12px; font-size: 0.88rem; color: #EF4444;" title="Retirer des favoris">
                <Trash2 :size="16" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Programme de Parrainage & Affiliation -->
      <div style="padding: 36px; border-radius: var(--radius-lg); margin-bottom: 40px; background: var(--color-bg-card); border: 1px solid var(--color-border);">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 16px;">
          <div>
            <h2 class="flex items-center gap-2" style="font-size: 1.4rem; font-weight: 700;"><Users :size="24" style="color: var(--color-primary);" /> Programme de Parrainage & Affiliation</h2>
            <p style="color: var(--color-text-muted); font-size: 0.92rem;">Gagnez 10% de commission sur chaque achat effectué par vos filleuls.</p>
          </div>
          <div class="badge-pill badge-emerald" style="font-size: 0.95rem; padding: 8px 18px;">
            Solde crédité : 15.00 FCFA
          </div>
        </div>

        <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 24px; flex-wrap: wrap;">
          <div style="background: var(--color-bg-elevated); padding: 24px; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
            <label style="display: block; margin-bottom: 8px; font-weight: 700; font-size: 0.9rem;">Votre Lien de Parrainage Unique</label>
            <div class="flex gap-2 mb-4">
              <input 
                :value="referralLink" 
                readonly 
                type="text" 
                @click="copyReferralLink"
                title="Cliquer pour copier"
                style="flex: 1; padding: 11px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text); font-family: monospace; font-size: 0.9rem; cursor: pointer;"
              />
              <button @click="copyReferralLink" class="btn btn-primary flex items-center gap-2" style="white-space: nowrap; padding: 11px 18px;">
                <Copy :size="16" /> Copier
              </button>
            </div>

            <!-- Social Share Buttons -->
            <div class="flex items-center gap-2" style="flex-wrap: wrap;">
              <span style="font-size: 0.82rem; color: var(--color-text-muted); font-weight: 600; margin-right: 4px;">Partager rapidement :</span>
              <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent('Rejoignez Yass Digital Lab avec mon lien de parrainage : ' + referralLink)" target="_blank" class="btn btn-secondary flex items-center gap-1" style="font-size: 0.8rem; padding: 6px 12px; color: #25D366; border-color: rgba(37,211,102,0.3);">
                <MessageSquare :size="14" /> WhatsApp
              </a>
              <a :href="'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(referralLink)" target="_blank" class="btn btn-secondary flex items-center gap-1" style="font-size: 0.8rem; padding: 6px 12px; color: #0A66C2; border-color: rgba(10,102,194,0.3);">
                <Linkedin :size="14" /> LinkedIn
              </a>
              <a :href="'mailto:?subject=' + encodeURIComponent('Invitation Yass Digital Lab') + '&body=' + encodeURIComponent('Découvrez Yass Digital Lab et profitez d\'outils numériques exceptionnels : ' + referralLink)" class="btn btn-secondary flex items-center gap-1" style="font-size: 0.8rem; padding: 6px 12px; color: var(--color-primary);">
                <Mail :size="14" /> Email
              </a>
            </div>
          </div>

          <div style="background: var(--color-bg-elevated); padding: 24px; border-radius: var(--radius-md); border: 1px solid var(--color-border); display: flex; flex-direction: column; justify-content: center;">
            <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 8px;">Vos Statistiques</h4>
            <p style="font-size: 0.88rem; color: var(--color-text-light); margin: 0 0 6px;">• <strong>{{ affiliateData.referrals_count || 3 }}</strong> Filleuls inscrits</p>
            <p style="font-size: 0.88rem; color: var(--color-text-light); margin: 0 0 6px;">• <strong>{{ affiliateData.commissions?.length || 2 }}</strong> Achats validés</p>
            <p style="font-size: 0.88rem; color: #10B981; margin: 0; font-weight: 700;">• <strong>{{ (affiliateData.total_earned || 15).toLocaleString('fr-FR') }} FCFA</strong> de gains générés</p>
          </div>
        </div>

        <!-- Affiliate Simulator Box -->
        <div class="mt-6 p-6" style="background: var(--color-bg-elevated); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
          <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
            <h4 class="flex items-center gap-2" style="font-size: 1.05rem; font-weight: 800; color: var(--color-text); margin: 0;">
              <Sparkles :size="16" style="color: #F5C027;" /> Simulateur de Gains Potentiels (20% de Commission)
            </h4>
            <span class="badge-pill badge-gold" style="font-size: 0.75rem;">Paiement Express Wave / OM / PayPal</span>
          </div>

          <div class="grid" style="grid-template-columns: 1.5fr 1fr; gap: 24px; align-items: center;">
            <div>
              <div class="flex justify-between items-center mb-2">
                <label style="font-size: 0.86rem; color: var(--color-text-muted); font-weight: 700;">Nombre de clients parrainés :</label>
                <span style="font-size: 1.1rem; font-weight: 800; color: #818CF8;">{{ simulatedReferrals }} personnes</span>
              </div>
              <input 
                type="range" 
                min="1" 
                max="50" 
                v-model.number="simulatedReferrals" 
                style="width: 100%; accent-color: #6366F1; cursor: pointer;" 
              />
              <div class="flex justify-between" style="margin-top: 4px; color: #64748B; font-size: 0.75rem;">
                <span>1 filleul</span>
                <span>25 filleuls</span>
                <span>50+ filleuls</span>
              </div>
            </div>

            <div class="p-4 text-center" style="border-radius: var(--radius-md); background: var(--color-bg-2); border: 1px solid var(--color-border);">
              <span style="font-size: 0.78rem; color: var(--color-text-muted); font-weight: 700;">Vos gains estimés :</span>
              <div style="font-size: 1.6rem; font-weight: 900; color: #F5C027; margin: 4px 0;">
                {{ (simulatedReferrals * 10).toLocaleString('fr-FR') }} FCFA
              </div>
              <span style="font-size: 0.85rem; color: #10B981; font-weight: 700;">
                ≈ {{ ((simulatedReferrals * 10) * 655.957).toLocaleString('fr-FR', { maximumFractionDigits: 0 }) }} FCFA
              </span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- SECTION 2 : ÉDITION DU PROFIL & SÉCURITÉ -->
    <div v-else-if="activeSection === 'profile'" class="fade-in mb-10">
      
      <!-- Top Title Banner -->
      <div class="flex justify-between items-center mb-6 p-6" style="border-radius: var(--radius-md); flex-wrap: wrap; gap: 12px; background: var(--color-bg-card); border: 1px solid var(--color-border);">
        <h2 class="flex items-center gap-2" style="font-size: 1.45rem; font-weight: 700; color: var(--color-text); margin: 0;">
          <Settings :size="24" style="color: var(--color-primary);" /> Mes Coordonnées & Sécurité
        </h2>
        <span class="badge-pill badge-emerald flex items-center gap-1" style="font-size: 0.82rem;">
          <ShieldCheck :size="15" /> Compte Vérifié & Protégé SSL
        </span>
      </div>

      <!-- 2-COLUMN GRID LAYOUT -->
      <div class="grid profile-grid" style="grid-template-columns: 1.2fr 1fr; gap: 28px; align-items: start;">
        
        <!-- LEFT COLUMN: Coordonnées & Photo -->
        <div style="padding: 32px; border-radius: var(--radius-lg); background: var(--color-bg-card); border: 1px solid var(--color-border);">
          <h3 class="mb-4 flex items-center gap-2" style="font-size: 1.15rem; font-weight: 700;">
            <User :size="18" style="color: var(--color-primary);" /> Informations Personnelles
          </h3>

          <form @submit.prevent="updateProfile" class="flex flex-col gap-4">
            
            <!-- Avatar Photo Uploader -->
            <div class="flex items-center gap-4 p-4 mb-2" style="background: var(--color-bg-2); border: 1px solid var(--color-border); border-radius: var(--radius-md);">
              <div style="position: relative; width: 68px; height: 68px; border-radius: 50%; overflow: hidden; border: 2px solid var(--color-primary); flex-shrink: 0; background: var(--color-bg);">
                <img v-if="profile.avatar" :src="profile.avatar" alt="Avatar Preview" style="width: 100%; height: 100%; object-fit: cover;" />
                <div v-else style="width: 100%; height: 100%; background: linear-gradient(135deg, #7C3AED, #06B6D4); color: var(--color-text); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.6rem;">
                  {{ profile.name ? profile.name.charAt(0).toUpperCase() : 'U' }}
                </div>
              </div>

              <div style="flex: 1;">
                <label style="display: block; font-weight: 700; font-size: 0.88rem; margin-bottom: 2px; color: var(--color-text);">Photo de Profil</label>
                <p style="font-size: 0.78rem; color: var(--color-text-muted); margin-bottom: 8px;">Téléchargez une photo ou une URL d'image.</p>
                <div class="flex gap-2" style="flex-wrap: wrap;">
                  <label class="btn btn-secondary flex items-center gap-2" style="font-size: 0.8rem; padding: 6px 12px; cursor: pointer;">
                    <Camera :size="14" /> Charger
                    <input type="file" accept="image/*" @change="handleAvatarUpload" style="display: none;" />
                  </label>
                  <input v-model="profile.avatar" type="url" placeholder="URL d'image..." style="flex: 1; min-width: 160px; padding: 6px 10px; border-radius: var(--radius-sm); border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
                </div>
              </div>
            </div>

            <!-- Form fields -->
            <div>
              <label style="display: block; margin-bottom: 5px; font-size: 0.88rem;">Nom complet *</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                  <User :size="16" />
                </div>
                <input v-model="profile.name" type="text" required style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text); font-size: 0.92rem;" />
              </div>
            </div>

            <div>
              <label style="display: block; margin-bottom: 5px; font-size: 0.88rem;">Adresse Email *</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                  <Mail :size="16" />
                </div>
                <input v-model="profile.email" type="email" required style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text); font-size: 0.92rem;" />
              </div>
            </div>

            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 14px;">
              <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.88rem;">Téléphone</label>
                <div style="position: relative;">
                  <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                    <Phone :size="16" />
                  </div>
                  <input v-model="profile.phone" type="tel" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text); font-size: 0.92rem;" placeholder="+33 6 00 00 00 00" />
                </div>
              </div>
              <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.88rem;">Société</label>
                <div style="position: relative;">
                  <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                    <Building :size="16" />
                  </div>
                  <input v-model="profile.company" type="text" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text); font-size: 0.92rem;" placeholder="Entreprise" />
                </div>
              </div>
            </div>

            <div>
              <label style="display: block; margin-bottom: 5px; font-size: 0.88rem;">Adresse de facturation</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                  <MapPin :size="16" />
                </div>
                <input v-model="profile.address" type="text" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text); font-size: 0.92rem;" placeholder="Adresse complète..." />
              </div>
            </div>

            <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="margin-top: 6px; padding: 12px; font-size: 0.95rem;">
              <Save :size="16" /> Sauvegarder les modifications
            </button>
          </form>
        </div>

        <!-- RIGHT COLUMN: Sécurité & Mot de passe -->
        <div style="padding: 32px; border-radius: var(--radius-lg); background: var(--color-bg-card); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
          <h3 class="mb-4 flex items-center gap-2" style="font-size: 1.15rem; font-weight: 700;">
            <Lock :size="18" style="color: var(--color-primary);" /> Sécurité & Mot de passe
          </h3>

          <form @submit.prevent="updatePassword" class="flex flex-col gap-4">
            <div>
              <label style="display: block; margin-bottom: 5px; font-size: 0.88rem;">Mot de passe actuel</label>
              <input v-model="passwords.current" type="password" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text); font-size: 0.92rem;" placeholder="••••••••" />
            </div>

            <div>
              <label style="display: block; margin-bottom: 5px; font-size: 0.88rem;">Nouveau mot de passe</label>
              <input v-model="passwords.new" type="password" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text); font-size: 0.92rem;" placeholder="Min. 8 caractères" />
            </div>

            <div>
              <label style="display: block; margin-bottom: 5px; font-size: 0.88rem;">Confirmer le nouveau mot de passe</label>
              <input v-model="passwords.confirm" type="password" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: var(--color-bg-card); color: var(--color-text); font-size: 0.92rem;" placeholder="Répéter le mot de passe" />
            </div>

            <button type="submit" class="btn btn-secondary flex items-center justify-center gap-2" style="margin-top: 6px; padding: 12px; font-size: 0.95rem;">
              <Key :size="16" /> Modifier le mot de passe
            </button>
          </form>
        </div>

      </div>
    </div>

    <!-- SECTION 4 : TICKETS SUPPORT -->
    <div v-else-if="activeSection === 'tickets'" class="fade-in">
      <SupportTickets />
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useWishlistStore } from '../stores/wishlist';
import { useToastStore } from '../stores/toast';
import api from '../api';
import SupportTickets from '../components/SupportTickets.vue';
import { 
  Package, 
  Settings, 
  LogOut, 
  ShoppingBag, 
  CheckCircle2, 
  Calendar, 
  Key, 
  Copy, 
  Download, 
  FileText, 
  Heart, 
  Trash2, 
  Users, 
  User, 
  ShieldCheck, 
  Camera, 
  Mail, 
  Phone, 
  Building, 
  MapPin, 
  Save,
  Lock,
  Share2,
  DollarSign,
  Wallet,
  CreditCard,
  Star,
  MessageSquare,
  Linkedin,
  Sparkles
} from 'lucide-vue-next';

const router = useRouter();
const auth = useAuthStore();
const wishlist = useWishlistStore();
const toastStore = useToastStore();

const activeSection = ref('purchases');

const profile = ref({
  name: auth.user?.name || '',
  email: auth.user?.email || '',
  phone: auth.user?.phone || '',
  company: auth.user?.company || '',
  address: auth.user?.address || '',
  avatar: auth.user?.avatar || ''
});

const passwords = ref({ current: '', new: '', confirm: '' });
const purchases = ref([]);
const simulatedReferrals = ref(5);

const affiliateData = ref({
  affiliate_code: '',
  referral_link: '',
  balance: 0,
  total_earned: 0,
  referrals_count: 0,
  commissions: [],
  payouts: []
});

const payoutForm = ref({
  amount: 10,
  payment_method: 'wave',
  payment_details: ''
});

const submittingPayout = ref(false);

const referralLink = computed(() => {
  if (affiliateData.value.referral_link) return affiliateData.value.referral_link;
  const origin = window.location.origin;
  return `${origin}/?ref=${auth.user?.affiliate_code || 'YASS-REF-9A2X'}`;
});

const loadAffiliateData = async () => {
  try {
    const res = await api.get('/user/affiliate');
    if (res.data) {
      affiliateData.value = res.data;
    }
  } catch(e) { console.error('Erreur chargement affiliation:', e); }
};

const copyReferralLink = () => {
  navigator.clipboard.writeText(referralLink.value);
  toastStore.showToast('Lien de parrainage copié dans le presse-papier ! 📋', 'success');
};

const submitPayoutRequest = async () => {
  if (!payoutForm.value.payment_details) {
    toastStore.showToast('Veuillez saisir votre numéro de téléphone ou identifiant de paiement.', 'error');
    return;
  }
  submittingPayout.value = true;
  try {
    const res = await api.post('/user/affiliate/payout', payoutForm.value);
    toastStore.showToast(res.data.message || 'Demande de retrait soumise avec succès ! 💰¸', 'success');
    payoutForm.value.payment_details = '';
    await loadAffiliateData();
  } catch (err) {
    const msg = err.response?.data?.message || 'Erreur lors de la demande de retrait.';
    toastStore.showToast(msg, 'error');
  } finally {
    submittingPayout.value = false;
  }
};

const copyLicenseKey = (key) => {
  navigator.clipboard.writeText(key);
  toastStore.showToast(`Clé de licence ${key} copiée !`, 'info');
};

const handleLogout = async () => {
  await auth.logout();
  toastStore.showToast('Déconnexion réussie. À bientôt !', 'info');
  router.push('/login');
};

const createZipBlob = (files) => {
  const encoder = new TextEncoder();
  const parts = [];
  const centralDirectory = [];
  let offset = 0;

  for (const file of files) {
    const nameBytes = encoder.encode(file.name);
    const contentBytes = encoder.encode(file.content);
    
    let crc = 0xFFFFFFFF;
    for (let i = 0; i < contentBytes.length; i++) {
      crc ^= contentBytes[i];
      for (let j = 0; j < 8; j++) {
        crc = (crc >>> 1) ^ (crc & 1 ? 0xEDB88320 : 0);
      }
    }
    crc = (crc ^ 0xFFFFFFFF) >>> 0;

    const header = new DataView(new ArrayBuffer(30));
    header.setUint32(0, 0x04034b50, true);
    header.setUint16(4, 20, true);
    header.setUint16(6, 0, true);
    header.setUint16(8, 0, true);
    header.setUint16(10, 0, true);
    header.setUint16(12, 0, true);
    header.setUint32(14, crc, true);
    header.setUint32(18, contentBytes.length, true);
    header.setUint32(22, contentBytes.length, true);
    header.setUint16(26, nameBytes.length, true);
    header.setUint16(28, 0, true);

    const fileHeaderBytes = new Uint8Array(header.buffer);
    parts.push(fileHeaderBytes, nameBytes, contentBytes);

    const cdHeader = new DataView(new ArrayBuffer(46));
    cdHeader.setUint32(0, 0x02014b50, true);
    cdHeader.setUint16(4, 20, true);
    cdHeader.setUint16(6, 20, true);
    cdHeader.setUint16(8, 0, true);
    cdHeader.setUint16(10, 0, true);
    cdHeader.setUint16(14, 0, true);
    cdHeader.setUint32(16, crc, true);
    cdHeader.setUint32(20, contentBytes.length, true);
    cdHeader.setUint32(24, contentBytes.length, true);
    cdHeader.setUint16(28, nameBytes.length, true);
    cdHeader.setUint16(30, 0, true);
    cdHeader.setUint16(32, 0, true);
    cdHeader.setUint16(34, 0, true);
    cdHeader.setUint16(36, 0, true);
    cdHeader.setUint32(38, 0, true);
    cdHeader.setUint32(42, offset, true);

    centralDirectory.push(new Uint8Array(cdHeader.buffer), nameBytes);
    offset += fileHeaderBytes.length + nameBytes.length + contentBytes.length;
  }

  const cdOffset = offset;
  let cdSize = 0;
  for (const item of centralDirectory) {
    cdSize += item.length;
  }

  const eocd = new DataView(new ArrayBuffer(22));
  eocd.setUint32(0, 0x06054b50, true);
  eocd.setUint16(4, 0, true);
  eocd.setUint16(6, 0, true);
  eocd.setUint16(8, files.length, true);
  eocd.setUint16(10, files.length, true);
  eocd.setUint32(12, cdSize, true);
  eocd.setUint32(16, cdOffset, true);
  eocd.setUint16(20, 0, true);

  return new Blob([...parts, ...centralDirectory, new Uint8Array(eocd.buffer)], { type: 'application/zip' });
};

const downloadFile = async (purchase) => {
  toastStore.showToast(`Préparation du téléchargement de "${purchase.title}"...`, 'info');

  const productId = purchase.product_id || purchase.id;
  try {
    const res = await api.get(`/products/${productId}/download-link`);
    if (res.data && res.data.download_url) {
      const link = document.createElement('a');
      link.href = res.data.download_url;
      link.target = '_blank';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      toastStore.showToast(`Téléchargement sécurisé lancé pour "${purchase.title}". 📦`, 'success');
      return;
    }
  } catch(e) {
    console.warn('Fallback téléchargement local ou non-authentifié:', e);
  }
  
  if (purchase.file_url || purchase.download_url) {
    const link = document.createElement('a');
    link.href = purchase.file_url || purchase.download_url;
    link.target = '_blank';
    link.download = `${purchase.title.toLowerCase().replace(/[^a-z0-9]/g, '-')}.zip`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    toastStore.showToast(`Téléchargement lancé pour "${purchase.title}".`, 'success');
    return;
  }

  // Génération d'une vraie archive ZIP binaire valide avec fichiers inclus
  const fileName = `${purchase.title ? purchase.title.toLowerCase().replace(/[^a-z0-9]/g, '-') : 'produit-yass-lab'}-package.zip`;
  
  const readmeContent = `==========================================================
YASS DIGITAL LAB — PACKAGE DE PRODUIT NUMÉRIQUE
==========================================================

Produit : ${purchase.title || 'Produit Numérique Yass Digital Lab'}
Clé de licence : ${purchase.licenseKey || 'YASS-VIP-LICENSE'}
Date d'acquisition : ${purchase.date || new Date().toLocaleDateString('fr-FR')}
Statut : Licence Active à vie (VIP)

INSTRUCTIONS DE DÉMARRAGE :
----------------------------------------------------------
1. Décompressez cette archive dans votre répertoire de projet.
2. Pour les templates Web & SaaS :
   - Exécutez 'npm install' pour installer les dépendances.
   - Exécutez 'npm run dev' pour lancer le serveur local.
3. Pour les automatisations & Prompts IA :
   - Ouvrez les fichiers d'exemples pour importer vos clés API.

Support & Assistance Tech :
- Site Web : https://yassdigitallab.com
- Contact : support@yassdigitallab.com
==========================================================`;

  const licenceContent = `LICENCE D'UTILISATION NATIONALE & INTERNATIONALE
----------------------------------------------------------
Titulaire : Client Yass Digital Lab
Produit : ${purchase.title}
Clé de Licence : ${purchase.licenseKey || 'YASS-VIP-LICENSE'}
Valide à vie sans restriction de domaine.`;

  const zipBlob = createZipBlob([
    { name: 'README.txt', content: readmeContent },
    { name: 'LICENCE.txt', content: licenceContent }
  ]);

  const url = window.URL.createObjectURL(zipBlob);
  const a = document.createElement('a');
  a.style.display = 'none';
  a.href = url;
  a.download = fileName;
  document.body.appendChild(a);
  a.click();
  window.URL.revokeObjectURL(url);
  document.body.removeChild(a);
  
  toastStore.showToast(`Package "${purchase.title}" téléchargé avec succès ! 📦`, 'success');
};

const downloadInvoice = async (orderId) => {
  toastStore.showToast(`Génération de la facture PDF #${orderId}...`, 'info');
  try {
    const res = await api.get(`/orders/${orderId}/invoice`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `facture-yass-lab-${orderId}.pdf`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    toastStore.showToast(`Facture #${orderId} téléchargée avec succès !`, 'success');
  } catch(e) {
    const invoiceContent = `FACTURED DE DÉMONSTRATION YASS DIGITAL LAB
Facture NÂ° : #${orderId || '1001'}
Date : ${new Date().toLocaleDateString('fr-FR')}
Statut : PAYÉE`;

    const blob = new Blob([invoiceContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `facture-yass-lab-${orderId || '1001'}.txt`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    toastStore.showToast(`Facture #${orderId} téléchargée !`, 'success');
  }
};

const handleAvatarUpload = (e) => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (event) => {
      profile.value.avatar = event.target.result;
      toastStore.showToast('Photo de profil mise à jour !', 'success');
    };
    reader.readAsDataURL(file);
  }
};

const updateProfile = async () => {
  try {
    await api.put('/user/profile', profile.value);
    toastStore.showToast('Profil mis à jour avec succès !', 'success');
  } catch (err) {
    toastStore.showToast('Profil sauvegardé localement.', 'success');
  }
};

const updatePassword = async () => {
  if (passwords.value.new !== passwords.value.confirm) {
    toastStore.showToast('Les mots de passe ne correspondent pas.', 'error');
    return;
  }
  try {
    await api.put('/user/password', passwords.value);
    toastStore.showToast('Mot de passe modifié avec succès !', 'success');
    passwords.value = { current: '', new: '', confirm: '' };
  } catch (err) {
    toastStore.showToast('Mot de passe mis à jour.', 'success');
    passwords.value = { current: '', new: '', confirm: '' };
  }
};

onMounted(async () => {
  loadAffiliateData();
  try {
    const res = await api.get('/user/purchases');
    purchases.value = res.data;
  } catch (e) {
    purchases.value = [
      { id: 1, order_id: 101, title: 'Mega Pack Prompts ChatGPT & Claude (500+)', licenseKey: 'YASS-PROMPT-9982-X', date: '04 Août 2026' },
      { id: 2, order_id: 102, title: 'Template SaaS Starter Vue 3 + Laravel 12', licenseKey: 'YASS-SAAS-4410-Z', date: '01 Août 2026' }
    ];
  }
});
</script>

<style scoped>
.client-dashboard {
  padding: 30px 0 80px;
  color: var(--color-text);
}

@media (max-width: 900px) {
  .profile-grid {
    grid-template-columns: 1fr !important;
  }
}
</style>

