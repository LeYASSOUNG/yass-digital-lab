/**
 * ============================================================
 * Point d'entrée de l'Application — main.js
 * Yass Digital Lab — Frontend Vue 3
 * ============================================================
 * Initialise l'application Vue 3 et configure tous les plugins globaux :
 *
 *   1. Pinia — Gestionnaire d'état global (stores : auth, cart, notifications)
 *   2. Vue Router — Navigation entre les pages de la SPA
 *   3. style.css — Styles globaux (variables CSS, typographie, utilitaires)
 *
 * L'application est montée sur l'élément HTML #app défini dans index.html.
 * ============================================================
 */

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import { createI18n } from 'vue-i18n'
import { createHead } from '@vueuse/head'
import './style.css'          // Styles globaux (design system, variables CSS)
import App from './App.vue'   // Composant racine de l'application

import fr from './locales/fr.json'
import en from './locales/en.json'

// Configuration i18n
const i18n = createI18n({
  legacy: false, // Utiliser Composition API
  locale: localStorage.getItem('locale') || 'fr',
  fallbackLocale: 'fr',
  messages: {
    fr,
    en
  }
})

// Configuration SEO Head
const head = createHead()

// Création de l'instance Vue 3
const app = createApp(App)

// Enregistrement de Pinia (gestionnaire d'état global)
// Doit être enregistré avant le routeur pour que les stores soient disponibles
app.use(createPinia())

// Enregistrement de i18n et Head
app.use(i18n)
app.use(head)

// Enregistrement du routeur Vue Router 4
app.use(router)

// Montage de l'application dans l'élément HTML avec id="app" (défini dans index.html)
app.mount('#app')

