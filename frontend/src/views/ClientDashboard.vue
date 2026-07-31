<template>
  <div class="client-dashboard container" style="margin-top: 30px;">
    
    <!-- Welcome Header Banner -->
    <div class="glass flex justify-between items-center mb-8 fade-in" style="padding: 36px 40px; border-radius: 24px; background: linear-gradient(135deg, rgba(15, 23, 42, 0.94), rgba(5, 8, 17, 0.96)); backdrop-filter: blur(20px); border: 1px solid var(--color-accent); box-shadow: 0 15px 45px rgba(0,0,0,0.5); flex-wrap: wrap; gap: 20px; color: #FFFFFF;">
      
      <div class="flex items-center gap-4">
        <div style="position: relative; width: 74px; height: 74px; border-radius: 50%; overflow: hidden; border: 2px solid var(--color-accent); flex-shrink: 0; box-shadow: 0 4px 16px rgba(212,175,55,0.45);">
          <img v-if="profile.avatar" :src="profile.avatar" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;" />
          <div v-else style="width: 100%; height: 100%; background: linear-gradient(135deg, #F0CC55, #D4AF37); color: #050811; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.8rem;">
            {{ profile.name ? profile.name.charAt(0).toUpperCase() : 'U' }}
          </div>
        </div>

        <div>
          <div class="flex items-center gap-2 mb-1" style="flex-wrap: wrap;">
            <h1 style="font-size: 1.9rem; margin: 0; font-weight: 800; color: #FFFFFF; font-family: var(--font-heading);">
              {{ profile.name || 'Espace Client' }}
            </h1>
            <span style="background: rgba(212,175,55,0.2); color: #F0CC55; border: 1px solid rgba(212,175,55,0.4); padding: 2px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
              ⭐ Membre Client VIP
            </span>
          </div>
          <p style="color: rgba(255, 255, 255, 0.85); font-size: 0.95rem; margin: 0;">
            {{ profile.email }} • Retrouvez vos achats, vos clés de licence et gérez votre profil.
          </p>
        </div>
      </div>

      <div class="flex gap-2" style="flex-wrap: wrap;">
        <button @click="activeSection = 'purchases'" class="btn flex items-center gap-2" :style="activeSection === 'purchases' ? 'background: linear-gradient(135deg, #F0CC55, #D4AF37); color: #050811 !important; font-weight: 800; border: none; box-shadow: 0 4px 14px rgba(212,175,55,0.4);' : 'background: rgba(255,255,255,0.14); color: #FFFFFF !important; font-weight: 700; border: 1px solid rgba(255,255,255,0.3);'" style="font-size: 0.88rem; padding: 10px 18px;">
          <Package :size="16" /> Mes Achats
        </button>
        <button @click="activeSection = 'profile'" class="btn flex items-center gap-2" :style="activeSection === 'profile' ? 'background: linear-gradient(135deg, #F0CC55, #D4AF37); color: #050811 !important; font-weight: 800; border: none; box-shadow: 0 4px 14px rgba(212,175,55,0.4);' : 'background: rgba(255,255,255,0.14); color: #FFFFFF !important; font-weight: 700; border: 1px solid rgba(255,255,255,0.3);'" style="font-size: 0.88rem; padding: 10px 18px;">
          <Settings :size="16" /> Mon Profil & Sécurité
        </button>
        <button @click="handleLogout" class="btn flex items-center gap-2" style="font-size: 0.88rem; padding: 10px 18px; background: rgba(239,68,68,0.2); border: 1px solid rgba(239,68,68,0.45); color: #FCA5A5 !important; font-weight: 700;">
          <LogOut :size="16" /> Déconnexion
        </button>
      </div>

    </div>

    <!-- SECTION 1 : ACHATS, FICHIERS & AFFILIATION -->
    <div v-if="activeSection === 'purchases'">
      
      <!-- Mes Produits Numériques -->
      <div class="glass" style="padding: 36px; border-radius: 24px; margin-bottom: 30px;">
        <div class="flex justify-between items-center mb-6">
          <h2 class="flex items-center gap-2" style="font-size: 1.4rem; font-weight: 700;"><Package :size="24" style="color: var(--color-accent);" /> Mes Produits Numériques & Licences</h2>
          <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 4px 14px; border-radius: 999px; font-weight: 700; font-size: 0.85rem;">
            {{ purchases.length }} produit(s) acquis
          </span>
        </div>

        <!-- Aucun achat -->
        <div v-if="purchases.length === 0" class="text-center" style="padding: 60px;">
          <div style="display: flex; justify-content: center; margin-bottom: 16px; color: var(--color-accent);">
            <ShoppingBag :size="56" />
          </div>
          <h3 class="mb-2">Vous n'avez pas encore effectué d'achats</h3>
          <p style="color: var(--color-text-light); margin-bottom: 24px;">Explorez nos catalogues pour trouver les meilleurs outils, guides et templates.</p>
          <router-link to="/products" class="btn btn-primary">Découvrir le catalogue →</router-link>
        </div>

        <!-- Liste des achats -->
        <div v-else class="grid" style="grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px;">
          <div v-for="purchase in purchases" :key="purchase.id" class="card glass flex flex-col justify-between" style="padding: 24px; border-radius: 18px; border: 1px solid var(--color-border); transition: transform 0.25s;">
            <div>
              <div class="flex justify-between items-center mb-3">
                <span style="font-size: 0.78rem; background: rgba(34,197,94,0.15); color: #22c55e; padding: 4px 12px; border-radius: 999px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                  <CheckCircle2 :size="13" /> Licence Active à vie
                </span>
                <span style="font-size: 0.82rem; color: var(--color-text-light); display: inline-flex; align-items: center; gap: 4px;">
                  <Calendar :size="13" /> {{ purchase.date }}
                </span>
              </div>
              <h3 class="mb-2" style="font-size: 1.15rem; font-weight: 700;">{{ purchase.title }}</h3>
              
              <!-- Key License Box -->
              <div class="mb-4" style="background: rgba(0,0,0,0.12); padding: 10px 14px; border-radius: 10px; border: 1px dashed var(--color-accent); font-family: monospace; font-size: 0.82rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--color-accent); font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                  <Key :size="14" /> {{ purchase.licenseKey }}
                </span>
                <button @click="copyLicenseKey(purchase.licenseKey)" style="background: none; border: none; color: var(--color-text-light); cursor: pointer; font-size: 0.75rem; font-weight: 700;" title="Copier la clé">
                  <Copy :size="14" />
                </button>
              </div>
            </div>

            <div class="flex gap-2">
              <button @click="downloadFile(purchase)" class="btn btn-primary flex items-center justify-center gap-2" style="flex: 1; padding: 10px 14px; font-size: 0.9rem;">
                <Download :size="16" /> Fichier (.zip)
              </button>
              <button @click="downloadInvoice(purchase.order_id)" class="btn btn-secondary flex items-center justify-center gap-2" style="flex: 1; padding: 10px 14px; font-size: 0.9rem;">
                <FileText :size="16" /> Facture PDF
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Produits Favoris / Wishlist -->
      <div v-if="wishlist.items.length > 0" class="glass" style="padding: 36px; border-radius: 24px; margin-bottom: 30px;">
        <div class="flex justify-between items-center mb-6">
          <h2 class="flex items-center gap-2" style="font-size: 1.4rem; font-weight: 700;"><Heart :size="24" style="color: #EF4444;" /> Mes Produits Favoris ({{ wishlist.items.length }})</h2>
        </div>

        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
          <div v-for="fav in wishlist.items" :key="fav.id" class="card glass p-4 flex flex-col justify-between" style="border-radius: 16px;">
            <div>
              <h4 class="mb-2" style="font-size: 1.05rem; font-weight: 700;">{{ fav.title }}</h4>
              <p style="color: var(--color-accent); font-weight: 800; font-size: 1.2rem; margin-bottom: 14px;">{{ fav.price }} €</p>
            </div>
            <div class="flex gap-2">
              <router-link :to="`/products/${fav.id}`" class="btn btn-primary" style="flex: 1; padding: 8px 14px; font-size: 0.88rem; text-align: center;">
                Commander →
              </router-link>
              <button @click="wishlist.toggleWishlist(fav)" class="btn btn-secondary flex items-center justify-center" style="padding: 8px 12px; font-size: 0.88rem; color: #ef4444;" title="Retirer des favoris">
                <Trash2 :size="16" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Programme de Parrainage & Affiliation -->
      <div class="glass" style="padding: 36px; border-radius: 24px; margin-bottom: 40px;">
        <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 16px;">
          <div>
            <h2 class="flex items-center gap-2" style="font-size: 1.4rem; font-weight: 700;"><Users :size="24" style="color: var(--color-accent);" /> Programme de Parrainage & Affiliation</h2>
            <p style="color: var(--color-text-light); font-size: 0.92rem;">Gagnez 10% de commission sur chaque achat effectué par vos filleuls.</p>
          </div>
          <div style="background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); padding: 8px 20px; border-radius: 999px; font-weight: 800; font-size: 0.98rem;">
            Solde crédité : 15.00 €
          </div>
        </div>

        <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 24px; flex-wrap: wrap;">
          <div style="background: rgba(0,0,0,0.06); padding: 24px; border-radius: 18px; border: 1px solid var(--color-border);">
            <label style="display: block; margin-bottom: 8px; font-weight: 700; font-size: 0.92rem;">Votre Lien de Parrainage Unique</label>
            <div class="flex gap-2 mb-4">
              <input 
                :value="referralLink" 
                readonly 
                type="text" 
                @click="copyReferralLink"
                title="Cliquer pour copier"
                style="flex: 1; padding: 11px 14px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-family: monospace; font-size: 0.9rem; cursor: pointer;"
              />
              <button @click="copyReferralLink" class="btn btn-primary flex items-center gap-2" style="white-space: nowrap; padding: 11px 18px;">
                <Copy :size="16" /> Copier le lien
              </button>
            </div>

            <!-- Social Share Buttons -->
            <div class="flex items-center gap-2" style="flex-wrap: wrap;">
              <span style="font-size: 0.82rem; color: var(--color-text-light); font-weight: 600; margin-right: 4px;">Partager rapidement :</span>
              <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent('Rejoignez Yass Digital Lab avec mon lien de parrainage : ' + referralLink)" target="_blank" class="btn btn-secondary flex items-center gap-1" style="font-size: 0.8rem; padding: 6px 12px; color: #25D366; border-color: rgba(37,211,102,0.3);">
                💬 WhatsApp
              </a>
              <a :href="'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(referralLink)" target="_blank" class="btn btn-secondary flex items-center gap-1" style="font-size: 0.8rem; padding: 6px 12px; color: #0A66C2; border-color: rgba(10,102,194,0.3);">
                💼 LinkedIn
              </a>
              <a :href="'mailto:?subject=' + encodeURIComponent('Invitation Yass Digital Lab') + '&body=' + encodeURIComponent('Découvrez Yass Digital Lab et profitez d\'outils numériques exceptionnels : ' + referralLink)" class="btn btn-secondary flex items-center gap-1" style="font-size: 0.8rem; padding: 6px 12px; color: var(--color-accent); border-color: rgba(212,175,55,0.3);">
                📧 Email
              </a>
            </div>
          </div>

          <div style="background: rgba(212,175,55,0.08); padding: 24px; border-radius: 18px; border: 1px solid rgba(212,175,55,0.25); display: flex; flex-direction: column; justify-content: center;">
            <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 8px;">Vos Statistiques</h4>
            <p style="font-size: 0.88rem; color: var(--color-text-light); margin: 0 0 6px;">• <strong>3</strong> Filleuls inscrits</p>
            <p style="font-size: 0.88rem; color: var(--color-text-light); margin: 0 0 6px;">• <strong>2</strong> Achats validés</p>
            <p style="font-size: 0.88rem; color: #10b981; margin: 0; font-weight: 700;">• <strong>15.00 €</strong> de gains générés</p>
          </div>
        </div>
      </div>

    </div>

    <!-- SECTION 2 : ÉDITION DU PROFIL & SÉCURITÉ (DISPOSITION DEUX COLONNES) -->
    <div v-else class="fade-in mb-10">
      
      <!-- Top Title Banner -->
      <div class="glass flex justify-between items-center mb-6 p-6" style="border-radius: 20px; flex-wrap: wrap; gap: 12px;">
        <h2 class="flex items-center gap-2" style="font-size: 1.5rem; font-weight: 700; color: var(--color-primary); margin: 0;">
          <Settings :size="24" style="color: var(--color-accent);" /> Mes Coordonnées & Sécurité
        </h2>
        <span class="flex items-center gap-1" style="font-size: 0.82rem; color: var(--color-success); font-weight: 700; background: rgba(16,185,129,0.12); padding: 5px 14px; border-radius: 999px;">
          <ShieldCheck :size="15" /> Compte Vérifié & Protégé SSL 256-bit
        </span>
      </div>

      <!-- 2-COLUMN GRID LAYOUT -->
      <div class="grid profile-grid" style="grid-template-columns: 1.2fr 1fr; gap: 28px; align-items: start;">
        
        <!-- LEFT COLUMN: Coordonnées & Photo -->
        <div class="glass" style="padding: 32px; border-radius: 24px;">
          <h3 class="mb-4 flex items-center gap-2" style="font-size: 1.15rem; font-weight: 700;">
            <User :size="18" style="color: var(--color-accent);" /> Informations Personnelles
          </h3>

          <form @submit.prevent="updateProfile" class="flex flex-col gap-4">
            
            <!-- Avatar Photo Uploader -->
            <div class="flex items-center gap-4 p-4 mb-2" style="background: rgba(212,175,55,0.06); border: 1px solid rgba(212,175,55,0.22); border-radius: 16px;">
              <div style="position: relative; width: 68px; height: 68px; border-radius: 50%; overflow: hidden; border: 2px solid var(--color-accent); flex-shrink: 0; background: var(--color-bg);">
                <img v-if="profile.avatar" :src="profile.avatar" alt="Avatar Preview" style="width: 100%; height: 100%; object-fit: cover;" />
                <div v-else style="width: 100%; height: 100%; background: linear-gradient(135deg, #F0CC55, #D4AF37); color: #050811; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.6rem;">
                  {{ profile.name ? profile.name.charAt(0).toUpperCase() : 'U' }}
                </div>
              </div>

              <div style="flex: 1;">
                <label style="display: block; font-weight: 700; font-size: 0.9rem; margin-bottom: 2px; color: var(--color-text);">Photo de Profil</label>
                <p style="font-size: 0.78rem; color: var(--color-text-light); margin-bottom: 8px;">Téléchargez une photo ou une URL d'image.</p>
                <div class="flex gap-2" style="flex-wrap: wrap;">
                  <label class="btn btn-secondary flex items-center gap-2" style="font-size: 0.8rem; padding: 6px 12px; cursor: pointer;">
                    <Camera :size="14" /> Charger
                    <input type="file" accept="image/*" @change="handleAvatarUpload" style="display: none;" />
                  </label>
                  <input v-model="profile.avatar" type="url" placeholder="URL d'image..." style="flex: 1; min-width: 160px; padding: 6px 10px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
                </div>
              </div>
            </div>

            <!-- Form fields -->
            <div>
              <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.9rem;">Nom complet *</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                  <User :size="16" />
                </div>
                <input v-model="profile.name" type="text" required style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" />
              </div>
            </div>

            <div>
              <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.9rem;">Adresse Email *</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                  <Mail :size="16" />
                </div>
                <input v-model="profile.email" type="email" required style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" />
              </div>
            </div>

            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 14px;">
              <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.9rem;">Téléphone</label>
                <div style="position: relative;">
                  <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                    <Phone :size="16" />
                  </div>
                  <input v-model="profile.phone" type="tel" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" placeholder="+33 6 12 34 56 78" />
                </div>
              </div>

              <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.9rem;">Entreprise</label>
                <div style="position: relative;">
                  <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                    <Building :size="16" />
                  </div>
                  <input v-model="profile.company" type="text" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" placeholder="Société" />
                </div>
              </div>
            </div>

            <div>
              <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.9rem;">Adresse de facturation</label>
              <div style="position: relative;">
                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                  <MapPin :size="16" />
                </div>
                <input v-model="profile.address" type="text" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" placeholder="Paris, France" />
              </div>
            </div>

            <button type="submit" class="btn btn-primary flex items-center justify-center gap-2" style="margin-top: 8px; padding: 11px 24px; font-size: 0.95rem; font-weight: 700;">
              <Check :size="18" /> Enregistrer les coordonnées
            </button>
          </form>
        </div>

        <!-- RIGHT COLUMN: Sécurité, Mot de Passe & Sessions -->
        <div class="flex flex-col gap-6">
          
          <!-- Modification du mot de passe -->
          <div class="glass" style="padding: 32px; border-radius: 24px;">
            <h3 class="mb-4 flex items-center gap-2" style="font-size: 1.15rem; font-weight: 700;">
              <Lock :size="18" style="color: var(--color-accent);" /> Sécurité & Mot de Passe
            </h3>
            
            <form @submit.prevent="updatePassword" class="flex flex-col gap-4">
              <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.9rem;">Nouveau mot de passe</label>
                <div style="position: relative;">
                  <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                    <Lock :size="16" />
                  </div>
                  <input v-model="profile.newPassword" type="password" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" placeholder="••••••••" />
                </div>
              </div>

              <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.9rem;">Confirmation du mot de passe</label>
                <div style="position: relative;">
                  <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-light); pointer-events: none;">
                    <Lock :size="16" />
                  </div>
                  <input v-model="profile.confirmPassword" type="password" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.92rem;" placeholder="••••••••" />
                </div>
              </div>

              <!-- Password Strength Indicator -->
              <div v-if="profile.newPassword" class="mt-1">
                <div style="height: 4px; border-radius: 999px; background: rgba(0,0,0,0.1); overflow: hidden; display: flex;">
                  <div :style="{ width: passwordStrength.pct + '%', background: passwordStrength.color }" style="height: 100%; transition: all 0.3s;"></div>
                </div>
                <span style="font-size: 0.76rem; font-weight: 600; display: block; margin-top: 3px;" :style="{ color: passwordStrength.color }">
                  Force : {{ passwordStrength.label }}
                </span>
              </div>

              <button type="submit" class="btn btn-secondary flex items-center justify-center gap-2" style="margin-top: 6px; padding: 10px 20px; font-size: 0.92rem; font-weight: 700; border-color: var(--color-accent); color: var(--color-accent);">
                <Key :size="16" /> Mettre à jour le mot de passe
              </button>
            </form>
          </div>

          <!-- Security Log & Sessions Card -->
          <div class="glass" style="padding: 28px; border-radius: 24px;">
            <h4 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
              <ShieldCheck :size="18" style="color: var(--color-accent);" /> Appareils & Sessions Connectées
            </h4>
            
            <div class="flex flex-col gap-3">
              <div class="flex justify-between items-center p-3" style="background: rgba(0,0,0,0.05); border-radius: 12px; border: 1px solid var(--color-border); font-size: 0.84rem;">
                <div class="flex items-center gap-3">
                  <span style="color: var(--color-success); font-weight: 800;">●</span>
                  <div>
                    <strong style="display: block; color: var(--color-text);">Session Actuelle — Chrome (Windows 11)</strong>
                    <span style="color: var(--color-text-light); font-size: 0.78rem;">Paris, France • IP: 192.168.1.45</span>
                  </div>
                </div>
                <span style="background: rgba(16,185,129,0.15); color: #10b981; font-weight: 700; padding: 2px 10px; border-radius: 999px; font-size: 0.72rem;">Actif</span>
              </div>

              <div class="flex justify-between items-center p-3" style="background: rgba(0,0,0,0.05); border-radius: 12px; border: 1px solid var(--color-border); font-size: 0.84rem;">
                <div class="flex items-center gap-3">
                  <span style="color: var(--color-text-light);">●</span>
                  <div>
                    <strong style="display: block; color: var(--color-text);">Session Mobile — Safari (iOS 17)</strong>
                    <span style="color: var(--color-text-light); font-size: 0.78rem;">Lyon, France • Hier à 09:14</span>
                  </div>
                </div>
                <button @click="revokeSession" style="background: none; border: none; color: #ef4444; font-size: 0.78rem; font-weight: 700; cursor: pointer; text-decoration: underline;">
                  Déconnecter
                </button>
              </div>
            </div>
          </div>

        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useWishlistStore } from '../stores/wishlist';
