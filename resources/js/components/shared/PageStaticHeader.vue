<template>
  <header v-if="!loading" class="page-static-header" :class="{ 'page-static-header--compact': compact }">
    <h1 class="page-title" :class="titleClass">{{ displayTitle }}</h1>
    <div
      v-if="displayContent"
      class="static-content page-intro"
      :class="introClass"
      v-html="displayContent"
    />
    <p v-else-if="defaultIntro" class="page-intro-fallback" :class="introClass">{{ defaultIntro }}</p>
  </header>
</template>

<script>
import { applySeo } from '../../utils/seo';

const contentCache = new Map();

export default {
  name: 'PageStaticHeader',
  props: {
    routeName: {
      type: String,
      default: null,
    },
    defaultTitle: {
      type: String,
      required: true,
    },
    defaultIntro: {
      type: String,
      default: '',
    },
    defaultContent: {
      type: String,
      default: '',
    },
    titleClass: {
      type: String,
      default: '',
    },
    introClass: {
      type: String,
      default: '',
    },
    compact: {
      type: Boolean,
      default: false,
    },
    updateSeo: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['loaded'],
  data() {
    return {
      page: null,
      loading: true,
    };
  },
  computed: {
    resolvedRouteName() {
      return this.routeName || this.$route.name;
    },
    displayTitle() {
      return this.page?.title || this.defaultTitle;
    },
    displayContent() {
      return this.page?.content || this.defaultContent || '';
    },
  },
  watch: {
    resolvedRouteName: {
      immediate: true,
      handler() {
        this.loadContent();
      },
    },
  },
  methods: {
    async loadContent() {
      const routeName = this.resolvedRouteName;
      if (!routeName) {
        this.loading = false;
        return;
      }

      if (contentCache.has(routeName)) {
        this.page = contentCache.get(routeName);
        this.loading = false;
        this.emitLoaded();
        return;
      }

      this.loading = true;

      try {
        const { data } = await window.axios.get(`/public/pages/route/${routeName}`);
        contentCache.set(routeName, data);
        this.page = data;
        this.emitLoaded();
      } catch {
        this.page = null;
        this.emitLoaded();
      } finally {
        this.loading = false;
      }
    },
    emitLoaded() {
      if (this.updateSeo && this.page) {
        applySeo({
          title: this.page.title,
          description: this.page.meta_description || this.$route.meta?.description || '',
          url: `${window.location.origin}${this.$route.path}`,
          robots: this.$route.meta?.robots,
        });
      }

      this.$emit('loaded', {
        page: this.page,
        title: this.displayTitle,
        content: this.displayContent,
      });
    },
  },
};
</script>
