import axios from 'axios';

window.axios = axios;

// Configure le header standard pour identifier les requêtes XHR côté serveur Laravel
const axiosDefaults = window.axios.defaults;
axiosDefaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
