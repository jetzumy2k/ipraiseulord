<template>
  <div class="public-page">
    <h1 class="page-title">Novenas</h1>
    <p class="text-muted">Nine days of prayer and devotion.</p>

    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <div v-else class="row g-3">
      <div v-for="novena in novenas" :key="novena.id" class="col-md-6">
        <router-link :to="`/novenas/${novena.slug}`" class="content-card list-card d-block">
          <h3>{{ novena.title }}</h3>
          <p v-if="novena.patron_saint" class="text-muted mb-1">{{ novena.patron_saint }}</p>
          <p class="mb-0">{{ excerpt(novena.description) }}</p>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NovenasPage',
  data() {
    return {
      novenas: [],
      loading: true,
    };
  },
  mounted() {
    window.axios.get('/public/novenas')
      .then(({ data }) => { this.novenas = data; })
      .finally(() => { this.loading = false; });
  },
  methods: {
    excerpt(text) {
      if (!text) return '';
      return text.length > 120 ? `${text.slice(0, 120)}…` : text;
    },
  },
};
</script>
