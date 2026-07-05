<template>
  <div class="public-page">
    <nav class="breadcrumb-nav mb-3">
      <router-link to="/prayers">Prayers</router-link>
    </nav>

    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <article v-else-if="prayer" class="content-card">
      <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
        <div>
          <h1 class="page-title">{{ prayer.title }}</h1>
          <span v-if="prayer.category" class="badge bg-secondary mb-3">{{ prayer.category }}</span>
          <p v-if="prayer.description" class="text-muted">{{ prayer.description }}</p>
        </div>
        <SocialShareBar
          :title="shareTitle"
          :text="shareText"
          compact
        />
      </div>
      <div class="prayer-content" v-html="prayer.content" />
    </article>
  </div>
</template>

<script>
import SocialShareBar from '../../components/shared/SocialShareBar.vue';
import { applySeo, buildArticleJsonLd } from '../../utils/seo';

export default {
  name: 'PrayerDetailPage',
  components: { SocialShareBar },
  data() {
    return {
      prayer: null,
      loading: true,
    };
  },
  computed: {
    shareTitle() {
      return this.prayer?.title || 'Prayer';
    },
    shareText() {
      const excerpt = (this.prayer?.description || this.stripHtml(this.prayer?.content || '')).slice(0, 180);
      return excerpt ? `${excerpt}…` : `Read ${this.shareTitle} on Praise U Lord.`;
    },
  },
  watch: {
    '$route.params.slug': {
      immediate: true,
      handler(slug) {
        this.loadPrayer(slug);
      },
    },
  },
  methods: {
    async loadPrayer(slug) {
      this.loading = true;
      try {
        const { data } = await window.axios.get(`/public/prayers/${slug}`);
        this.prayer = data;
        this.updateSeo();
      } finally {
        this.loading = false;
      }
    },
    updateSeo() {
      if (!this.prayer) {
        return;
      }

      const url = `${window.location.origin}/prayers/${this.prayer.slug}`;
      const description = this.prayer.description
        || this.stripHtml(this.prayer.content).slice(0, 160)
        || `Read ${this.prayer.title} on Praise U Lord.`;

      applySeo({
        title: this.prayer.title,
        description,
        url,
        type: 'article',
        jsonLd: buildArticleJsonLd({
          title: this.prayer.title,
          description,
          url,
        }),
      });
    },
    stripHtml(html) {
      const doc = new DOMParser().parseFromString(html, 'text/html');
      return doc.body.textContent?.replace(/\s+/g, ' ').trim() || '';
    },
  },
};
</script>