import { useToastStore } from '../stores/toast';
import { useRouter } from 'vue-router';
import { ref, computed, onMounted } from 'vue';
import { 
  User, 
  Mail,
  Phone,
  Building,
  MapPin,
  Package, 
  Settings, 
  LogOut, 
  ShoppingBag, 
  Download, 
  FileText, 
  Heart, 
  Key, 
  CheckCircle2, 
  Calendar, 
  Trash2, 
  Users, 
  Copy, 
  Lock, 
  Check, 
  Camera, 
  ShieldCheck 
} from 'lucide-vue-next';

const auth = useAuthStore();
const wishlist = useWishlistStore();
const toastStore = useToastStore();
const router = useRouter();

const activeSection = ref('profile'); // 'purchases' or 'profile'

const profile = ref({
  name: '',
  email: '',
  phone: '',
  company: '',
  address: '',
  avatar: '',
  newPassword: '',
  confirmPassword: ''
});

const passwordStrength = computed(() => {
  const p = profile.value.newPassword;
  if (!p) return { label: 'Inexistant', pct: 0, color: '#e2e8f0' };
  if (p.length < 6) return { label: 'Faible', pct: 33, color: '#ef4444' };
  if (p.length < 10 || !/\d/.test(p)) return { label: 'Moyen', pct: 66, color: '#f59e0b' };
  return { label: 'Très Fort 💪', pct: 100, color: '#10b981' };
});

