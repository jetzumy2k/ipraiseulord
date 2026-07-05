<template>
  <div class="public-page">
    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <article v-else-if="page" class="content-card">
      <h1 class="page-title">{{ page.title }}</h1>
      <div class="static-content" v-html="page.content" />
    </article>

    <div v-else class="alert alert-warning">Page not found.</div>
  </div>
</template>

<script>
import { applySeo, buildWebPageJsonLd } from '../../utils/seo';

export default {
  name: 'StaticPageView',
  data() {
    return {
      page: null,
      loading: true,
    };
  },
  computed: {
    slug() {
      return this.$route.meta.slug;
    },
  },
  watch: {
    slug: {
      immediate: true,
      handler(val) {
        if (val) this.loadPage(val);
      },
    },
  },
  methods: {
    async loadPage(slug) {
      this.loading = true;
      try {
        const { data } = await window.axios.get(`/public/pages/${slug}`);
        this.page = data;
        this.updateSeo();
      } catch {
        this.page = null;
      } finally {
        this.loading = false;
      }
    },
    updateSeo() {
      if (!this.page) {
        return;
      }

      const url = `${window.location.origin}${this.$route.path}`;
      const description = this.page.meta_description || '';

      applySeo({
        title: this.page.title,
        description,
        url,
        robots: this.$route.meta.robots,
        jsonLd: buildWebPageJsonLd({
          title: this.page.title,
          description,
          url,
        }),
      });
    },
  },
};
</script>
