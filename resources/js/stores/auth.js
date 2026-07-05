import { defineStore } from 'pinia';
import axios from '../bootstrap';

const TOKEN_KEY = 'auth_token';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem(TOKEN_KEY) || null,
        loading: false,
        initialized: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token && !!state.user,
        isSuperAdmin: (state) => state.user?.role === 'super_admin',
    },

    actions: {
        setToken(token) {
            this.token = token;
            if (token) {
                localStorage.setItem(TOKEN_KEY, token);
                axios.defaults.headers.common.Authorization = `Bearer ${token}`;
            } else {
                localStorage.removeItem(TOKEN_KEY);
                delete axios.defaults.headers.common.Authorization;
            }
        },

        async initialize() {
            if (!this.token) {
                this.initialized = true;
                return;
            }

            axios.defaults.headers.common.Authorization = `Bearer ${this.token}`;

            try {
                const { data } = await axios.get('/auth/me');
                this.user = data;
            } catch {
                this.setToken(null);
                this.user = null;
            } finally {
                this.initialized = true;
            }
        },

        async login(credentials) {
            this.loading = true;
            try {
                const { data } = await axios.post('/auth/login', credentials);
                this.setToken(data.token);
                this.user = data.user;
                return data;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                if (this.token) {
                    await axios.post('/auth/logout');
                }
            } catch {
                // ignore logout errors
            } finally {
                this.user = null;
                this.setToken(null);
            }
        },
    },
});