const purchases = ref([
  {
    id: 101,
    order_id: 1001,
    title: 'Template SaaS Starter Vue 3 + Laravel 12',
    date: '28/07/2026',
    licenseKey: 'YASS-SaaS-99201-PRO',
    downloadUrl: '/downloads/saas-template.zip'
  },
  {
    id: 102,
    order_id: 1002,
    title: 'Mega Pack Prompts ChatGPT & Claude 3.5',
    date: '25/07/2026',
    licenseKey: 'YASS-PROMPTS-44102-VIP',
    downloadUrl: '/downloads/prompts-pack.zip'
  }
]);

const referralLink = computed(() => {
  const code = profile.value.name ? profile.value.name.replace(/\s+/g, '').toUpperCase().substring(0, 8) : 'CLIENT';
  return `https://yassdigitallab.com?ref=${code}`;
});

const copyLicenseKey = async (key) => {
  try {
    await navigator.clipboard.writeText(key);
    toastStore.showToast(`Clé de licence ${key} copié dans le presse-papiers !`, 'success');
  } catch (e) {
    toastStore.showToast(`Clé : ${key}`, 'info');
  }
};

const handleAvatarUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = (event) => {
    profile.value.avatar = event.target.result;
    toastStore.showToast('Photo de profil prévisualisée ! Pensez à enregistrer.', 'info');
  };
  reader.readAsDataURL(file);
};

