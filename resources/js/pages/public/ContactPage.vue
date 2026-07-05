<template>
  <div class="public-page">
    <PageStaticHeader
      default-title="Contact Us"
      default-intro="We would love to hear from you."
      update-seo
    />

    <form class="content-card" @submit.prevent="submit">
      <div v-if="success" class="alert alert-success">{{ success }}</div>
      <div v-if="error" class="alert alert-danger">{{ error }}</div>

      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input id="name" v-model="form.name" type="text" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" v-model="form.email" type="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="subject" class="form-label">Subject</label>
        <input id="subject" v-model="form.subject" type="text" class="form-control">
      </div>
      <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea id="message" v-model="form.message" class="form-control" rows="5" required />
      </div>
      <button type="submit" class="btn btn-primary" :disabled="loading">
        <i v-if="loading" class="fas fa-spinner fa-spin me-1" />
        Send Message
      </button>
    </form>
  </div>
</template>

<script>
import PageStaticHeader from '../../components/shared/PageStaticHeader.vue';

export default {
  name: 'ContactPage',
  components: { PageStaticHeader },
  data() {
    return {
      form: { name: '', email: '', subject: '', message: '' },
      loading: false,
      success: null,
      error: null,
    };
  },
  methods: {
    async submit() {
      this.loading = true;
      this.success = null;
      this.error = null;
      try {
        await window.axios.post('/public/contact', this.form);
        this.success = 'Thank you! Your message has been sent.';
        this.form = { name: '', email: '', subject: '', message: '' };
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to send message.';
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
