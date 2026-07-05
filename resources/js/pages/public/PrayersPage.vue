<template>
  <div class="public-page">
    <h1 class="page-title">Prayers</h1>
    <p class="text-muted">Traditional and daily prayers for every occasion.</p>

    <div class="mb-3">
      <select v-model="category" class="form-select form-select-sm" style="max-width: 220px;">
        <option value="">All categories</option>
        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
      </select>
    </div>

    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <div v-else class="row g-3">
      <div v-for="prayer in filteredPrayers" :key="prayer.id" class="col-md-6">
        <router-link :to="`/prayers/${prayer.slug}`" class="content-card list-card d-block">
          <h3>{{ prayer.title }}</h3>
          <span v-if="prayer.category" class="badge bg-secondary mb-2">{{ prayer.category }}</span>
          <p class="mb-0">{{ excerpt(prayer.description) }}</p>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PrayersPage',
  data() {
    return {
      prayers: [],
      loading: true,
      category: '',
    };
  },
  computed: {
    categories() {
      return [...new Set(this.prayers.map((p) => p.category).filter(Boolean))];
    },
    filteredPrayers() {
      if (!this.category) return this.prayers;
      return this.prayers.filter((p) => p.category === this.category);
    },
  },
  mounted() {
    window.axios.get('/public/prayers')
      .then(({ data }) => { this.prayers = data; })
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
