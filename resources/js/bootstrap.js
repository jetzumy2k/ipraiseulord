import axios from 'axios';
import $ from 'jquery';
import * as bootstrap from 'bootstrap';
import 'admin-lte/dist/js/adminlte.min.js';

window.$ = window.jQuery = $;
window.bootstrap = bootstrap;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = '/api';
window.axios.defaults.withCredentials = true;

const token = localStorage.getItem('auth_token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export default axios;
