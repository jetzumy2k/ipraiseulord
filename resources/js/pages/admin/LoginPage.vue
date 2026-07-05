<template>
  <div class="login-page d-flex align-items-center justify-content-center min-vh-100">
    <div class="card login-card shadow">
      <div class="card-body p-4">
        <h1 class="h4 text-center mb-4">Admin Login</h1>

        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <form @submit.prevent="submit">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" v-model="form.email" type="email" class="form-control" required autofocus>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" v-model="form.password" type="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100" :disabled="auth.loading">
            <i v-if="auth.loading" class="fas fa-spinner fa-spin me-1" />
            Sign In
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '../../stores/auth';

export default {
  name: 'LoginPage',
  data() {
    return {
      form: { email: '', password: '' },
      error: null,
    };
  },
  computed: {
    auth() {
      return useAuthStore();
    },
  },
  methods: {
    async submit() {
      this.error = null;
      try {
        await this.auth.login(this.form);
        const redirect = this.$route.query.redirect || '/admin/dashboard';
        this.$router.push(redirect);
      } catch (err) {
        if (err.response?.data?.errors?.email) {
          this.error = err.response.data.errors.email[0];
        } else {
          this.error = 'Invalid credentials.';
        }
      }
    },
  },
};
</script>

<style scoped>
.login-page {
  background: #f4f6f9;
}
.login-card {
  width: 100%;
  max-width: 400px;
}
</style>
