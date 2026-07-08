<template>
  <div class="sidebar-card sidebar-verse-card">
    <h3 class="sidebar-title">{{ title }}</h3>
    <blockquote v-if="verse" class="sidebar-verse-quote">
      <p>{{ verse.text }}</p>
      <footer>
        {{ verse.reference }}
        <span v-if="verse.version"> · {{ verse.version }}</span>
      </footer>
    </blockquote>
    <p v-else-if="loading" class="text-muted small mb-0">
      <i class="fas fa-spinner fa-spin me-1" />Loading...
    </p>
    <p v-else class="text-muted small mb-0">No verses available yet.</p>
    <SocialShareBar
      v-if="verse"
      class="sidebar-share-bar"
      :title="shareTitle"
      :text="shareText"
      :url="shareUrl"
      compact
    />
  </div>
</template>

<script>
import SocialShareBar from './SocialShareBar.vue';
import { buildSidebarQuoteShareText } from '../../utils/socialShare';
const REFRESH_MS = 5 * 60 * 1000;

export default {
  name: 'SidebarVerseWidget',
  components: { SocialShareBar },
  props: {
    title: { type: String, required: true },
    scope: { type: String, required: true },
  },
  data() {
    return {
      verse: null,
      loading: true,
      refreshTimer: null,
    };
  },
  computed: {
    sharePayload() {
      if (!this.verse) {
        return { title: '', text: '' };
      }

      return buildSidebarQuoteShareText({
        title: this.title,
        text: this.verse.text,
        reference: this.verse.reference,
        version: this.verse.version,
      });
    },
    shareTitle() {
      return this.sharePayload.title;
    },
    shareText() {
      return this.sharePayload.text;
    },
    shareUrl() {
      return window.location.href;
    },
  },
  mounted() {
    this.fetchVerse();
    this.refreshTimer = setInterval(this.fetchVerse, REFRESH_MS);
  },
  beforeUnmount() {
    if (this.refreshTimer) {
      clearInterval(this.refreshTimer);
    }
  },
  methods: {
    async fetchVerse() {
      const isInitialLoad = !this.verse;
      if (isInitialLoad) {
        this.loading = true;
      }

      try {
        const { data } = await window.axios.get(`/public/bible/verse/random/${this.scope}`);
        this.verse = data;
      } catch {
        if (isInitialLoad) {
          this.verse = null;
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
