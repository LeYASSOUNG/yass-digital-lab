import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  { path: '/',                    name: 'Home',              component: () => import('../views/Home.vue') },
  { path: '/products',            name: 'Products',           component: () => import('../views/Products.vue') },
  { path: '/products/:id',        name: 'ProductDetail',      component: () => import('../views/ProductDetail.vue') },
  { path: '/services',            name: 'Services',           component: () => import('../views/Services.vue') },
  { path: '/courses',             name: 'Courses',            component: () => import('../views/Courses.vue') },
  { path: '/courses/:id',         name: 'CourseDetail',       component: () => import('../views/CourseDetail.vue') },
  { path: '/coupons',             name: 'Coupons',            component: () => import('../views/Coupons.vue') },
  { path: '/about',               name: 'About',              component: () => import('../views/About.vue') },
  { path: '/checkout',            name: 'Checkout',           component: () => import('../views/Checkout.vue') },
  { path: '/login',               name: 'Login',              component: () => import('../views/Login.vue') },
  { path: '/blog',                name: 'Blog',               component: () => import('../views/Blog.vue') },
  { path: '/contact',             name: 'Contact',            component: () => import('../views/Contact.vue') },
  { path: '/order-confirmation',  name: 'OrderConfirmation',  component: () => import('../views/OrderConfirmation.vue') },
  { path: '/forgot-password',     name: 'ForgotPassword',     component: () => import('../views/ForgotPassword.vue') },
  { path: '/verify-email/:id/:hash', name: 'VerifyEmail', component: () => import('../views/VerifyEmail.vue') },
  { path: '/suivi-devis',         name: 'QuoteTracking',      component: () => import('../views/QuoteTracking.vue') },
  { path: '/auth/callback',       name: 'AuthCallback',       component: () => import('../views/AuthCallback.vue') },

  // Routes protégées — nécessitent une session active
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: () => import('../views/AdminLogin.vue')
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: () => import('../views/AdminDashboard.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  { 
    path: '/dashboard',     
    name: 'ClientDashboard', 
    component: () => import('../views/ClientDashboard.vue'), 
    meta: { requiresAuth: true } 
  },
  { 
    path: '/notifications', 
    name: 'Notifications',   
    component: () => import('../views/Notifications.vue'),   
    meta: { requiresAuth: true } 
  },

  { path: '/:pathMatch(.*)*',     name: 'NotFound',           component: () => import('../views/NotFound.vue') }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

// Navigation Guards Vue Router (sans callback next deprecie)
router.beforeEach((to) => {
  const token = localStorage.getItem('token');
  const userStr = localStorage.getItem('user');
  const user = userStr ? JSON.parse(userStr) : null;

  if (to.meta.requiresAdmin) {
    const adminRoles = ['admin', 'super_admin', 'editor', 'creator', 'support'];
    if (!token) {
      return { name: 'AdminLogin', query: { redirect: to.fullPath } };
    }
    if (!user || !adminRoles.includes(user.role)) {
      // Si c'est un client qui essaie d'aller sur l'admin
      return { name: 'ClientDashboard' };
    }
  }

  if (to.meta.requiresAuth && !token) {
    return { name: 'Login', query: { redirect: to.fullPath } };
  }
});

export default router

