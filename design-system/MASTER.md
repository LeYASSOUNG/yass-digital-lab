# Design System Master File — Yass Digital Lab

> **Source of Truth** pour l'interface UI/UX de Yass Digital Lab.
> Toutes les pages (Landing, Catalogue, Détail Produit, Checkout, Dashboards) doivent se conformer à ce système de design.

---

## 🏛️ Identité & Vision Produit

- **Nom Produit :** Yass Digital Lab
- **Type :** Marketplace E-commerce de Produits & Services Digitaux (Templates Vue 3/Laravel, Packs de Prompts IA, Scripts, Prestations de Développement sur-mesure)
- **Cible :** Professionnels, startups et créateurs en Côte d'Ivoire & Afrique de l'Ouest
- **Ton & Aesthetic :** Professionnel, moderne, digne de confiance (*Trust & Authority* + *SaaS Premium*)
- **Stack Technologique :** Vue 3 SPA + Tailwind CSS + Lucide Icons (Frontend), API Laravel 12 (Backend)

---

## 🎨 System Tokens & Palette de Couleurs

### Palette Principale ("Trust & Authority + SaaS Accent")

| Rôle UI | Couleur Hex | Variable CSS | Usage / Intent |
| --- | --- | --- | --- |
| **Primary** | `#1E40AF` *(Blue 800)* | `--color-primary` | Titres, headers, marque principale, badges d'autorité |
| **Secondary** | `#3B82F6` *(Blue 500)* | `--color-secondary` | Éléments interactifs secondaires, liens, bordures actives |
| **Accent / CTA** | `#16A34A` *(Emerald 600)* | `--color-accent` | Boutons d'action prioritaires (Acheter, Panier, Devis) |
| **Brand Accent** | `#D4AF37` *(Gold Premium)* | `--color-gold` | Badges VIP, certifications, highlights exclusifs |
| **Background (Light)** | `#F8FAFC` *(Slate 50)* | `--color-background` | Fond général de page en mode clair |
| **Background (Dark)** | `#0B0F19` *(Obsidian)* | `--color-bg-dark` | Fond principal en mode sombre |
| **Surface Card (Light)** | `#FFFFFF` *(Pure White)* | `--color-card` | Fond de carte et modales |
| **Surface Card (Dark)** | `#111827` *(Slate 900)* | `--color-card-dark` | Fond de carte et modales en mode sombre |
| **Text Primary** | `#0F172A` *(Slate 900)* | `--color-text` | Texte principal lisible (contraste AAA > 7:1) |
| **Text Secondary** | `#475569` *(Slate 600)* | `--color-text-muted` | Sous-titres, labels, descriptions |
| **Border** | `#E2E8F0` / `rgba(255,255,255,0.1)` | `--color-border` | Bordures fines et séparateurs |
| **Success** | `#10B981` *(Emerald)* | `--color-success` | Confirmations, garanties, statut actif |
| **Destructive** | `#DC2626` *(Red 600)* | `--color-destructive` | Suppression, erreurs, alertes |

---

## ✍️ Typographie & Échelle

- **Police de Titres (Headings) :** `Poppins`, `Plus Jakarta Sans`, sans-serif (Graisses: 600, 700, 800)
- **Police de Corps (Body/UI) :** `Open Sans`, `Inter`, system-ui, sans-serif (Graisses: 400, 500, 600)
- **Google Fonts Import :**

```css
@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap');
```

---

## 🧩 Spécification des Composants UI (Tailwind & CSS)

### 1. Boutons (CTA & Actions)

- **Bouton Primaire (CTA Conversion) :**
  - Gradient Indigo/Emerald réactif, coins arrondis `rounded-xl`, padding `px-6 py-3`, police `font-bold`, ombre portée `shadow-md hover:shadow-lg`, animation `transition-all duration-200 hover:-translate-y-0.5`.
- **Bouton Secondaire (Navigation / Filtre) :**
  - Fond `bg-card`, bordure fine `border border-slate-200 dark:border-slate-800`, texte `text-slate-800 dark:text-slate-100`, hover `hover:border-blue-600 hover:text-blue-600`.

### 2. Cartes de Produits / Services (Marketplace Cards)

- Fond Glassmorphic léger (`backdrop-blur-md bg-white/80 dark:bg-slate-900/80`), bordure 1px `border-slate-200/80 dark:border-slate-800/80`.
- Effet au survol : élévation douce `hover:-translate-y-1`, halo réactif `hover:shadow-indigo-500/10 hover:border-indigo-500/30`.
- Badges type de produit en haut à droite, prix net mis en valeur, boutons d'action rapide (Panier / Détails).

### 3. Barre de Recherche & Filtres (Marketplace Pattern)

- Barre de recherche proéminente au centre du Hero avec icône Lucide `Search` et bouton "Rechercher".
- Filtres pilules (`badge-pill`) cliquables pour basculer de catégorie avec indicateur actif luminescent.

---

## 🚫 Anti-Patterns Strictement Interdits

1. ❌ **Pas d'émojis comme icônes d'interface** — Utiliser exclusivement des icônes SVG vectorielles Lucide (`lucide-vue-next`).
2. ❌ **Pas d'éléments cliquables sans `cursor-pointer`** — Tout bouton, lien, carte ou onglet interactif doit avoir un curseur pointeur.
3. ❌ **Pas de contrastes de texte inférieurs à 4.5:1 (WCAG AA)** — Le texte doit être parfaitement lisible en mode clair et sombre.
4. ❌ **Pas de changements d'état sans transition** — Les hovers, focus et ouvertures de modales doivent inclure une transition fluide (150ms à 300ms).
5. ❌ **Pas de dégradés roses/violets néon agressifs de type "IA générique"** — Privilégier une palette d'autorité professionnelle (Bleu Indigo, Émeraude, Slate, Or accent).

---

## 📋 Pre-Delivery Checklist (Checklist de Validation UI/UX)

- [x] Pas d'émojis bruts utilisés à la place des icônes SVG.
- [x] Contraste des couleurs conforme aux normes WCAG AA (ratio minimum 4.5:1).
- [x] Etats de survol (`hover`) fluides sur tous les boutons, cartes et liens.
- [x] Etats de focus (`focus-visible`) visibles au clavier pour l'accessibilité.
- [x] Respect de la règle `prefers-reduced-motion`.
- [x] Adaptabilité responsive complète (375px mobile, 768px tablette, 1024px ordinateur, 1440px grand écran).
- [x] Conservation intégrale des fonctionnalités backend et du state Pinia/Vue-Router.
