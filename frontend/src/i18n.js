import { ref } from 'vue';

export const currentLang = ref(localStorage.getItem('lang') || 'fr');

export const translations = {
  fr: {
    // Nav & General
    home: 'Accueil',
    products: 'Produits',
    services: 'Services',
    courses: 'Formations',
    blog: 'Blog',
    about: 'À propos',
    contact: 'Contact',
    cart: 'Panier',
    mySpace: 'Mon Espace',
    login: 'Connexion',
    logout: 'Déconnexion',
    promoNotice: 'Promo d\'ouverture Yass Digital Lab : -20% avec le code YASS20 sur tous les packs !',
    tagline: 'Outils Numériques Intelligents',
    footerRights: 'Tous droits réservés.',
    newsletterTitle: 'Newsletter Privée',
    newsletterSub: 'Recevez nos derniers prompts IA et offres exclusives directement par email.',
    subscribe: 'S\'inscrire',

    // Home Page
    heroBadge: 'Solutions Numériques & Intelligence Artificielle',
    heroTitle: 'Des outils numériques intelligents pour aller plus loin.',
    heroSubtitle: 'Découvrez nos packs de prompts optimisés, templates d\'applications SaaS, '
      + 'e-books et prestations sur mesure par Yass Digital Lab.',
    exploreCatalog: 'Explorer le catalogue',
    requestQuote: 'Demander un devis',
    featuredProducts: 'Produits Phares',
    featuredProductsSub: 'Une sélection de nos meilleures ressources prêtes à booster votre productivité.',
    ourServices: 'Prestations & Devis',
    ourServicesSub: 'Des services sur mesure pour concrétiser vos projets digitaux les plus ambitieux.',
    viewDetails: 'Détails →',
    learnMore: 'En savoir plus →',

    // Products Page
    catalogTitle: 'Nos Produits Numériques',
    catalogSub: 'Des ressources de haute qualité pour accélérer vos projets.',
    searchPlaceholder: 'Rechercher un produit...',
    sortDefault: 'Tri par défaut',
    sortPriceAsc: 'Prix : Croissant (▲)',
    sortPriceDesc: 'Prix : Décroissant (▼)',
    sortNameAsc: 'Nom : A à Z',
    all: 'Tous',
    addToCart: 'Ajouter au Panier',
    liveDemo: 'Démo en direct',

    // Services Page
    servicesTitle: 'Nos Services & Solutions Sur-Mesure',
    servicesSub: 'De la conception à l\'intégration IA, nous réalisons vos projets digitaux d\'exception.',
    requestQuoteBtn: 'ðŸ“ Demander un devis',
    customQuoteModalTitle: 'ðŸ“ Demande de Devis sur Mesure',
    fullName: 'Nom complet',
    email: 'Adresse Email',
    projectType: 'Type de projet',
    projectDetails: 'Détails de votre besoin',
    sendRequest: 'Envoyer la demande →',

    // About Page
    aboutTitle: 'À Propos du Créateur',
    aboutSub: 'Passionné par l\'Intelligence Artificielle et le développement Web haute performance.',
    bioTitle: 'Diarrassouba Yassoungo Youssouf',
    bioSub: 'Développeur Full Stack & Spécialiste IA',
    bioText: 'Fondateur de Yass Digital Lab, je conçois des solutions numériques modernes et intelligentes '
      + 'combinant architectures web robustes (Laravel, Vue.js, PostgreSQL) et puissance des modèles d\'IA.',

    // Contact Page
    contactTitle: 'Contactez-nous',
    contactSub: 'Une question ? Un projet ? Notre équipe vous répond sous 24h.',
    subject: 'Sujet',
    message: 'Message',
    sendMessage: 'Envoyer le message →'
  },

  en: {
    // Nav & General
    home: 'Home',
    products: 'Products',
    services: 'Services',
    courses: 'Courses',
    blog: 'Blog',
    about: 'About Us',
    contact: 'Contact',
    cart: 'Cart',
    mySpace: 'My Account',
    login: 'Login',
    logout: 'Logout',
    promoNotice: 'Yass Digital Lab Opening Sale: 20% OFF with code YASS20 on all packs!',
    tagline: 'Smart Digital Tools',
    footerRights: 'All rights reserved.',
    newsletterTitle: 'Private Newsletter',
    newsletterSub: 'Receive our latest AI prompts and exclusive deals directly by email.',
    subscribe: 'Subscribe',

    // Home Page
    heroBadge: 'Digital Solutions & Artificial Intelligence',
    heroTitle: 'Smart digital tools to power your progress.',
    heroSubtitle: 'Explore optimized prompt packs, SaaS templates, e-books, '
      + 'and custom digital services by Yass Digital Lab.',
    exploreCatalog: 'Explore Catalog',
    requestQuote: 'Request a Quote',
    featuredProducts: 'Featured Products',
    featuredProductsSub: 'A curated selection of top-tier resources to boost your workflow.',
    ourServices: 'Services & Custom Solutions',
    ourServicesSub: 'Tailored digital solutions designed to bring your ambitious projects to life.',
    viewDetails: 'Details →',
    learnMore: 'Learn More →',

    // Products Page
    catalogTitle: 'Digital Products',
    catalogSub: 'High quality digital assets to accelerate your business.',
    searchPlaceholder: 'Search products...',
    sortDefault: 'Default Sorting',
    sortPriceAsc: 'Price: Low to High (▲)',
    sortPriceDesc: 'Price: High to Low (▼)',
    sortNameAsc: 'Name: A to Z',
    all: 'All',
    addToCart: 'Add to Cart',
    liveDemo: 'Live Demo',

    // Services Page
    servicesTitle: 'Custom Services & Solutions',
    servicesSub: 'From web architecture to AI integration, we craft exceptional digital products.',
    requestQuoteBtn: 'ðŸ“ Request a Quote',
    customQuoteModalTitle: 'ðŸ“ Custom Quote Request',
    fullName: 'Full Name',
    email: 'Email Address',
    projectType: 'Project Type',
    projectDetails: 'Project Details',
    sendRequest: 'Send Request →',

    // About Page
    aboutTitle: 'About the Creator',
    aboutSub: 'Passionate about Artificial Intelligence & high-performance Web development.',
    bioTitle: 'Diarrassouba Yassoungo Youssouf',
    bioSub: 'Full Stack Developer & AI Specialist',
    bioText: 'Founder of Yass Digital Lab, I build modern, smart digital products combining robust '
      + 'web architectures (Laravel, Vue.js, PostgreSQL) with state-of-the-art AI models.',

    // Contact Page
    contactTitle: 'Get in Touch',
    contactSub: 'Have a question or a project? Our team gets back to you within 24h.',
    subject: 'Subject',
    message: 'Message',
    sendMessage: 'Send Message →'
  }
};

export const t = (key) => {
  return translations[currentLang.value]?.[key] || key;
};

export const toggleLang = () => {
  currentLang.value = currentLang.value === 'fr' ? 'en' : 'fr';
  localStorage.setItem('lang', currentLang.value);
};