const revokeSession = () => {
  toastStore.showToast('Session mobile déconnectée avec succès !', 'info');
};

const updateProfile = async () => {
  try {
    toastStore.showToast('Informations personnelles enregistrées avec succès !', 'success');
    if (auth.user) {
      auth.user.name = profile.value.name;
      auth.user.email = profile.value.email;
      localStorage.setItem('user', JSON.stringify(auth.user));
    }
  } catch (error) {
    toastStore.showToast('Erreur lors de la mise à jour du profil.', 'error');
  }
};

const updatePassword = async () => {
  if (!profile.value.newPassword) {
    toastStore.showToast('Veuillez saisir un nouveau mot de passe.', 'info');
    return;
  }
  if (profile.value.newPassword !== profile.value.confirmPassword) {
    toastStore.showToast('Les mots de passe ne correspondent pas.', 'error');
    return;
  }

  toastStore.showToast('Mot de passe mis à jour avec succès !', 'success');
  profile.value.newPassword = '';
  profile.value.confirmPassword = '';
};

const downloadFile = (purchase) => {
  toastStore.showToast(`Téléchargement de ${purchase.title} démarré...`, 'info');
  const a = document.createElement('a');
  a.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(`Téléchargement Officiel Yass Digital Lab\nProduit: ${purchase.title}\nClé: ${purchase.licenseKey}\nMerci pour votre achat !`);
  a.download = `${purchase.title.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_yassdigitallab.txt`;
  document.body.appendChild(a);
  a.click();
  a.remove();
};

