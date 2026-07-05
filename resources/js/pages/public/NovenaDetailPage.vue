<template>
  <div class="public-page">
    <nav class="breadcrumb-nav mb-3">
      <router-link to="/novenas">Novenas</router-link>
    </nav>

    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <article v-else-if="novena" class="content-card novena-guide">
      <header class="mb-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
          <div>
            <h1 class="page-title mb-1">{{ novena.title }}</h1>
            <p v-if="novena.patron_saint" class="text-muted mb-2">{{ novena.patron_saint }}</p>
            <p v-if="novena.description" class="mb-0">{{ novena.description }}</p>
          </div>
          <SocialShareBar
            :title="shareTitle"
            :text="shareText"
            compact
          />
        </div>
      </header>

      <nav v-if="sections.length" class="mass-guide-toc mb-4" aria-label="Novena sections">
        <a
          v-for="section in sections"
          :key="section.id"
          :href="`#novena-${section.id}`"
          class="mass-guide-toc-link"
        >
          {{ sectionNavLabel(section) }}
        </a>
      </nav>

      <div v-if="daySections.length" class="novena-day-nav mb-4">
        <span class="novena-day-nav-label">Jump to day</span>
        <button
          v-for="section in daySections"
          :key="section.id"
          type="button"
          class="btn btn-sm"
          :class="activeSectionId === section.id ? 'btn-primary' : 'btn-outline-primary'"
          @click="scrollToSection(section.id)"
        >
          {{ section.title.replace('Day ', '') }}
        </button>
      </div>

      <section
        v-for="section in sections"
        :id="`novena-${section.id}`"
        :key="section.id"
        class="mass-section"
      >
        <h2 class="mass-section-title">
          {{ section.title }}
          <span v-if="section.subtitle" class="novena-section-subtitle"> — {{ section.subtitle }}</span>
        </h2>

        <div
          v-for="(step, stepIndex) in section.steps"
          :key="`${section.id}-${stepIndex}`"
          class="mass-step"
        >
          <h3 class="mass-step-title">{{ step.title }}</h3>

          <div
            v-for="(part, partIndex) in step.parts"
            :key="`${section.id}-${stepIndex}-${partIndex}`"
            class="mass-part"
            :class="partClass(part.role)"
          >
            <span class="mass-part-label">{{ part.label }}</span>
            <p class="mass-part-text">{{ part.text }}</p>
          </div>
        </div>
      </section>
    </article>
  </div>
</template>

<script>
import SocialShareBar from '../../components/shared/SocialShareBar.vue';
import { applySeo, buildArticleJsonLd } from '../../utils/seo';

export default {
  name: 'NovenaDetailPage',
  components: { SocialShareBar },
  data() {
    return {
      novena: null,
      loading: true,
      activeSectionId: 'opening',
    };
  },
  computed: {
    sections() {
      return this.novena?.guide?.sections ?? [];
    },
    daySections() {
      return this.sections.filter((section) => section.id.startsWith('day-'));
    },
    shareTitle() {
      return this.novena?.title ? `${this.novena.title} Novena` : 'Novena';
    },
    shareText() {
      const patron = this.novena?.patron_saint ? ` (${this.novena.patron_saint})` : '';
      return `Join this nine-day novena${patron} with leader and people responses on Praise U Lord.`;
    },
  },
  watch: {
    '$route.params.slug': {
      immediate: true,
      handler(slug) {
        this.loadNovena(slug);
      },
    },
    '$route.hash'() {
      this.syncSectionFromHash();
    },
  },
  mounted() {
    window.addEventListener('scroll', this.updateActiveSection, { passive: true });
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.updateActiveSection);
  },
  methods: {
    async loadNovena(slug) {
      this.loading = true;

      try {
        const { data } = await window.axios.get(`/public/novenas/${slug}`);
        this.novena = data;
        this.updateSeo();
        this.syncSectionFromHash();
        this.$nextTick(() => this.scrollToHash());
      } finally {
        this.loading = false;
      }
    },
    updateSeo() {
      if (!this.novena) {
        return;
      }

      const url = `${window.location.origin}/novenas/${this.novena.slug}`;
      const description = this.novena.description
        || `Pray the ${this.novena.title} novena with daily prayers and responses on Praise U Lord.`;

      applySeo({
        title: this.novena.title,
        description,
        url,
        type: 'article',
        jsonLd: buildArticleJsonLd({
          title: this.novena.title,
          description,
          url,
        }),
      });
    },
    sectionNavLabel(section) {
      if (section.id.startsWith('day-')) {
        return section.title;
      }

      return section.title;
    },
    partClass(role) {
      const mappedRole = {
        leader: 'priest',
        people: 'people',
        all: 'all',
        note: 'note',
      }[role] || role;

      return `mass-part--${mappedRole}`;
    },
    scrollToSection(sectionId) {
      this.activeSectionId = sectionId;
      this.$router.replace({ hash: `#novena-${sectionId}` }).catch(() => {});
      this.$nextTick(() => {
        document.getElementById(`novena-${sectionId}`)?.scrollIntoView({
          behavior: 'smooth',
          block: 'start',
        });
      });
    },
    syncSectionFromHash() {
      const match = this.$route.hash.match(/^#novena-(.+)$/);
      if (match) {
        this.activeSectionId = match[1];
      }
    },
    scrollToHash() {
      if (!this.$route.hash) {
        return;
      }

      const element = document.querySelector(this.$route.hash);
      element?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    },
    updateActiveSection() {
      const offset = 120;
      let current = this.sections[0]?.id;

      for (const section of this.sections) {
        const element = document.getElementById(`novena-${section.id}`);
        if (element && element.getBoundingClientRect().top <= offset) {
          current = section.id;
        }
      }

      if (current) {
        this.activeSectionId = current;
      }
    },
  },
};
</script>
