import { createRouter, createWebHistory } from 'vue-router'

import Home              from '../views/Home.vue'
import Products          from '../views/Products.vue'
import ProductDetail     from '../views/ProductDetail.vue'
import Services          from '../views/Services.vue'
import About             from '../views/About.vue'
import Checkout          from '../views/Checkout.vue'
import Login             from '../views/Login.vue'
import AdminDashboard    from '../views/AdminDashboard.vue'
import ClientDashboard   from '../views/ClientDashboard.vue'
import Blog              from '../views/Blog.vue'
import Contact           from '../views/Contact.vue'
import Notifications     from '../views/Notifications.vue'
import OrderConfirmation from '../views/OrderConfirmation.vue'
import ForgotPassword    from '../views/ForgotPassword.vue'
import ResetPassword     from '../views/ResetPassword.vue'
import VerifyEmail       from '../views/VerifyEmail.vue'
import NotFound          from '../views/NotFound.vue'

const routes = [
  { path: '/',                    name: 'Home',              component: Home },
  { path: '/products',            name: 'Products',           component: Products },
  { path: '/products/:id',        name: 'ProductDetail',      component: ProductDetail },
  { path: '/services',            name: 'Services',           component: Services },
  { path: '/about',               name: 'About',              component: About },
  { path: '/checkout',            name: 'Checkout',           component: Checkout },
  { path: '/login',               name: 'Login',              component: Login },
  { path: '/blog',                name: 'Blog',               component: Blog },
  { path: '/contact',             name: 'Contact',            component: Contact },
  { path: '/order-confirmation',  name: 'OrderConfirmation',  component: OrderConfirmation },
  { path: '/forgot-password',     name: 'ForgotPassword',     component: ForgotPassword },
  { path: '/reset-password',      name: 'ResetPassword',      component: ResetPassword },
  { path: '/verify-email',        name: 'VerifyEmail',        component: VerifyEmail },

  // Routes protégées — nécessitent une session active
  { path: '/admin',         name: 'AdminDashboard',  component: AdminDashboard,  meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/dashboard',     name: 'ClientDashboard', component: ClientDashboard, meta: { requiresAuth: true } },
  { path: '/notifications', name: 'Notifications',   component: Notifications,   meta: { requiresAuth: true } },

  { path: '/:pathMatch(.*)*',     name: 'NotFound',           component: NotFound }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

// ─── Navigation Guards ────────────────────────────────────────────────────────
router.beforeEach((to, _from, next) => {
  // Lecture du token et de l'utilisateur depuis localStorage
  const token = localStorage.getItem('token');
  const userStr = localStorage.getItem('user');
  const user = userStr ? JSON.parse(userStr) : null;

  if (to.meta.requiresAuth && !token) {
    // Non connecté → rediriger vers /login
    return next({ name: 'Login', query: { redirect: to.fullPath } });
  }

  if (to.meta.requiresAdmin) {
    const adminRoles = ['admin', 'super_admin', 'editor', 'creator', 'support'];
    if (!user || !adminRoles.includes(user.role)) {
      // Connecté mais pas admin → rediriger vers le dashboard client
      return next({ name: 'ClientDashboard' });
    }
  }

  next();
});

export default router