const downloadInvoice = (orderId) => {
  const invoiceHTML = `<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Facture #FA-00000${orderId}</title>
  <style>
    body { font-family: 'Helvetica', sans-serif; padding: 40px; color: #0f172a; }
    .header { display: flex; justify-content: space-between; border-bottom: 2px solid #d4af37; padding-bottom: 20px; }
    .title { font-size: 24px; font-weight: bold; color: #0f172a; }
    .table { width: 100%; border-collapse: collapse; margin-top: 30px; }
    .table th, .table td { padding: 12px; border: 1px solid #e2e8f0; text-align: left; }
    .table th { background: #f8fafc; }
    .total { text-align: right; margin-top: 20px; font-size: 18px; font-weight: bold; color: #d4af37; }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <div class="title">Yass Digital Lab</div>
      <p>Outils Numériques Intelligents</p>
    </div>
    <div>
      <h3>FACTURE #FA-00000${orderId}</h3>
      <p>Date: ${new Date().toLocaleDateString('fr-FR')}</p>
    </div>
  </div>
  <div style="margin-top: 30px;">
    <p><strong>Client:</strong> ${profile.value.name || 'Client'}</p>
    <p><strong>Email:</strong> ${profile.value.email || 'client@yass.com'}</p>
  </div>
  <table class="table">
    <thead>
      <tr><th>Description</th><th>Qté</th><th>Prix unitaire</th><th>Total</th></tr>
    </thead>
    <tbody>
      <tr><td>Achat numérique Yass Digital Lab (Licence VIP)</td><td>1</td><td>49.00 €</td><td>49.00 €</td></tr>
    </tbody>
  </table>
  <div class="total">Total Payé: 49.00 €</div>
</body>
</html>`;

  const blob = new Blob([invoiceHTML], { type: 'text/html;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `Facture_FA-00000${orderId}_YassDigitalLab.html`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
  toastStore.showToast(`Facture #FA-00000${orderId} téléchargée avec succès !`, 'success');
};

const copyReferralLink = async () => {
  try {
    await navigator.clipboard.writeText(referralLink.value);
    toastStore.showToast('Lien de parrainage copié avec succès !', 'success');
  } catch (err) {
    toastStore.showToast('Lien de parrainage copié !', 'success');
  }
};

const handleLogout = async () => {
  await auth.logout();
  router.push('/');
};

onMounted(() => {
  if (auth.user) {
    profile.value.name = auth.user.name || '';
    profile.value.email = auth.user.email || '';
    profile.value.phone = auth.user.phone || '';
    profile.value.company = auth.user.company || '';
    profile.value.address = auth.user.address || '';
    profile.value.avatar = auth.user.avatar || '';
  }
});
</script>

<style scoped>
@media (max-width: 900px) {
  .profile-grid {
    grid-template-columns: 1fr !important;
  }
}
.card:hover {
  transform: translateY(-4px);
  border-color: var(--color-accent) !important;
}
</style>
